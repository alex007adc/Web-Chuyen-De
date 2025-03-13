<?php
session_start();
require '../config.php'; // Kết nối database

// Kiểm tra đăng nhập của khách hàng hoặc admin
if (!isset($_SESSION['khachhang_id']) && !isset($_SESSION['admin_id'])) {
    echo "<script>alert('Bạn cần đăng nhập để gửi đánh giá!'); window.location.href='../LoginSignup/LoginSignup.html';</script>";
    exit();
}

// Xác định ai là người bình luận (admin hay khách hàng)
$khachhang_id = isset($_SESSION['khachhang_id']) ? $_SESSION['khachhang_id'] : NULL;
$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : NULL;

$sanpham_id = $_POST['sanpham'];
$sao = $_POST['sao'];
$noidung = trim($_POST['noidung']);

// Kiểm tra nội dung không được rỗng
if (empty($noidung)) {
    echo "<script>alert('Nội dung không được để trống!'); window.history.back();</script>";
    exit();
}

// Chèn vào database
$sql = "INSERT INTO danhgia_binhluan (sanpham, khachhang, admin, sao, noidung, thoigian, duyet) 
        VALUES (?, ?, ?, ?, ?, NOW(), 0)";
$stmt = $conn->prepare($sql);

// Nếu là khách hàng, đặt admin_id = NULL; nếu là admin, đặt khachhang_id = NULL
$khachhang_id = $khachhang_id ?: NULL;
$admin_id = $admin_id ?: NULL;

$stmt->bind_param("iiiss", $sanpham_id, $khachhang_id, $admin_id, $sao, $noidung);



if ($stmt->execute()) {
    echo "<script>alert('Đánh giá của bạn đã được gửi và đang chờ duyệt!'); window.location.href='../muahang/muahang.php?id=$sanpham_id';</script>";
} else {
    echo "<script>alert('Có lỗi xảy ra! Vui lòng thử lại.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
