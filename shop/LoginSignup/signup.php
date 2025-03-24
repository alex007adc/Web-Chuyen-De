<?php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['gmail']);
    $password = trim($_POST['password']);
    $sdt = trim($_POST['SDT']); // Đổi 'sdt' thành 'SDT' để khớp với name trong form
    $diachi = trim($_POST['diachi']);

    // Kiểm tra điều kiện hợp lệ
    if (empty($name) || empty($email) || empty($password) || empty($sdt) || empty($diachi)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!'); window.location.href='LoginSignup.html';</script>";
        exit();
    }

    // Kiểm tra định dạng email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email không hợp lệ!'); window.location.href='LoginSignup.html';</script>";
        exit();
    }

    // Kiểm tra số điện thoại (chỉ chứa số, 9 chữ số)
    if (!preg_match('/^[0-9]{9}$/', $sdt)) {
        echo "<script>alert('Số điện thoại không hợp lệ! Vui lòng nhập 10 hoặc 11 số.'); window.location.href='LoginSignup.html';</script>";
        exit();
    }

    // Kiểm tra độ mạnh của mật khẩu
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || 
        !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
        echo "<script>alert('Mật khẩu phải có ít nhất 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt!'); window.location.href='LoginSignup.html';</script>";
        exit();
    }

    // Mã hóa mật khẩu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Kiểm tra email đã tồn tại chưa
    $check_sql = "SELECT * FROM khachhang WHERE gmail = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Thêm tài khoản mới
        $sql = "INSERT INTO khachhang (namekh, gmail, password, sdt, diachi) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $hashed_password, $sdt, $diachi);

        if ($stmt->execute()) {
            echo "<script>alert('Đăng ký thành công!'); window.location.href='LoginSignup.html';</script>";
        } else {
            echo "<script>alert('Lỗi khi đăng ký! Vui lòng thử lại.');</script>";
        }
    } else {
        echo "<script>alert('Email đã tồn tại!'); window.location.href='LoginSignup.html';</script>";
    }
}
?>
