<?php
session_start();
require '../config.php'; // Kết nối đến database

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$danhmuc_id = intval($_GET['id']);

// Lấy danh sách sản phẩm thuộc danh mục
$sql = "SELECT * FROM sanpham WHERE tendanhmuc = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $danhmuc_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm theo danh mục</title>
</head>
<body>
    <h2>Danh sách sản phẩm</h2>
    <div class="product-list">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product">
                <h3><?= htmlspecialchars($row['tensp']); ?></h3>
                <p>Giá: <?= number_format($row['gia'], 0, ',', '.'); ?> VNĐ</p>
                <a href="chitietsanpham.php?id=<?= $row['id']; ?>">Xem chi tiết</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
