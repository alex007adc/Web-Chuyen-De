<?php
session_start();
require '../config.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Truy vấn danh sách bình luận & đánh giá
$sql = "SELECT d.id, d.khachhang, d.admin, d.sanpham, d.sao, d.noidung, d.thoigian, d.duyet, 
               k.namekh AS namekh, a.nameadmin AS nameadmin
        FROM danhgia_binhluan d
        LEFT JOIN khachhang k ON d.khachhang = k.id
        LEFT JOIN adminshop a ON d.admin = a.id
        ORDER BY d.thoigian DESC";


$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Bình Luận & Đánh Giá</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/admin.css">
</head>
<body>

    <!-- Sidebar -->
    <div id="sidebar">
        <a href="../admin/admin.php">Trang quản trị</a>
        <a href="../shop/index.php">Website</a>
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
    
        <h2>Quản lý bình luận & đánh giá</h2>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Nội dung</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Số sao</th> <!-- Hiển thị số sao nếu có -->
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td>
                        <?php 
                        if ($row['khachhang']) {
                            echo htmlspecialchars($row['namekh']);
                        } elseif ($row['admin']) {
                            echo htmlspecialchars($row['nameadmin']);
                        } else {
                            echo "Không xác định";
                        }
                        ?>
                    </td>
                    <td><?= !empty($row['noidung']) ? nl2br(htmlspecialchars($row['noidung'])) : "(Không có bình luận)"; ?></td>
                    <td><?= $row['thoigian']; ?></td>
                    <td>
                        <?= ($row['duyet'] == 1) ? '<span class="badge bg-success">Đã duyệt</span>' : '<span class="badge bg-warning text-dark">Chờ duyệt</span>'; ?>
                    </td>
                    <td>
                        <?= ($row['sao'] !== NULL) ? $row['sao'] . ' ★' : 'Không có'; ?>
                    </td>
                    <td>
                        <?php if (!empty($row['noidung']) && $row['duyet'] == 0): ?>
                            <a href="duyetbinhluan.php?id=<?= $row['id']; ?>" class="btn btn-success btn-sm">Duyệt</a>
                        <?php endif; ?>
                        <a href="xoabinhluan.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận xóa?');">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
