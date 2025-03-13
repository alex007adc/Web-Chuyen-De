<?php
require '../config.php';

// Kiểm tra ID đơn hàng
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Không tìm thấy đơn hàng!");
}

$id = intval($_GET['id']);

// Lấy thông tin đơn hàng
$sql = "SELECT * FROM donhang WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$donhang = $stmt->get_result()->fetch_assoc();

if (!$donhang) {
    die("Đơn hàng không tồn tại!");
}

// Xuất hóa đơn
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=hoadon_$id.xls");
echo "Hóa đơn đơn hàng #$id\n";
echo "Khách hàng: " . $donhang['khachhang_id'] . "\n";
echo "Ngày đặt: " . $donhang['ngaydat'] . "\n";
echo "Tổng tiền: " . number_format($donhang['tongtien'], 0, ',', '.') . " VND\n";
?>
