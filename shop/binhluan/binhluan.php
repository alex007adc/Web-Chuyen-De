<?php
session_start();
require '../config.php';

if (!isset($_SESSION['khachhang'])) {
    die("Bạn phải đăng nhập để bình luận.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sanpham'], $_POST['noidung'])) {
    $sanpham = $_POST['sanpham'];
    $khachhang = $_SESSION['khachhang'];
    $noidung = trim($_POST['noidung']);

    if (!empty($noidung)) {
        $sql = "INSERT INTO binhluan (sanpham, khachhang, noidung) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $sanpham, $khachhang, $noidung);
        $stmt->execute();
    }
}

header("Location: ../muahang/muahang.php?id=" . $sanpham);
exit();
?>
