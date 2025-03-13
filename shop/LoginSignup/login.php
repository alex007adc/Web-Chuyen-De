<?php
session_start();
include '../config.php'; // Kết nối đến database




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['gmail']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Kiểm tra tài khoản admin trước
        $sql = "SELECT * FROM adminshop WHERE gmail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['nameadmin'];
                header("Location: ../admin/admin.php"); // Chuyển hướng đến trang admin
                exit();
            } else {
                echo "<script>alert('Sai mật khẩu!'); window.location.href='LoginSignup.html';</script>";
                exit();
            }
        }

        // Nếu không phải admin, kiểm tra tài khoản khách hàng
        $sql = "SELECT * FROM khachhang WHERE gmail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password'])) {
                $_SESSION['khachhang_id'] = $user['id']; // Lưu ID khách hàng
                $_SESSION['khachhang_name'] = $user['namekh']; // Lưu tên khách hàng
                header("Location: ../shop/index.php"); // Chuyển hướng đến trang khách hàng
                exit();
            } else {
                echo "<script>alert('Sai mật khẩu!'); window.location.href='LoginSignup.html';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Email không tồn tại!'); window.location.href='LoginSignup.html';</script>";
            exit();
        }
    }
}
?>
