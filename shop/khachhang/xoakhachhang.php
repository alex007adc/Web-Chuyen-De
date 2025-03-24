<?php
include '../config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Kiểm tra khách hàng có tồn tại không
    $check_sql = "SELECT * FROM khachhang WHERE id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Xóa khách hàng
        $delete_sql = "DELETE FROM khachhang WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "<script>window.location.href='quanlykhachhang.php';</script>";
        } else {
            echo "<script>window.location.href='quanlykhachhang.php';</script>";
        }
    } else {
        echo "<script>window.location.href='quanlykhachhang.php';</script>";
    }
}
?>
