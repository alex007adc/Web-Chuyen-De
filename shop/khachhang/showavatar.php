<?php
require '../config.php';

if (!isset($_GET['id'])) {
    die("ID không hợp lệ");
}

$id = intval($_GET['id']);
$sql = "SELECT avatar FROM khachhang WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($avatarData);
$stmt->fetch();
$stmt->close();
$conn->close();

if ($avatarData) {
    header("Content-Type: image/png"); // Hoặc image/jpeg tùy loại ảnh
    echo $avatarData;
} else {
    readfile("../uploads/default.png"); // Ảnh mặc định nếu không có avatar
}
?>
