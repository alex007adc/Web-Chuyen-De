<?php
session_start();
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['reset_email'];
    $input_code = trim($_POST["code"]);

    // Kiểm tra mã có đúng không
    $sql = "SELECT reset_code, reset_code_expire FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['reset_code'] == $input_code) {
        if (strtotime($user['reset_code_expire']) >= time()) {
            $_SESSION['verified_email'] = $email;
            header("Location: resetpassword.php");
            exit();
        } else {
            echo "Mã xác nhận đã hết hạn!";
        }
    } else {
        echo "Mã xác nhận không đúng!";
    }
}
?>
