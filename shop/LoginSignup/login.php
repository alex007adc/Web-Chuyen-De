<?php
session_start();
include '../config.php'; // Kết nối database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = trim($_POST['gmail']); // Có thể nhập Username hoặc Gmail
    $password = trim($_POST['password']);

    if (empty($input) || empty($password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.location.href='LoginSignup.html';</script>";
        exit();
    }

    // Hàm kiểm tra đăng nhập
    function checkLogin($conn, $table, $emailField, $usernameField, $idField, $nameField, $redirect) {
        global $input, $password;

        $sql = "SELECT * FROM $table WHERE $emailField = ? OR $usernameField = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $input, $input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password'])) {
                $_SESSION[$idField] = $user['id']; // Lưu ID
                $_SESSION[$nameField] = $user[$usernameField]; // Lưu tên
                
                header("Location: $redirect"); // Chuyển hướng
                exit();
            } else {
                echo "<script>alert('Sai mật khẩu!'); window.location.href='LoginSignup.html';</script>";
                exit();
            }
        }
    }

    // Kiểm tra tài khoản Admin
    checkLogin($conn, "adminshop", "gmail", "nameadmin", "admin_id", "admin_name", "../admin/admin.php");

    // Kiểm tra tài khoản Khách hàng
    checkLogin($conn, "khachhang", "gmail", "namekh", "khachhang_id", "khachhang_name", "../shop/index.php");

    // Nếu không tìm thấy tài khoản
    echo "<script>alert('Email hoặc Username không tồn tại!'); window.location.href='LoginSignup.html';</script>";
    exit();
}
?>
