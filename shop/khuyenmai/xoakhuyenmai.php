<?php
include('../config.php'); // Kết nối với cơ sở dữ liệu

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Kiểm tra kết nối cơ sở dữ liệu
    if (!$conn) {
        die("Kết nối cơ sở dữ liệu không thành công: " . mysqli_connect_error());
    }

    // Kiểm tra xem ID có tồn tại trong cơ sở dữ liệu không
    $check_sql = "SELECT COUNT(*) FROM khuyenmai WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count == 0) {
        echo "Chương trình khuyến mãi không tồn tại!";
        exit();
    }

    // Xóa chương trình khuyến mãi
    $sql = "DELETE FROM khuyenmai WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Lỗi khi chuẩn bị câu lệnh SQL: " . $conn->error;
    } else {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Sau khi xóa thành công, chuyển hướng đến trang danh sách khuyến mãi
            header("Location: khuyenmai.php");
            exit(); // Đảm bảo quá trình thực thi dừng lại tại đây
        } else {
            echo "Lỗi khi xóa chương trình khuyến mãi: " . $stmt->error;
        }
    }

    // Đóng statement và kết nối
    $stmt->close();
    $conn->close();
}
?>
