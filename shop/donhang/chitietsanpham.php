<?php
session_start();
require '../config.php'; // Kết nối database

// Kiểm tra nếu không có ID sản phẩm
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Lấy thông tin sản phẩm
$sql_binhluan = "SELECT db.noidung, db.sao, db.thoigian, k.namekh 
                 FROM danhgia_binhluan db
                 JOIN khachhang k ON db.khachhang = k.id
                 WHERE db.sanpham = ? AND db.duyet = 1
                 ORDER BY db.thoigian DESC";
$stmt_binhluan = $conn->prepare($sql_binhluan);
$stmt_binhluan->bind_param("i", $id);
$stmt_binhluan->execute();
$binhluan_result = $stmt_binhluan->get_result();



if ($product_result->num_rows == 0) {
    echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='index.php';</script>";
    exit();
}

$product = $product_result->fetch_assoc();

// Lấy danh sách bình luận
$sql_binhluan = "SELECT b.noidung, k.namekh, b.ngaybinhluan 
                 FROM binhluan b
                 JOIN khachhang k ON b.user_id = k.id
                 WHERE b.sanpham_id = ?
                 ORDER BY b.ngaybinhluan DESC";
$stmt_binhluan = $conn->prepare($sql_binhluan);
$stmt_binhluan->bind_param("i", $id);
$stmt_binhluan->execute();
$binhluan_result = $stmt_binhluan->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết sản phẩm - <?= htmlspecialchars($product['tensp']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<!-- Header -->
<header class="bg-dark py-5">
    <div class="container text-center text-white">
        <h1 class="display-4 fw-bolder">Chi tiết sản phẩm</h1>
    </div>
</header>

<!-- Chi tiết sản phẩm -->
<div class="mt-5">
    <h3>Đánh giá & Bình luận</h3>

    <?php if ($binhluan_result->num_rows > 0): ?>
        <?php while ($bl = $binhluan_result->fetch_assoc()): ?>
            <div class="border p-3 my-2">
                <strong><?= htmlspecialchars($bl['namekh']); ?></strong>
                <span class="text-muted">(<?= $bl['thoigian']; ?>)</span>
                <p>⭐ <?= str_repeat('⭐', $bl['sao']); ?> (<?= $bl['sao']; ?> sao)</p>
                <p><?= nl2br(htmlspecialchars($bl['noidung'])); ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
    <?php endif; ?>
</div>


    <!-- Bình luận -->
    <div class="mt-5">
        <h3>Bình luận</h3>

        <!-- Hiển thị bình luận -->
        <?php while ($bl = $binhluan_result->fetch_assoc()): ?>
            <div class="border p-3 my-2">
                <strong><?= htmlspecialchars($bl['namekh']); ?></strong>
                <span class="text-muted">(<?= $bl['ngaybinhluan']; ?>)</span>
                <p><?= nl2br(htmlspecialchars($bl['noidung'])); ?></p>
            </div>
        <?php endwhile; ?>

        <!-- Form nhập bình luận -->
        <?php if (isset($_SESSION['khachhang_id'])): ?>
            <form action="xuly_binhluan.php" method="POST">
                <input type="hidden" name="sanpham_id" value="<?= $id; ?>">
                <textarea name="noidung" class="form-control" placeholder="Nhập bình luận..." required></textarea>
                <button type="submit" class="btn btn-success mt-2">Gửi bình luận</button>
            </form>
        <?php else: ?>
            <p><a href="../LoginSignup/LoginSignup.html">Đăng nhập để bình luận</a></p>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer class="py-4 bg-dark text-white text-center">
    <p class="m-0">Bản Quyền &copy; Cửa Hàng Nam 2025</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
