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


$id = $_SESSION['khachhang_id'];

// Truy vấn thông tin khách hàng
$sql = "SELECT namekh, gmail, SDT, diachi, avatar FROM khachhang WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Không tìm thấy thông tin khách hàng!");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Thông Tin Khách Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
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
                            <?php if ($isLoggedIn): ?>
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($user['avatar']); ?>" class="rounded-circle" width="40" height="40" alt="Avatar">
                                <?php else: ?>
                                    <div class="user-initial rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white" style="width: 40px; height: 40px;">
                                        <?= htmlspecialchars($initial); ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                Đăng Nhập
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <?php if ($isLoggedIn && isset($_SESSION['khachhang_id'])): ?>
                                <li><a class="dropdown-item" href="thongtinkhachhang.php">Xem Thông Tin</a></li>
                                <li><a class="dropdown-item" href="../giohang/giohang.php">Giỏ Hàng</a></li>
                                <li><a class="dropdown-item" href="../logout.php">Đăng Xuất</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
    </nav>

    <!-- Thông tin khách hàng -->
    <div class="container mt-5">
        <h2 class="text-center">Thông Tin Khách Hàng</h2>
        <div class="card mx-auto mt-4" style="max-width: 500px;">
            <div class="card-body">
                <div class="text-center">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($user['avatar']); ?>" alt="Ảnh đại diện" class="img-thumbnail" width="150">
                    <?php else: ?>
                        <p>Chưa có ảnh đại diện</p>
                    <?php endif; ?>
                </div>
                <p><strong>Họ và Tên:</strong> <?= htmlspecialchars($user['namekh']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['gmail']); ?></p>
                <p><strong>Số Điện Thoại:</strong> <?= htmlspecialchars($user['SDT']); ?></p>
                <p><strong>Địa Chỉ:</strong> <?= htmlspecialchars($user['diachi']); ?></p>
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="capnhatthongtin.php" class="btn btn-primary">Cập Nhật Thông Tin</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
