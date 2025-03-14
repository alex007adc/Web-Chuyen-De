<?php
session_start();
require '../config.php'; // Kết nối database

// Kiểm tra đăng nhập
if (!isset($_SESSION['khachhang_id'])) {
    die("Bạn chưa đăng nhập!");
}
$id = $_SESSION['khachhang_id'];

// Lấy thông tin khách hàng từ database
$sql = "SELECT namekh, gmail, SDT, diachi, avatar FROM khachhang WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$error = "";
$success = "";

// Xử lý khi người dùng bấm nút "Lưu Thay Đổi"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namekh = trim($_POST['namekh']);
    $gmail = trim($_POST['gmail']);
    $SDT = trim($_POST['SDT']);
    $diachi = trim($_POST['diachi']);
    
    $avatarData = null;
    if (!empty($_FILES['avatar']['tmp_name'])) {
        $avatarData = file_get_contents($_FILES['avatar']['tmp_name']);
    }

    // Kiểm tra dữ liệu hợp lệ
    if (empty($namekh) || empty($gmail) || empty($SDT) || empty($diachi)) {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } else {
        if ($avatarData) {
            // Cập nhật thông tin kèm avatar
            $sql_update = "UPDATE khachhang SET namekh=?, gmail=?, SDT=?, diachi=?, avatar=? WHERE id=?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("ssssbi", $namekh, $gmail, $SDT, $diachi, $null, $id);
            $stmt->send_long_data(4, $avatarData); // Gửi dữ liệu ảnh vào MySQL
        } else {
            // Cập nhật không có avatar
            $sql_update = "UPDATE khachhang SET namekh=?, gmail=?, SDT=?, diachi=? WHERE id=?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("ssssi", $namekh, $gmail, $SDT, $diachi, $id);
        }

        if ($stmt->execute()) {
            header("Location: thongtinkhachhang.php");
        } else {
            $error = "Lỗi SQL: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cập Nhật Thông Tin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Cập Nhật Thông Tin</h2>
        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

        <form method="post" enctype="multipart/form-data" class="mx-auto mt-4" style="max-width: 500px;">
            <div class="text-center">
                <img src="showavatar.php?id=<?= $id ?>" class="rounded-circle" alt="Avatar" width="150" height="150">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Họ và Tên</label>
                <input type="text" class="form-control" name="namekh" value="<?= htmlspecialchars($user['namekh']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="gmail" value="<?= htmlspecialchars($user['gmail']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Số Điện Thoại</label>
                <input type="text" class="form-control" name="SDT" value="<?= htmlspecialchars($user['SDT']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Địa Chỉ</label>
                <input type="text" class="form-control" name="diachi" value="<?= htmlspecialchars($user['diachi']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ảnh Đại Diện</label>
                <input type="file" class="form-control" name="avatar" accept="image/*">
            </div>
            
            <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
            <a href="thongtinkhachhang.php" class="btn btn-secondary">Quay Lại</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
