<?php
session_start();
require '../config.php'; // Kết nối đến database

// Kiểm tra đăng nhập
$isLoggedIn = false;
$initial = ""; // Mặc định

if (isset($_SESSION['khachhang_id']) && !empty($_SESSION['khachhang_name'])) {
    $isLoggedIn = true;
    $initial = strtoupper(mb_substr($_SESSION['khachhang_name'], 0, 1)); // Lấy chữ cái đầu của khách hàng
} elseif (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_name'])) {
    $isLoggedIn = true;
    $initial = strtoupper(mb_substr($_SESSION['admin_name'], 0, 1)); // Lấy chữ cái đầu của admin
}

$khachhang_id = $_SESSION['khachhang_id'];

$sql_user = "SELECT avatar FROM khachhang WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $khachhang_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();
// Lấy thông tin giỏ hàng từ database
$sql = "SELECT gh.id, sp.tensp, sp.gia, gh.soluong, sp.hinhanh 
        FROM giohang gh 
        JOIN sanpham sp ON gh.sanpham_id = sp.id 
        WHERE gh.khachhang_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $khachhang_id);
$stmt->execute();
$result = $stmt->get_result();

$giohang = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $giohang[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giỏ Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="#!">Cửa Hàng Nam</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Chuyển đổi menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="../shop/index.php">Trang Chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#!">Giới Thiệu</a></li>
                </ul>
                <form class="d-flex">
                    <div class="dropdown">
                        <button class="btn btn-outline-dark dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if ($isLoggedIn && !empty($user['avatar'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($user['avatar']); ?>" class="rounded-circle" width="40" height="40" alt="Avatar">
                            <?php elseif ($isLoggedIn): ?>
                                <div class="user-initial rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white" style="width: 40px; height: 40px;">
                                    <?= htmlspecialchars($initial); ?>
                                </div>
                            <?php else: ?>
                                Đăng Nhập
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <?php if ($isLoggedIn && isset($_SESSION['khachhang_id'])): ?>
                                <li><a class="dropdown-item" href="../khachhang/thongtinkhachhang.php">Xem Thông Tin</a></li>
                                <li><a class="dropdown-item" href="../giohang/giohang.php">Giỏ Hàng</a></li>
                                <li><a class="dropdown-item" href="../logout.php">Đăng Xuất</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Giỏ Hàng</h2>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Hình Ảnh</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Giá</th>
                    <th>Số Lượng</th>
                    <th>Tổng Tiền</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php $tongtien = 0; ?>
                <?php foreach ($giohang as $item): ?>
                    <tr>
                        <td><img src="data:image/jpeg;base64,<?= base64_encode($item['hinhanh']); ?>" width="50" height="50"></td>
                        <td><?= htmlspecialchars($item['tensp']); ?></td>
                        <td><?= number_format($item['gia'], 0, ',', '.'); ?> VND</td>
                        <td><?= $item['soluong']; ?></td>
                        <td><?= number_format($item['gia'] * $item['soluong'], 0, ',', '.'); ?> VND</td>
                        <td>
                            <a href="xoagiohang.php?id=<?= $item['id']; ?>" class="btn btn-danger btn-sm">Xóa</a>
                        </td>
                    </tr>
                    <?php $tongtien += $item['gia'] * $item['soluong']; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h4 class="text-end">Tổng tiền: <?= number_format($tongtien, 0, ',', '.'); ?> VND</h4>
        <div class="text-center mt-3">
            <a href="../shop/index.php" class="btn btn-secondary">Tiếp tục mua sắm</a>
            <a href="thanhtoan.php" class="btn btn-primary">Thanh Toán</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
