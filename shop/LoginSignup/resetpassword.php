<?php
session_start();
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['verified_email'];
    $new_password = password_hash($_POST["new_password"], PASSWORD_DEFAULT);

    // Cập nhật mật khẩu
    $sql = "UPDATE users SET password = ?, reset_code = NULL, reset_code_expire = NULL WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $email);
    $stmt->execute();

    echo "Mật khẩu đã được cập nhật!";
    session_destroy();
}
?>
