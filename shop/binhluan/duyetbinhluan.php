<?php
require '../config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Cập nhật trạng thái duyệt chỉ khi có nội dung bình luận
    $sql = "UPDATE danhgia_binhluan SET duyet = 1 WHERE id = ? AND noidung IS NOT NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: quanlybinhluan.php");
    exit();
}
?>
