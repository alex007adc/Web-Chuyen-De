<?php
session_start();
require '../config.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Truy vấn để lấy danh sách khuyến mãi
$sql = "SELECT * FROM khuyenmai ORDER BY ngaybatdau DESC";
$result = $conn->query($sql);

// Kiểm tra nếu có kết quả
if ($result->num_rows > 0) {
    // Dữ liệu có sẵn
} else {
    echo "Không có dữ liệu khuyến mãi.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Khuyến Mãi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/admin.css">
</head>
<body>
<!-- Sidebar -->
    <div id="sidebar">
        <a href="../admin/admin.php">Trang quản trị</a>
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
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Đăng xuất</a></li>
                </ul>
            </div>
        </nav>

        <div class="container mt-4">
            <h2>Quản lý Khuyến Mãi</h2>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên Khuyến Mãi</th>
                        <th>Mô Tả</th>
                        <th>Ngày Bắt Đầu</th>
                        <th>Ngày Kết Thúc</th>
                        <th>Phần Trăm Giảm</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['tenkhuyenmai']); ?></td>
                        <td><?= !empty($row['mota']) ? nl2br(htmlspecialchars($row['mota'])) : "(Không có mô tả)"; ?></td>
                        <td><?= $row['ngaybatdau']; ?></td>
                        <td><?= $row['ngayketthuc']; ?></td>
                        <td><?= $row['phantramgiam'] . ' %'; ?></td>
                        <td>
                            <?= ($row['trangthai'] == 'active') ? '<span class="badge bg-success">Hoạt động</span>' : '<span class="badge bg-danger">Không hoạt động</span>'; ?>
                        </td>
                        <td>
                            <a href="suakhuyenmai.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">Sửa</a>
                            <a href="xoakhuyenmai.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận xóa?');">Xóa</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
