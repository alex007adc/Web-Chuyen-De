<?php
// Kết nối database
require '../config.php';

// Kiểm tra có ID sản phẩm không
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Truy vấn lấy ảnh từ database
    $sql = "SELECT hinhanh FROM sanpham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    // Kiểm tra ảnh có tồn tại không
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($imageData);
        $stmt->fetch();

        // Hiển thị ảnh
        header("Content-Type: image/jpeg"); // hoặc image/png nếu là PNG
        echo $imageData;
    } else {
        // Hiển thị ảnh mặc định nếu không có
        header("Content-Type: image/png");
        readfile("no-image.png"); // Đặt một ảnh mặc định trong thư mục
    }
} else {
    echo "Không tìm thấy ảnh!";
}
?>
