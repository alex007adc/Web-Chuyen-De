<?php
session_start();
require '../config.php'; // Kết nối database

// Hàm chuyển chuỗi thành không dấu
function convert_vi_to_en($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $transliteration = [
        'à'=>'a', 'á'=>'a', 'ạ'=>'a', 'ả'=>'a', 'ã'=>'a',
        'â'=>'a', 'ầ'=>'a', 'ấ'=>'a', 'ậ'=>'a', 'ẩ'=>'a', 'ẫ'=>'a',
        'ă'=>'a', 'ằ'=>'a', 'ắ'=>'a', 'ặ'=>'a', 'ẳ'=>'a', 'ẵ'=>'a',
        'è'=>'e', 'é'=>'e', 'ẹ'=>'e', 'ẻ'=>'e', 'ẽ'=>'e',
        'ê'=>'e', 'ề'=>'e', 'ế'=>'e', 'ệ'=>'e', 'ể'=>'e', 'ễ'=>'e',
        'ì'=>'i', 'í'=>'i', 'ị'=>'i', 'ỉ'=>'i', 'ĩ'=>'i',
        'ò'=>'o', 'ó'=>'o', 'ọ'=>'o', 'ỏ'=>'o', 'õ'=>'o',
        'ô'=>'o', 'ồ'=>'o', 'ố'=>'o', 'ộ'=>'o', 'ổ'=>'o', 'ỗ'=>'o',
        'ơ'=>'o', 'ờ'=>'o', 'ớ'=>'o', 'ợ'=>'o', 'ở'=>'o', 'ỡ'=>'o',
        'ù'=>'u', 'ú'=>'u', 'ụ'=>'u', 'ủ'=>'u', 'ũ'=>'u',
        'ư'=>'u', 'ừ'=>'u', 'ứ'=>'u', 'ự'=>'u', 'ử'=>'u', 'ữ'=>'u',
        'ỳ'=>'y', 'ý'=>'y', 'ỵ'=>'y', 'ỷ'=>'y', 'ỹ'=>'y',
        'đ'=>'d'
    ];
    return strtr($str, $transliteration);
}

// Nhận dữ liệu từ GET request
$search = isset($_GET['query']) ? trim($_GET['query']) : '';
$danhmuc = isset($_GET['danhmuc']) ? trim($_GET['danhmuc']) : '';
$khuyenmai = isset($_GET['khuyenmai']) ? $_GET['khuyenmai'] : '';

// Chuyển chuỗi thành không dấu
$search_no_accents = convert_vi_to_en($search);
$danhmuc_no_accents = convert_vi_to_en($danhmuc);

// Tạo câu SQL động
$sql = "SELECT * FROM sanpham WHERE 1=1";
$params = [];
$types = "";

// Nếu có từ khóa tìm kiếm
if (!empty($search)) {
    $sql .= " AND (LOWER(tensp) LIKE LOWER(?) OR LOWER(mota) LIKE LOWER(?))";
    $params[] = "%$search_no_accents%";
    $params[] = "%$search_no_accents%";
    $types .= "ss";
}

// Nếu có danh mục tìm kiếm
if (!empty($danhmuc)) {
    $sql .= " AND LOWER(tendanhmuc) LIKE LOWER(?)";
    $params[] = "%$danhmuc_no_accents%";
    $types .= "s";
}

// Nếu có lọc khuyến mãi
if ($khuyenmai !== '') {
    $sql .= ($khuyenmai == "0") ? " AND khuyenmai = 0" : " AND khuyenmai > 0";
}

// Chuẩn bị và thực hiện truy vấn
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Xử lý dữ liệu trả về
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = [
        'id' => $row['id'],
        'tensp' => $row['tensp'],
        'hinhanh' => "../img/img.php?id=" . $row['id'],
        'tendanhmuc' => $row['tendanhmuc'],
        'soluong' => $row['soluong'],
        'gia' => number_format($row['gia'], 0, ',', '.'),
        'khuyenmai' => $row['khuyenmai'],
        'gia_khuyenmai' => ($row['khuyenmai'] > 0) 
            ? number_format($row['gia'] - ($row['gia'] * $row['khuyenmai'] / 100), 0, ',', '.') 
            : null
    ];
}

// Đóng kết nối
$stmt->close();
$conn->close();

// Trả về JSON
header("Content-Type: application/json");
echo json_encode($products);
