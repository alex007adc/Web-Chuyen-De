<?php
session_start();
require '../config.php'; // Kết nối đến database

$isLoggedIn = false;
$userName = '';
$avatar = null;

if (isset($_SESSION['khachhang_id'])) {
    $id = $_SESSION['khachhang_id'];
    $sql = "SELECT namekh, avatar FROM khachhang WHERE id = ?";
} elseif (isset($_SESSION['admin_id'])) {
    $id = $_SESSION['admin_id'];
    $sql = "SELECT nameadmin AS namekh, avatar FROM adminshop WHERE id = ?";
}

if (isset($sql)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userName = $row['namekh'];
        $avatar = $row['avatar']; // Ảnh đại diện từ database
        $_SESSION['user_name'] = $userName;
        $isLoggedIn = true;
    }
    $stmt->close();
}

$initial = $isLoggedIn ? strtoupper(mb_substr($userName, 0, 1)) : "";

// Lấy danh sách danh mục từ bảng danhmuc
$sql_danhmuc = "SELECT id, tendanhmuc FROM danhmuc";
$result_danhmuc = $conn->query($sql_danhmuc);



$where = "WHERE 1"; // Luôn đúng để dễ nối thêm điều kiện

// Lọc theo danh mục nếu có
if (isset($_GET['danhmuc']) && $_GET['danhmuc'] !== 'all') {
    $danhmuc_id = intval($_GET['danhmuc']);
    $where .= " AND sp.tendanhmuc = $danhmuc_id";
}

// Lọc theo từ khóa tìm kiếm nếu có
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $query = $conn->real_escape_string($_GET['query']);
    $where .= " AND sp.tensp LIKE '%$query%'";
}

$sql = "SELECT sp.id, sp.tensp, sp.hinhanh, sp.soluong, sp.gia, sp.khuyenmai, dm.tendanhmuc 
        FROM sanpham sp 
        JOIN danhmuc dm ON sp.tendanhmuc = dm.id 
        $where";

$result = $conn->query($sql);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Cửa Hàng Đồ Da Nam Cao Cấp</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
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
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="#!">Trang Chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#!">Giới Thiệu</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="categoryDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Danh Mục</a>
                        <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                        <li><a class="dropdown-item" href="index.php?danhmuc=all">Tất Cả</a></li>
                            <?php while ($row_danhmuc = $result_danhmuc->fetch_assoc()): ?>
                                <li><a class="dropdown-item" href="index.php?danhmuc=<?= $row_danhmuc['id']; ?>">
                                    <?= htmlspecialchars($row_danhmuc['tendanhmuc']); ?>
                                </a></li>
                            <?php endwhile; ?>
                        </ul>
                    </li>
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
                                    <li><a class="dropdown-item" href="../giohang/giohang.php">Giỏ Hàng</a></li>
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

    <!-- Header-->
    <header class="bg-dark py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Mua Sắm Phong Cách</h1>
                <p class="lead fw-normal text-white-50 mb-0">Cửa Hàng Đồ Da Dành Cho Phái Mạnh</p>
            </div>
            <!-- Form tìm kiếm -->
            <form class="d-flex" id="searchForm">
                <input class="form-control me-2" type="search" placeholder="Tìm kiếm sản phẩm..." 
                    aria-label="Search" name="query" id="searchInput">
                <button class="btn btn-outline-dark" type="submit">Tìm Kiếm</button>
            </form>
        </div>
    </header>

    <!-- Danh sách sản phẩm -->
    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <div id="productList" class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col mb-5">
                        <div class="card h-100 position-relative">
                            <!-- Hình ảnh sản phẩm -->
                            <img src="../img/img.php?id=<?= $row['id']; ?>" class="card-img-top">

                            <!-- Chi tiết sản phẩm -->
                            <div class="card-body p-4 text-center">
                                <h5 class="fw-bolder"><?= htmlspecialchars($row['tensp']); ?></h5>

                                <!-- Hiển thị danh mục -->
                                <p>Danh Mục: <strong><?= htmlspecialchars($row['tendanhmuc']); ?></strong></p>

                                <!-- Hiển thị số lượng -->
                                <p class="text-secondary small">
                                    <?php if ($row['soluong'] > 0): ?>
                                        Còn Lại: <strong class="text-success"><?= $row['soluong']; ?></strong> Sản Phẩm
                                    <?php else: ?>
                                        <strong class="text-danger">Hết Hàng</strong>
                                    <?php endif; ?>
                                </p>

                                <!-- Hiển thị giá và giá khuyến mãi -->
                                <?php if ($row['khuyenmai'] > 0): ?>
                                    <span class="text-muted text-decoration-line-through">
                                        <?= number_format($row['gia'], 0, ',', '.'); ?>VNĐ
                                    </span>
                                    <strong class="text-danger">
                                        <?= number_format($row['gia'] - ($row['gia'] * $row['khuyenmai'] / 100), 0, ',', '.'); ?>VNĐ
                                    </strong>
                                <?php else: ?>
                                    <strong>
                                        <?= number_format($row['gia'], 0, ',', '.'); ?>VNĐ
                                    </strong>
                                <?php endif; ?>
                            </div>

                            <!-- Nút xem chi tiết -->
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent text-center">
                                <a class="btn btn-outline-dark mt-auto" href="../donhang/chitietsanpham.php?id=<?= $row['id']; ?>">Xem Chi Tiết</a>
                            </div>
                            <!-- Nút thêm vào giỏ hàng -->
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent text-center">
                                <?php if (!isset($_SESSION['khachhang_id']) && !isset($_SESSION['admin_id'])): ?>
                                    <a class="btn btn-outline-dark mt-auto" href="../muahang/muahang.php?id=<?= $row['id']; ?>">Mua Hàng</a>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent text-center">
                                <?php if (isset($_SESSION['khachhang_id'])): ?>
                                    <a class="btn btn-outline-dark mt-auto" href="../muahang/muahang.php?id=<?= $row['id']; ?>">Mua Hàng</a>
                                <?php elseif (isset($_SESSION['admin_id'])): ?>
                                    <a class="btn btn-outline-dark mt-auto" href="../muahang/muahang.php?id=<?= $row['id']; ?>">Mua Hàng</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <!-- Footer-->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Bản quyền &copy; Cửa Hàng Đồ Da Nam Cao Cấp 2025</p>
        </div>
    </footer>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>
</body>
</html>