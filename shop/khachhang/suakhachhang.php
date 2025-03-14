<?php
include '../config.php';

// Lấy danh sách loại khách hàng
$loaikhach_sql = "SELECT makh, ten_loai FROM loaikhachhang";
$loaikhach_result = $conn->query($loaikhach_sql);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM khachhang WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $khachhang = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $makh = trim($_POST['makh']);  // Chọn từ dropdown
    $name = trim($_POST['namekh']);
    $email = trim($_POST['gmail']);
    $sdt = trim($_POST['SDT']);
    $diachi = trim($_POST['diachi']);

    $update_sql = "UPDATE khachhang SET makh = ?, namekh = ?, gmail = ?, SDT = ?, diachi = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssssi", $makh, $name, $email, $sdt, $diachi, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='khachhang.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Chỉnh sửa khách hàng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Chỉnh sửa thông tin khách hàng</h2>
        <form method="POST">
            <label>Loại khách hàng:</label>
            <select name="makh" class="form-control">
                <?php while ($row = $loaikhach_result->fetch_assoc()) { ?>
                    <option value="<?php echo $row['makh']; ?>" <?php echo ($row['makh'] == $khachhang['makh']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['ten_loai']); ?>
                    </option>
                <?php } ?>
            </select>

            <label>Họ tên:</label>
            <input type="text" name="namekh" class="form-control" value="<?php echo htmlspecialchars($khachhang['namekh']); ?>" required>

            <label>Email:</label>
            <input type="email" name="gmail" class="form-control" value="<?php echo htmlspecialchars($khachhang['gmail']); ?>" required>

            <label>Số điện thoại:</label>
            <input type="text" name="SDT" class="form-control" value="<?php echo htmlspecialchars($khachhang['SDT']); ?>" required>

            <label>Địa chỉ:</label>
            <input type="text" name="diachi" class="form-control" value="<?php echo htmlspecialchars($khachhang['diachi']); ?>" required>

            <button type="submit" class="btn btn-primary mt-3">Lưu thay đổi</button>
        </form>
    </div>
</body>
</html>
