<?php
include '../config.php';

// Lấy danh sách khách hàng từ database
$sql = "SELECT id, makh, namekh, gmail, SDT, diachi, trangthai, timekhoa, loinhappass FROM khachhang ORDER BY id DESC";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Admin</title>
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
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Đăng xuất</a></li>
                </ul>
            </div>
        </nav>

        <div class="container mt-4">
            <h2>Danh Sách Khách Hàng</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Loại KH</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['makh']); ?></td>
                            <td><?php echo htmlspecialchars($row['namekh']); ?></td>
                            <td><?php echo htmlspecialchars($row['gmail']); ?></td>
                            <td><?php echo htmlspecialchars($row['SDT']); ?></td>
                            <td><?php echo htmlspecialchars($row['diachi']); ?></td>
                            <td>
                                <?php
                                $current_time = time();
                                $lock_time_db = strtotime($row['timekhoa']);
                                $time_diff = $current_time - $lock_time_db;
                                $time_remaining = 300 - $time_diff; // 5 phút = 300 giây

                                if ($row['trangthai'] == 0) {
                                    echo "<span class='text-danger'>Bị khóa vĩnh viễn</span>";
                                } elseif ($row['loinhappass'] >= 5 && $time_diff < 300) {
                                    echo "<span class='text-warning'>Tạm khóa (" . gmdate("i:s", $time_remaining) . " phút)</span>";
                                } else {
                                    echo "<span class='text-success'>Hoạt động</span>";
                                }
                                ?>
                            </td>
                            <td>
                                <a href="suakhachhang.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="xoakhachhang.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?')">Xóa</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>