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
        // Tạo mã xác nhận 6 ký tự ngẫu nhiên
        $verification_code = strtoupper(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6));
        $expire_time = date("Y-m-d H:i:s", strtotime("+5 minutes")); // Hết hạn sau 5 phút

        // Lưu mã vào database
        $sql = "UPDATE users SET reset_code = ?, reset_code_expire = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $verification_code, $expire_time, $email);
        $stmt->execute();

        // Gửi email chứa mã xác nhận
        $subject = "Mã xác nhận quên mật khẩu";
        $message = "Mã xác nhận của bạn là: $verification_code. Mã có hiệu lực trong 10 phút.";
        $headers = "From: no-reply@yourwebsite.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['reset_email'] = $email; // Lưu email để dùng ở bước xác nhận
            echo "Mã xác nhận đã được gửi đến email của bạn!";
        } else {
            echo "Không thể gửi email!";
        }
    } else {
        echo "Email không tồn tại!";
    }
}
?>
