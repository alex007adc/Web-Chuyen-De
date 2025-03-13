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

// Kiểm tra nếu không có ID sản phẩm
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Lấy thông tin sản phẩm từ database (bao gồm tên danh mục)
$sql = "SELECT sp.tensp, sp.mota, sp.soluong, sp.gia, sp.khuyenmai, sp.hinhanh, dm.tendanhmuc
        FROM sanpham sp
        JOIN danhmuc dm ON sp.tendanhmuc = dm.id
        WHERE sp.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='index.php';</script>";
    exit();
}

// Lấy trung bình số sao và số lượt đánh giá từ bảng danhgia_binhluan
$sql_danhgia = "SELECT AVG(sao) AS trungbinh_sao, COUNT(*) AS soluot FROM danhgia_binhluan WHERE sanpham = ?";
$stmt_danhgia = $conn->prepare($sql_danhgia);
$stmt_danhgia->bind_param("i", $id);
$stmt_danhgia->execute();
$result_danhgia = $stmt_danhgia->get_result()->fetch_assoc();
$trungbinh_sao = round($result_danhgia['trungbinh_sao'], 1);
$soluot_danhgia = $result_danhgia['soluot'];

$row = $result->fetch_assoc();
$gia_km = ($row['khuyenmai'] > 0) ? $row['gia'] - ($row['gia'] * $row['khuyenmai'] / 100) : $row['gia'];

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mua Hàng - <?= htmlspecialchars($row['tensp']); ?></title>
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="categoryDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Danh Mục</a>
                        <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                            <li><a class="dropdown-item" href="#" onclick="filterProducts('all')">Tất Cả</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterProducts('sale')">Sản Phẩm Giảm Giá</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterProducts('popular')">Phổ Biến</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterProducts('new')">Hàng Mới Về</a></li>
                        </ul>
                    </li>
                </ul>
                <form class="d-flex">
                    <div class="dropdown">
                    <button class="btn btn-outline-dark dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if ($isLoggedIn): ?>
                            <div class="user-initial"><?= htmlspecialchars($initial); ?></div>
                        <?php else: ?>
                            Đăng Nhập
                        <?php endif; ?>
                    </button>
                    </button>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <?php if ($isLoggedIn): ?>
                                <?php if (isset($_SESSION['admin_id'])): ?>
                                    <!-- Menu dành cho Admin -->
                                    <li><a class="dropdown-item" href="../admin/admin.php">Trang Quản Trị</a></li>
                                    <li><a class="dropdown-item" href="profile.php">Xem Thông Tin</a></li>
                                    <li><a class="dropdown-item" href="../logout.php">Đăng Xuất</a></li>
                                <?php else: ?>
                                    <!-- Menu dành cho User -->
                                    <li><a class="dropdown-item" href="profile.php">Xem Thông Tin</a></li>
                                    <li><a class="dropdown-item" href="../giohang/giohang.php">Giỏ Hàng</a></li>
                                    <li><a class="dropdown-item" href="../logout.php">Đăng Xuất</a></li>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- Menu dành cho người chưa đăng nhập -->
                                <li><a class="dropdown-item" href="../LoginSignup/LoginSignup.html">Đăng Nhập</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
    </nav>

    <!-- Chi tiết sản phẩm -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <img src="../img/img.php?id=<?= $id; ?>" class="img-fluid rounded shadow" alt="<?= htmlspecialchars($row['tensp']); ?>">
            </div>
            <div class="col-md-6">
                <h2>Tên Sản Phẩm: <?= htmlspecialchars($row['tensp']); ?></h2>
                <p class="text-muted"><?= nl2br(htmlspecialchars($row['mota'])); ?></p>

                <!-- Hiển thị danh mục -->
                <p><strong>Danh Mục: <?= htmlspecialchars($row['tendanhmuc']); ?></strong></p>

                <!-- Hiển thị giá -->
                <?php if ($row['khuyenmai'] > 0): ?>
                    <p class="text-muted text-decoration-line-through">Giá Cũ: 
                        <?= number_format($row['gia'], 0, ',', '.'); ?>VNĐ
                    </p>

                    <h3 class="text-danger">Giá Mới: 
                        <?= number_format($gia_km, 0, ',', '.'); ?>VNĐ
                    </h3>
                <?php else: ?>
                    <h3>Giá: <?= number_format($row['gia'], 0, ',', '.'); ?>VNĐ</h3>
                <?php endif; ?>
                <p class="text-secondary">
                    <?php if ($row['soluong'] > 0): ?>
                        <strong class="text-success">Còn Lại: <?= $row['soluong']; ?> Sản Phẩm</strong>
                    <?php else: ?>
                        <strong class="text-danger">Hết Hàng</strong>
                    <?php endif; ?>
                </p>

                <!-- Chọn số lượng -->
                <form action="thanhtoan.php" method="POST">
                    <input type="hidden" name="id" value="<?= $id; ?>">
                    <br>
                    
                    <!-- Nút Mua ngay -->
                    <?php if ($row['soluong'] > 0): ?>
                        <button type="submit" class="btn btn-primary">Mua Ngay</button>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Hết Hàng</button>
                    <?php endif; ?>
                </form>

                <!-- Nút quay lại -->
                <br>
                <a href="../shop/index.php" class="btn btn-outline-secondary">Quay Lại</a>
            </div>
        </div>
    </div>
    <!-- Đánh giá & Bình luận -->
    <div class="container mt-4">
        <h4>Đánh Giá & Bình Luận</h4>
        <p><strong>Trung Bình:</strong> <?= $trungbinh_sao; ?> ⭐ (<?= $soluot_danhgia; ?> Lượt Đánh Giá)</p>

        <?php if ($isLoggedIn): ?>
            <form action="../binhluan/binhluansanpham.php" method="POST">
                <input type="hidden" name="sanpham" value="<?= $id; ?>">
                <label for="sao">Chọn Số Sao:</label>
                <select name="sao" id="sao" class="form-select" required>
                    <option value="5">⭐⭐⭐⭐⭐ - Rất Tốt</option>
                    <option value="4">⭐⭐⭐⭐ - Tốt</option>
                    <option value="3">⭐⭐⭐ - Bình Thường</option>
                    <option value="2">⭐⭐ - Tệ</option>
                    <option value="1">⭐ - Rất Tệ</option>
                </select>
                <label for="noidung">Bình Luận:</label>
                <textarea name="noidung" id="noidung" class="form-control" rows="3" required></textarea>
                <button type="submit" class="btn btn-primary mt-2">Gửi</button>
            </form>
        <?php else: ?>
            <p class="text-danger">Bạn Cần Đăng Nhập Để Bình Luận!</p>
        <?php endif; ?>

        <div class="mt-3">
            <?php
            $sql_binhluan = "SELECT 
                            COALESCE(a.nameadmin, k.namekh) AS nguoi_binhluan, 
                            d.sao, d.noidung, d.thoigian 
                        FROM danhgia_binhluan d
                        LEFT JOIN khachhang k ON d.khachhang = k.id 
                        LEFT JOIN adminshop a ON d.admin = a.id 
                        WHERE d.sanpham = ? AND d.duyet = 1 
                        ORDER BY d.thoigian DESC";

            $stmt_binhluan = $conn->prepare($sql_binhluan);
            $stmt_binhluan->bind_param("i", $id);
            $stmt_binhluan->execute();
            $result_binhluan = $stmt_binhluan->get_result();
            
            while ($bl = $result_binhluan->fetch_assoc()):
            ?>
                <p>
                    <strong><?= htmlspecialchars($bl['nguoi_binhluan']); ?></strong> - 
                    <span class="text-muted"><?= date('d/m/Y H:i', strtotime($bl['thoigian'])); ?></span> - 
                    <span class="text-warning"><?= str_repeat('⭐', $bl['sao']); ?></span>
                </p>
                <p class="mb-1"><?= htmlspecialchars($bl['noidung']); ?></p>
            <?php endwhile; ?>
        </div>
    </div>

<!-- Footer -->
<footer class="py-4 bg-dark text-white text-center mt-5">
    <p class="m-0">Bản Quyền &copy; Cửa Hàng Đồ Da Nam 2025</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
