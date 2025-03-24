<?php
require '../config.php';

if (!isset($_GET['id'])) {
    die("Thiếu thông tin xóa.");
}

$id = intval($_GET['id']);

if ($id <= 0) {
    die("ID không hợp lệ.");
}

// Xóa bình luận / đánh giá
$sql = "DELETE FROM danhgia_binhluan WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Quay lại trang quản lý bình luận
header("Location: quanlybinhluan.php");
exit();
?>
