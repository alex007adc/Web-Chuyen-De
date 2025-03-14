<?php
include('../config.php'); // Kết nối với cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $tenkhuyenmai = $_POST['tenkhuyenmai'];
    $mota = $_POST['mota'];
    $ngaybatdau = $_POST['ngaybatdau'];
    $ngayketthuc = $_POST['ngayketthuc'];
    $phantramgiam = $_POST['phantramgiam'];
    $trangthai = $_POST['trangthai']; // Lấy giá trị trạng thái từ form
    
    // Sử dụng Prepared Statements để bảo vệ khỏi SQL Injection
    // Câu lệnh SQL thêm khuyến mãi
    $stmt = $conn->prepare("INSERT INTO khuyenmai (tenkhuyenmai, mota, ngaybatdau, ngayketthuc, phantramgiam, trangthai) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $tenkhuyenmai, $mota, $ngaybatdau, $ngayketthuc, $phantramgiam, $trangthai);
        
    if ($stmt->execute()) {
        echo "Khuyến mãi đã được thêm thành công!";
        header("Location: khuyenmai.php"); // Quay lại trang danh sách khuyến mãi
        exit(); // Thêm exit() để ngừng script sau khi redirect
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
    <title>Thêm Khuyến Mãi</title>
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
        <div class="container mt-4">
            <h2>Thêm Khuyến Mãi</h2>
            <form method="POST">
                <div class="mb-3">
                    <label>Tên khuyến mãi</label>
                    <input type="text" name="tenkhuyenmai" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Mô tả</label>
                    <textarea name="mota" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Ngày bắt đầu</label>
                    <input type="date" name="ngaybatdau" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Ngày kết thúc</label>
                    <input type="date" name="ngayketthuc" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phần trăm giảm (%)</label>
                    <input type="number" class="form-control" name="phantramgiam" min="0" max="100" required>
                </div>
                <div class="mb-3">
                    <label>Trạng thái</label>
                    <select name="trangthai" class="form-control">
                        <option value="active">Đã duyệt</option>
                        <option value="inactive">Chưa duyệt</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Thêm Khuyến Mãi</button>
            </form>
        </div>
    </div>
</body>
</html>
