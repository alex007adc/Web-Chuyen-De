<?php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['gmail']);
    $password = trim($_POST['password']);

    // Kiểm tra điều kiện hợp lệ cho thông tin nhập vào
    if (empty($name) || empty($email) || empty($password)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!'); window.location.href='LoginSignup.html';</script>";
        exit();
    }

    // Kiểm tra định dạng email hợp lệ
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email không hợp lệ!'); window.location.href='LoginSignup.html';</script>";
        exit();
    }

    // Kiểm tra độ mạnh của mật khẩu
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
        echo "<script>alert('Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt!'); window.location.href='LoginSignup.html';</script>";
        exit();
    }

    // Mã hóa mật khẩu trước khi lưu vào database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Kiểm tra email đã tồn tại chưa
    $check_sql = "SELECT * FROM khachhang WHERE gmail = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Nếu chưa tồn tại, thêm mới tài khoản
        $sql = "INSERT INTO khachhang (namekh, gmail, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Đăng ký thành công!'); window.location.href='LoginSignup.html';</script>";
        } else {
            echo "<script>alert('Lỗi khi đăng ký!');</script>";
        }
    } else {
        echo "<script>alert('Email đã tồn tại!'); window.location.href='LoginSignup.html';</script>";
    }
}
?>
