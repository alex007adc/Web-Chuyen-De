<?php
// Kết nối database
require '../config.php';

// Xử lý form khi nhấn submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $masp = $_POST['masp'];
    $tensp = $_POST['tensp'];
    $mota = $_POST['mota'];
    $soluong = $_POST['soluong'];
    $gia = $_POST['gia'];
    $danhmuc_id = $_POST['danhmuc'];
    $khuyenmai = isset($_POST['khuyenmai']) ? (int)$_POST['khuyenmai'] : 0;

    // Kiểm tra khuyến mãi có hợp lệ không (không cần vì đã có lọc trong dropdown)

    // Xử lý upload ảnh
    if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] == 0) {
        $imageData = file_get_contents($_FILES['hinhanh']['tmp_name']); // Đọc dữ liệu ảnh
    } else {
        $imageData = NULL;
    }
    
    // Chèn vào database
    $sql = "INSERT INTO sanpham (masp, tensp, mota, soluong, gia, tendanhmuc, khuyenmai, hinhanh) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiisis", $masp, $tensp, $mota, $soluong, $gia, $danhmuc_id, $khuyenmai, $imageData);
    $stmt->send_long_data(7, $imageData); // Gửi dữ liệu ảnh

    if ($stmt->execute()) {
        echo "<script> window.location='sanpham.php';</script>";
    } else {
        echo "<script>alert('Lỗi thêm sản phẩm: " . $conn->error . "');</script>";
    }
    
    // Xử lý upload ảnh
    if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] == 0) {
        $imageData = file_get_contents($_FILES['hinhanh']['tmp_name']); // Đọc dữ liệu ảnh
    } else {
        $imageData = NULL;
    }
    
    // Chèn vào database
    $sql = "INSERT INTO sanpham (masp, tensp, mota, soluong, gia, tendanhmuc, khuyenmai, hinhanh) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiisis", $masp, $tensp, $mota, $soluong, $gia, $danhmuc_id, $khuyenmai, $imageData);
    $stmt->send_long_data(7, $imageData); // Gửi dữ liệu ảnh
    




    if ($stmt->execute()) {
        echo "<script> window.location='sanpham.php';</script>";
    } else {
        echo "<script>alert('Lỗi thêm sản phẩm: " . $conn->error . "');</script>";
    }
}

// Lấy danh sách danh mục từ database
$danhmuc_sql = "SELECT * FROM danhmuc";
$danhmuc_result = $conn->query($danhmuc_sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm</title>
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
        <!-- Dropdown Khuyến mãi -->
        <div class="dropdown">
            <button class="dropdown-btn">Khuyến mãi</button>
            <div class="dropdown-content">
                <a href="../khuyenmai/khuyenmai.php">Danh sách khuyến mãi</a>
                <a href="../khuyenmai/themkhuyenmai.php">Thêm khuyến mãi</a>
            </div>
        </div>
        <a href="../nguoidung/nguoidung.php">Quản lý Người dùng</a>
        <a href="../donhang/donhang.php">Quản lý Đơn hàng</a>
        <a href="../danhmuc/danhmuc.php">Quản lý Danh mục</a>
    </div>

    <!-- Nội dung chính -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <span class="navbar-brand">Dashboard Admin</span>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="../logout.php">Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="container mt-4">
        <h2>Thêm sản phẩm</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Mã SP</label>
                <input type="text" name="masp" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Hình ảnh</label>
                <input type="file" name="hinhanh" class="form-control">
            </div>
            <div class="mb-3">
                <label>Tên sản phẩm</label>
                <input type="text" name="tensp" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Mô tả</label>
                <textarea name="mota" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label>Số lượng</label>
                <input type="number" name="soluong" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Giá</label>
                <input type="number" name="gia" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Danh mục</label>
                <select name="danhmuc" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php while ($row = $danhmuc_result->fetch_assoc()): ?>
                        <option value="<?= $row['id']; ?>"><?= $row['tendanhmuc']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Khuyến mãi</label>
                <select name="khuyenmai" class="form-control" required>
                    <option value="">-- Chọn khuyến mãi --</option>
                    <?php
                    // Lấy danh sách khuyến mãi từ cơ sở dữ liệu
                    $khuyenmai_sql = "SELECT * FROM khuyenmai WHERE trangthai = 'active'"; // Lọc khuyến mãi đang hoạt động
                    $khuyenmai_result = $conn->query($khuyenmai_sql);
                    
                    while ($row_km = $khuyenmai_result->fetch_assoc()):
                    ?>
                        <option value="<?= $row_km['id']; ?>"><?= $row_km['tenkhuyenmai']; ?> - <?= $row_km['phantramgiam']; ?>%</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
        </form>
    </div>
</body>
</html>