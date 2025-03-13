<?php
// Kết nối database
require '../config.php';

// Lấy danh sách danh mục
$danhmuc_sql = "SELECT id, tendanhmuc FROM danhmuc";
$danhmuc_result = $conn->query($danhmuc_sql);

// Lấy danh mục được chọn từ request
$danhmuc_id = isset($_GET['danhmuc']) ? (int)$_GET['danhmuc'] : 0;

// Truy vấn danh sách sản phẩm
$sql = "SELECT sanpham.*, danhmuc.tendanhmuc 
        FROM sanpham 
        JOIN danhmuc ON sanpham.tendanhmuc = danhmuc.id";
$params = [];

if ($danhmuc_id > 0) {
    $sql .= " WHERE sanpham.tendanhmuc = ?";
    $params[] = $danhmuc_id;
}

// Chuẩn bị truy vấn
$stmt = $conn->prepare($sql);

// Bind tham số nếu có lọc danh mục
if (!empty($params)) {
    $stmt->bind_param("i", ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
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

        <div class="container mt-4">
            <h2>Quản lý Sản phẩm</h2>

            <!-- Thanh chứa nút "Thêm sản phẩm" và danh mục -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="../sanpham/themsanpham.php" class="btn btn-primary">Thêm sản phẩm</a>

                <!-- Dropdown danh mục -->
                <form method="GET" action="sanpham.php" class="d-flex">
                    <select name="danhmuc" class="form-select me-2" onchange="this.form.submit()">
                        <option value="0">-- Chọn danh mục --</option>
                        <?php while ($row = $danhmuc_result->fetch_assoc()): ?>
                            <option value="<?= $row['id']; ?>" <?= ($row['id'] == $danhmuc_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['tendanhmuc']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?php if ($danhmuc_id > 0): ?>
                        <a href="sanpham.php" class="btn btn-secondary">Tất cả</a>
                    <?php endif; ?>
                </form>
            </div>

            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã SP</th>
                        <th>Hình ảnh</th>
                        <th>Tên SP</th>
                        <th>Mô tả</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Danh mục</th>
                        <th>Khuyến mãi (%)</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['masp']); ?></td>
                            <td>
                                <img src="../img/img.php?id=<?= $row['id']; ?>" width="200" height="100">
                            </td>
                            <td><?= htmlspecialchars($row['tensp']); ?></td>
                            <td><?= htmlspecialchars($row['mota']); ?></td>
                            <td><?= $row['soluong']; ?></td>
                            <td><strong><?= number_format($row['gia']); ?> VNĐ</strong></td>
                            <td><?= htmlspecialchars($row['tendanhmuc']); ?></td>
                            <td><?= $row['khuyenmai']; ?>%</td>
                            <td>
                                <a href="suasanpham.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="xoasanpham.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>