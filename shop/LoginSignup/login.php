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

    /** Xử lý đăng nhập cho KHÁCH HÀNG (có khóa 5 phút) **/
    $sql = "SELECT * FROM khachhang WHERE gmail = ? OR namekh = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $id = $user['id'];
        $failed_attempts = $user['loinhappass'];
        $lock_time = $user['timekhoa'];

        // Kiểm tra nếu tài khoản bị khóa
        if ($failed_attempts >= 5) {
            $current_time = time();
            $lock_time_db = strtotime($lock_time);
            
            if ($current_time - $lock_time_db < 300) { // Nếu chưa hết 5 phút
                echo "<script>alert('Tài khoản của bạn bị khóa trong 5 phút. Vui lòng thử lại sau!'); window.location.href='LoginSignup.html';</script>";
                exit();
            } else {
                // Hết 5 phút -> Mở khóa
                $conn->query("UPDATE khachhang SET loinhappass = 0, timekhoa = NULL WHERE id = $id");
            }
        }

        // Kiểm tra nếu tài khoản bị khóa vĩnh viễn
        if ($user['trangthai'] == 0) {
            echo "<script>alert('Tài khoản của bạn đã bị khóa!'); window.location.href='LoginSignup.html';</script>";
            exit();
        }

        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            $conn->query("UPDATE khachhang SET loinhappass = 0, timekhoa = NULL WHERE id = $id");

            $_SESSION['khachhang_id'] = $user['id']; 
            $_SESSION['khachhang_name'] = $user['namekh']; 
            
            header("Location: ../shop/index.php"); 
            exit();
        } else {
            $failed_attempts++;
            if ($failed_attempts >= 5) {
                $lock_time = date("Y-m-d H:i:s");
                $conn->query("UPDATE khachhang SET loinhappass = 5, timekhoa = '$lock_time' WHERE id = $id");
                echo "<script>alert('Bạn đã nhập sai 5 lần. Tài khoản bị khóa trong 5 phút!'); window.location.href='LoginSignup.html';</script>";
            } else {
                $conn->query("UPDATE khachhang SET loinhappass = $failed_attempts WHERE id = $id");
                echo "<script>alert('Sai mật khẩu! Bạn đã nhập sai $failed_attempts lần.'); window.location.href='LoginSignup.html';</script>";
            }
            exit();
        }
    }

    /** Xử lý đăng nhập cho ADMIN (Không có khóa 5 phút) **/
    $sql = "SELECT * FROM adminshop WHERE gmail = ? OR nameadmin = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['nameadmin'];

            header("Location: ../admin/admin.php");
            exit();
        } else {
            echo "<script>alert('Sai mật khẩu Admin!'); window.location.href='LoginSignup.html';</script>";
            exit();
        }
    }

    // Nếu không tìm thấy tài khoản
    echo "<script>alert('Email hoặc Username không tồn tại!'); window.location.href='LoginSignup.html';</script>";
    exit();
}
?>
