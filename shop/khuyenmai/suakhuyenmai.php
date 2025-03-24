<?php
include('../config.php'); // Kết nối với cơ sở dữ liệu

// Kiểm tra nếu có tham số id trên URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Truy vấn lấy thông tin khuyến mãi hiện tại
    $stmt = $conn->prepare("SELECT * FROM khuyenmai WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $khuyenmai = $result->fetch_assoc();

    if (!$khuyenmai) {
        die("Không tìm thấy khuyến mãi.");
    }
} else {
    die("ID khuyến mãi không hợp lệ.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $tenkhuyenmai = $_POST['tenkhuyenmai'];
    $mota = $_POST['mota'];
    $ngaybatdau = $_POST['ngaybatdau'];
    $ngayketthuc = $_POST['ngayketthuc'];
    $phantramgiam = $_POST['phantramgiam'];
    $trangthai = $_POST['trangthai'];

    // Cập nhật khuyến mãi trong CSDL
    $stmt = $conn->prepare("UPDATE khuyenmai SET tenkhuyenmai = ?, mota = ?, ngaybatdau = ?, ngayketthuc = ?, phantramgiam = ?, trangthai = ? WHERE id = ?");
    $stmt->bind_param("ssssisi", $tenkhuyenmai, $mota, $ngaybatdau, $ngayketthuc, $phantramgiam, $trangthai, $id);

    if ($stmt->execute()) {
        echo "Cập nhật khuyến mãi thành công!";
        header("Location: khuyenmai.php"); // Quay lại danh sách khuyến mãi
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Khuyến Mãi</title>
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
        <a href="../khachhang/khachhang.php">Quản Lý Khách Hàng</a>
        <a href="../donhang/donhang.php">Quản Lý Đơn Hàng</a>
        <a href="../danhmuc/danhmuc.php">Quản Lý Danh Mục</a>
        <a href="../binhluan/quanlybinhluan.php">Quản lý Bình Luận</a>
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
        <div class="container mt-4">
            <h2>Sửa Khuyến Mãi</h2>
            <form method="POST">
                <div class="mb-3">
                    <label>Tên khuyến mãi</label>
                    <input type="text" name="tenkhuyenmai" class="form-control" value="<?php echo htmlspecialchars($khuyenmai['tenkhuyenmai']); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Mô tả</label>
                    <textarea name="mota" class="form-control" required><?php echo htmlspecialchars($khuyenmai['mota']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label>Ngày bắt đầu</label>
                    <input type="date" name="ngaybatdau" class="form-control" value="<?php echo $khuyenmai['ngaybatdau']; ?>" required>
                </div>
                <div class="mb-3">
                    <label>Ngày kết thúc</label>
                    <input type="date" name="ngayketthuc" class="form-control" value="<?php echo $khuyenmai['ngayketthuc']; ?>" required>
                </div>
                <div class="mb-3">
                    <label>Phần trăm giảm (%)</label>
                    <input type="number" class="form-control" name="phantramgiam" value="<?php echo $khuyenmai['phantramgiam']; ?>" min="0" max="100" required>
                </div>
                <div class="mb-3">
                    <label>Trạng thái</label>
                    <select name="trangthai" class="form-control">
                        <option value="active" <?php echo ($khuyenmai['trangthai'] == 'active') ? 'selected' : ''; ?>>Đã duyệt</option>
                        <option value="inactive" <?php echo ($khuyenmai['trangthai'] == 'inactive') ? 'selected' : ''; ?>>Chưa duyệt</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </form>
        </div>
    </div>
</body>
</html>
