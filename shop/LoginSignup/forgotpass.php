<?php
session_start();
include '../config.php'; // Kết nối database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    // Kiểm tra email có tồn tại không
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(32)); // Tạo token bảo mật
        $expire_time = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token hết hạn sau 1 giờ

        // Lưu token vào database
        $sql = "UPDATE users SET reset_token = ?, reset_token_expire = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expire_time, $email);
        $stmt->execute();

        // Gửi email đặt lại mật khẩu
        $reset_link = "http://yourwebsite.com/resetpass.php?token=$token"; // yourwebsite.com thay ten mien website
        $subject = "Reset Your Password";
        $message = "Click this link to reset your password: $reset_link";
        $headers = "From: no-reply@yourwebsite.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($email, $subject, $message, $headers)) {
            echo "Check your email for the reset link.";
        } else {
            echo "Error sending email.";
        }
    } else {
        echo "Email not found.";
    }
}
?>
