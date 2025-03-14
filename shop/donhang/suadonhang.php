<?php
require '../config.php'; // Kết nối CSDL

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Không tìm thấy đơn hàng!");
}

$id = intval($_GET['id']);

// Lấy thông tin đơn hàng
$sql = "SELECT trangthai FROM donhang WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$donhang = $stmt->get_result()->fetch_assoc();

if (!$donhang) {
    die("Đơn hàng không tồn tại!");
}

// Cập nhật trạng thái đơn hàng
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trangthai_moi = $_POST['trangthai'];

    $sql_update = "UPDATE donhang SET trangthai = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $trangthai_moi, $id);

    if ($stmt_update->execute()) {
        echo "<script>alert('Cập nhật thành công!'); window.location='donhang.php';</script>";
    } else {
        echo "Lỗi SQL: " . $stmt_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa trạng thái đơn hàng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Sửa trạng thái đơn hàng #<?= $id; ?></h2>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select class="form-select" name="trangthai">
                    <option value="Chờ xác nhận" <?= ($donhang['trangthai'] == "Chờ xác nhận") ? 'selected' : ''; ?>>Chờ xác nhận</option>
                    <option value="Đã xác nhận" <?= ($donhang['trangthai'] == "Đã xác nhận") ? 'selected' : ''; ?>>Đã xác nhận</option>
                    <option value="Đang giao hàng" <?= ($donhang['trangthai'] == "Đang giao hàng") ? 'selected' : ''; ?>>Đang giao hàng</option>
                    <option value="Hoàn thành" <?= ($donhang['trangthai'] == "Hoàn thành") ? 'selected' : ''; ?>>Hoàn thành</option>
                    <option value="Đã hủy" <?= ($donhang['trangthai'] == "Đã hủy") ? 'selected' : ''; ?>>Đã hủy</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="donhang.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>
</html>
