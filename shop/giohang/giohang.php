<?php
session_start();
include '../config.php'; // Kết nối database

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    die("Vui lòng đăng nhập để xem giỏ hàng!");
}

$user_id = $_SESSION['user_id'];

// Lấy giỏ hàng của user
$sql = "SELECT cart.id, products.name, products.price, products.image, cart.quantity 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của bạn</title>
    <link rel="stylesheet" href="cart.css">
</head>
<body>
    <h2>🛒 Giỏ hàng của bạn</h2>
    
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Hình ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
                <th>Hành động</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><img src="<?php echo $row['image']; ?>" width="50"></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo number_format($row['price'], 0, ',', '.'); ?> VNĐ</td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo number_format($row['price'] * $row['quantity'], 0, ',', '.'); ?> VNĐ</td>
                <td>
                    <a href="remove_cart.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Xóa sản phẩm này?')"> Xóa</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <a href="checkout.php">Thanh toán</a>
    <?php else: ?>
        <p>Giỏ hàng trống!</p>
    <?php endif; ?>

</body>
</html>
