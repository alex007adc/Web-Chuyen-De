<?php
session_start();
require '../config.php'; // Kết nối database

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

// Nếu có người dùng, thực hiện truy vấn lấy avatar
if (isset($sql)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userName = $row['name'];
        $avatar = $row['avatar']; // Ảnh đại diện từ database
        $_SESSION['user_name'] = $userName;
        $isLoggedIn = true;
    }
    $stmt->close();
}

$id = $_POST['id'];
$avatar = isset($_POST['avatar']) ? base64_decode($_POST['avatar']) : null;

// Lấy thông tin sản phẩm
$sql = "SELECT tensp, soluong, gia, khuyenmai, hinhanh FROM sanpham WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='../shop/index.php';</script>";
    exit();
}

$row = $result->fetch_assoc();
$tensp = $row['tensp'];
$soluong = $row['soluong'];
$gia_goc = $row['gia'];
$gia_km = ($row['khuyenmai'] > 0) ? $row['gia'] - ($row['gia'] * $row['khuyenmai'] / 100) : $row['gia'];
$hinhanh = $row['hinhanh'];

// Kiểm tra số lượng hàng tồn kho
if ($soluong <= 0) {
    echo "<script>alert('Sản phẩm đã hết hàng!'); window.location.href='../shop/index.php';</script>";
    exit();
}

// Lấy danh sách phương thức thanh toán từ database
$sql_phuong_thuc = "SELECT id, ten_phuong_thuc FROM phuongthucthanhtoan WHERE trang_thai = 'Hoạt động'";
$result_phuong_thuc = $conn->query($sql_phuong_thuc);

$phuong_thuc_list = [];
if ($result_phuong_thuc->num_rows > 0) {
    while ($row_phuong_thuc = $result_phuong_thuc->fetch_assoc()) {
        $phuong_thuc_list[] = $row_phuong_thuc;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['thanhtoan'])) {
    $phuongthuc = $_POST['phuongthuc']; // ID phương thức thanh toán
    $giaohang = $_POST['giaohang']; // Hình thức giao hàng

    // Tính phí giao hàng
    $phi_giao_hang = ($giaohang === 'Nhanh') ? 30000 : 20000;

    // Kiểm tra khuyến mãi
    $gia_km = ($row['khuyenmai'] > 0) ? $row['gia'] - ($row['gia'] * $row['khuyenmai'] / 100) : $row['gia'];

    // Tổng tiền
    $tongtien = $gia_km + $phi_giao_hang;

    // Thêm vào đơn hàng
    $sql_insert = "INSERT INTO donhang (khachhang_id, sanpham_id, soluong, tongtien, trangthai, phuongthuc_id, giaohang) 
                   VALUES (?, ?, 1, ?, 'Chờ xử lý', ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iidis", $khachhang_id, $id, $tongtien, $phuongthuc, $giaohang);
    $stmt_insert->execute();

    // Trừ số lượng sản phẩm
    $sql_update = "UPDATE sanpham SET soluong = soluong - 1 WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $id);
    $stmt_update->execute();

    // Xử lý thanh toán online (nếu có)
    if ($phuongthuc == 'Online') {
        echo "<script>alert('Chuyển hướng đến cổng thanh toán...'); window.location.href='../payment_gateway.php';</script>";
        exit();
    }

    echo "<script>alert('Đặt hàng thành công!'); window.location.href='../giohang/giohang.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thanh Toán - <?= htmlspecialchars($tensp); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
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
                        <?php if ($isLoggedIn): ?>
                            <button class="btn btn-outline-dark dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if ($avatar): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($avatar); ?>" 
                                        class="rounded-circle" width="40" height="40" alt="Avatar">
                                <?php else: ?>
                                    <div class="user-initial rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white" 
                                        style="width: 40px; height: 40px;">
                                        <?= htmlspecialchars($initial); ?>
                                    </div>
                                <?php endif; ?>
                            </button>

                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <?php if (isset($_SESSION['admin_id'])): ?>
                                    <li><a class="dropdown-item" href="../admin/admin.php">Trang Quản Trị</a></li>
                                    <li><a class="dropdown-item" href="../logout.php">Đăng Xuất</a></li>
                                <?php elseif (isset($_SESSION['khachhang_id'])): ?>
                                    <li><a class="dropdown-item" href="../khachhang/thongtinkhachhang.php">Xem Thông Tin</a></li>
                                    <li><a class="dropdown-item" href="giohang.php">Giỏ Hàng</a></li>
                                    <li><a class="dropdown-item" href="../logout.php">Đăng Xuất</a></li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?>
                            <button class="btn btn-outline-dark" type="button" onclick="window.location.href='../LoginSignup/LoginSignup.html';">
                                Đăng Nhập
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </nav>

    <!-- Nội dung thanh toán -->
    <div class="container mt-5">
        <h2 class="text-center">Xác Nhận Thanh Toán</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <img src="../img/img.php?id=<?= $id; ?>" class="img-fluid rounded shadow" alt="<?= htmlspecialchars($tensp); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($tensp); ?></h5>
                        <p class="card-text">Giá gốc: <del><?= number_format($gia_goc, 0, ',', '.'); ?> VNĐ</del></p>
                        <p class="card-text"><strong>Giá khuyến mãi: <?= number_format($gia_km, 0, ',', '.'); ?> VNĐ</strong></p>

                        <form method="POST" action="thanhtoan.php">
                            <input type="hidden" name="id" value="<?= $id; ?>">
                            <input type="hidden" name="avatar" value="<?= base64_encode($avatar); ?>">

                            <!-- Chọn phương thức thanh toán -->
                            <label class="form-label">Chọn phương thức thanh toán:</label>
                            <select name="phuongthuc" class="form-control" required>
                                <?php foreach ($phuong_thuc_list as $phuong_thuc): ?>
                                    <option value="<?= htmlspecialchars($phuong_thuc['id']); ?>">
                                        <?= htmlspecialchars($phuong_thuc['ten_phuong_thuc']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Nút xác nhận thanh toán -->
                            <button type="submit" name="thanhtoan" class="btn btn-primary mt-3">Xác Nhận Thanh Toán</button>

                            <!-- Nút hủy thanh toán -->
                            <a href="../muahang/muahang.php?id=<?= $id; ?>" class="btn btn-secondary mt-3">Hủy Thanh Toán</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Footer -->
<footer class="py-4 bg-dark text-white text-center mt-5">
    <p class="m-0">Bản Quyền &copy; Cửa Hàng Đồ Da Nam 2025</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>