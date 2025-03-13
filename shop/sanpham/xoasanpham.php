<?php
include "../config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM sanpham WHERE id = $id";
    
    if ($conn->query($sql)) {
        echo "<script> window.location.href='sanpham.php';</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>
