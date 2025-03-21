<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
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
            <h2>Chào mừng Admin!</h2>
            <p>Đây là bảng điều khiển quản lý hệ thống.</p>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
