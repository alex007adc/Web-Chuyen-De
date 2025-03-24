<?php
include '../config.php';

// Lấy danh sách loại khách hàng
$loaikhach_sql = "SELECT makh, ten_loai FROM loaikhachhang";
$loaikhach_result = $conn->query($loaikhach_sql);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM khachhang WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $khachhang = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $makh = trim($_POST['makh']);
    $name = trim($_POST['namekh']);
    $email = trim($_POST['gmail']);
    $sdt = trim($_POST['SDT']);
    $diachi = trim($_POST['diachi']);
    $trangthai = isset($_POST['trangthai']) ? intval($_POST['trangthai']) : 1;

    $update_sql = "UPDATE khachhang SET makh = ?, namekh = ?, gmail = ?, SDT = ?, diachi = ?, trangthai = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssssii", $makh, $name, $email, $sdt, $diachi, $trangthai, $id);
    
    if ($stmt->execute()) {
        echo "<script>window.location.href='khachhang.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật!');</script>";
    }
}
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

        <div class="container mt-5">
            <h2>Chỉnh sửa thông tin khách hàng</h2>
            <form method="POST">
                <label>Loại khách hàng:</label>
                <select name="makh" class="form-control">
                    <?php while ($row = $loaikhach_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['makh']; ?>" <?php echo ($row['makh'] == $khachhang['makh']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['ten_loai']); ?>
                        </option>
                    <?php } ?>
                </select>

                <label>Họ tên:</label>
                <input type="text" name="namekh" class="form-control" value="<?php echo htmlspecialchars($khachhang['namekh']); ?>" required>

                <label>Email:</label>
                <input type="email" name="gmail" class="form-control" value="<?php echo htmlspecialchars($khachhang['gmail']); ?>" required>

                <label>Số điện thoại:</label>
                <input type="text" name="SDT" class="form-control" value="<?php echo htmlspecialchars($khachhang['SDT']); ?>" required>

                <label>Địa chỉ:</label>
                <input type="text" name="diachi" class="form-control" value="<?php echo htmlspecialchars($khachhang['diachi']); ?>" required>

                <label>Trạng thái tài khoản:</label>
                <select name="trangthai" class="form-control">
                    <option value="1" <?php echo ($khachhang['trangthai'] == 1) ? 'selected' : ''; ?>>Hoạt động</option>
                    <option value="0" <?php echo ($khachhang['trangthai'] == 0) ? 'selected' : ''; ?>>Bị khóa</option>
                </select>

                <button type="submit" class="btn btn-primary mt-3">Lưu thay đổi</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
