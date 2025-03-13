<?php
require '../config.php'; // Kết nối đến database

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Không tìm thấy sản phẩm!");
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM sanpham WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Lấy danh sách danh mục
$danhmuc_sql = "SELECT id, tendanhmuc FROM danhmuc";
$danhmuc_result = $conn->query($danhmuc_sql);


if ($result->num_rows == 0) {
    die("Sản phẩm không tồn tại!");
}

$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $masp = $_POST['masp'];
    $tensp = $_POST['tensp'];
    $mota = $_POST['mota'];
    $soluong = $_POST['soluong'];
    $gia = $_POST['gia'];
    $danhmuc_id = $_POST['danhmuc']; // Lấy ID danh mục từ form
    $khuyenmai = isset($_POST['khuyenmai']) ? (int)$_POST['khuyenmai'] : 0;

    // Giới hạn từ 0 - 100%
    if ($khuyenmai < 0 || $khuyenmai > 100) {
        die("Khuyến mãi phải từ 0% đến 100%!");
    }


    // Xử lý hình ảnh
    if (!empty($_FILES['hinhanh']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png'];
        if (!in_array($_FILES['hinhanh']['type'], $allowed_types)) {
            die("Chỉ chấp nhận ảnh định dạng JPG hoặc PNG!");
        }
        $hinhanh = file_get_contents($_FILES['hinhanh']['tmp_name']);

        $sql = "UPDATE sanpham SET masp=?, hinhanh=?, tensp=?, mota=?, soluong=?, gia=?, tendanhmuc=?, khuyenmai=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sbssiidii", $masp, $null, $tensp, $mota, $soluong, $gia, $danhmuc_id, $khuyenmai, $id);
        $stmt->send_long_data(1, $hinhanh); // Gửi dữ liệu ảnh vào cột BLOB


    } else {
        $sql = "UPDATE sanpham SET masp=?, tensp=?, mota=?, soluong=?, gia=?, tendanhmuc=?, khuyenmai=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiisii", $masp, $tensp, $mota, $soluong, $gia, $danhmuc_id, $khuyenmai, $id);

    }

    if ($stmt->execute()) {
        echo "<script> window.location='sanpham.php';</script>";
    } else {
        echo "Lỗi SQL: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/admin.css">
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar">
        <a href="../admin/admin.php">Trang quan tri</a>
        <a href="../shop/index.php">Website</a>
        <!-- Dropdown Sản phẩm -->
        <div class="dropdown">
            <button class="dropdown-btn">Quản lý Sản phẩm</button>
            <div class="dropdown-content">
                <a href="../sanpham/sanpham.php">Danh sách Sản phẩm</a>
                <a href="../sanpham/themsanpham.php">Thêm Sản phẩm</a>
            </div>
        </div>

        <a href="../khuyenmai/khuyenmai.php">Quản lý Khuyến mãi</a>
        <a href="../nguoidung/nguoidung.php">Quản lý Người dùng</a>
        <a href="../donhang/donhang.php">Quản lý Đơn hàng</a>
        <a href="../danhmuc/danhmuc.php">Quản lý Danh mục</a>
    </div>

    <!-- Nội dung chính -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <span class="navbar-brand">Dashboard Admin</span>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Đăng xuất</a></li>
                </ul>
            </div>
        </nav>

    <div class="container mt-4">
        <h2>Sửa Thông Tin Sản Phẩm</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Mã sản phẩm</label>
                <input type="text" class="form-control" name="masp" value="<?= htmlspecialchars($row['masp']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Hình ảnh</label>
                <input type="file" class="form-control" name="hinhanh">
                <br>
                <?php if (!empty($row['hinhanh'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($row['hinhanh']); ?>" width="100">
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Tên sản phẩm</label>
                <input type="text" class="form-control" name="tensp" value="<?= htmlspecialchars($row['tensp']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea class="form-control" name="mota" required><?= htmlspecialchars($row['mota']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Số lượng</label>
                <input type="number" class="form-control" name="soluong" value="<?= $row['soluong']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Giá mới</label>
                <input type="number" class="form-control" name="gia" value="<?= $row['gia']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Danh mục</label>
                <select class="form-select" name="danhmuc">
                    <?php while ($dm = $danhmuc_result->fetch_assoc()): ?>
                        <option value="<?= $dm['id']; ?>" <?= ($dm['id'] == $row['tendanhmuc']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dm['tendanhmuc']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Khuyến mãi (%)</label>
                <input type="number" class="form-control" name="khuyenmai" value="<?= isset($row['khuyenmai']) ? $row['khuyenmai'] : ''; ?>" min="0" max="100" required>
            </div>


            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            <a href="sanpham.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</body>
</html>
