<?php
require '../config.php'; // Kết nối CSDL

// Lọc theo trạng thái (nếu có)
$trangthai = isset($_GET['trangthai']) ? $_GET['trangthai'] : '';

// Truy vấn danh sách đơn hàng
$sql = "SELECT id, namekh, tenDH, sanpham, ngaydat, diachi, sdt, soluong, gia, ghichu, trangthai FROM donhang";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();


if ($trangthai != '') {
    $sql .= " WHERE dh.trangthai = ?";
}

$stmt = $conn->prepare($sql);
if ($trangthai != '') {
    $stmt->bind_param("s", $trangthai);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng</title>
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
            <h2>Danh sách đơn hàng</h2>

            <!-- Lọc đơn hàng -->
            <form method="get" class="mb-3">
                <label for="trangthai">Lọc theo trạng thái:</label>
                <select name="trangthai" id="trangthai" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    <option value="Chờ xác nhận" <?= ($trangthai == "Chờ xác nhận") ? 'selected' : ''; ?>>Chờ xác nhận</option>
                    <option value="Đã xác nhận" <?= ($trangthai == "Đã xác nhận") ? 'selected' : ''; ?>>Đã xác nhận</option>
                    <option value="Đang giao hàng" <?= ($trangthai == "Đang giao hàng") ? 'selected' : ''; ?>>Đang giao hàng</option>
                    <option value="Hoàn thành" <?= ($trangthai == "Hoàn thành") ? 'selected' : ''; ?>>Hoàn thành</option>
                    <option value="Đã hủy" <?= ($trangthai == "Đã hủy") ? 'selected' : ''; ?>>Đã hủy</option>
                </select>
            </form>

            <!-- Danh sách đơn hàng -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Tên đơn hàng</th>
                        <th>Sản phẩm</th>
                        <th>Ngày Đặt</th>
                        <th>Địa chỉ</th>
                        <th>SĐT</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Ghi Chú</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['namekh']; ?></td>
                            <td><?= $row['tenDH']; ?></td>
                            <td><?= $row['sanpham']; ?></td>
                            <td><?= $row['ngaydat']; ?></td>
                            <td><?= $row['diachi']; ?></td>
                            <td><?= $row['sdt']; ?></td>
                            <td><?= $row['soluong']; ?></td>
                            <td><?= number_format($row['gia'], 0, ',', '.') . " VND"; ?></td>
                            <td><?= $row['ghichu']; ?></td>
                            <td><?= $row['trangthai']; ?></td>
                            <td>
                                <a href="chitietdonhang.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm">Xem</a>
                                <a href="suadonhang.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="indonhang.php?id=<?= $row['id']; ?>" class="btn btn-secondary btn-sm">In</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
