/*!
* Start Bootstrap - Shop Homepage v5.0.6 (https://startbootstrap.com/template/shop-homepage)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-shop-homepage/blob/master/LICENSE)
*/
// This file is intentionally blank
// Use this file to add JavaScript to your project

document.getElementById("searchForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Ngăn tải lại trang

    let searchQuery = document.getElementById("searchInput").value.trim();

    // Gửi request AJAX đến server
    fetch("../timkiem/timkiem.php?query=" + encodeURIComponent(searchQuery))
        .then(response => response.json())
        .then(data => {
            let productList = document.getElementById("productList");
            productList.innerHTML = ""; // Xóa danh sách cũ

            if (data.length === 0) {
                // Nếu không có sản phẩm, hiển thị tất cả sản phẩm
                fetch("../timkiem/timkiem.php")
                    .then(response => response.json())
                    .then(allProducts => renderProducts(allProducts));
            } else {
                renderProducts(data);
            }
        })
        .catch(error => console.error("Lỗi khi tìm kiếm sản phẩm:", error));
});

function renderProducts(products) {
    let productList = document.getElementById("productList");
    productList.innerHTML = ""; // Xóa danh sách cũ

    products.forEach(product => {
        let productHTML = `
            <div class="col mb-5">
                <div class="card h-100 position-relative">
                    <img src="${product.hinhanh}" class="card-img-top">
                    <div class="card-body p-4 text-center">
                        <h5 class="fw-bolder">${product.tensp}</h5>
                        <p>Danh Mục: <strong>${product.tendanhmuc}</strong></p>
                        <p class="text-secondary small">
                            ${product.soluong > 0 
                                ? `Còn lại: <strong class="text-success">${product.soluong}</strong> sản phẩm`
                                : `<strong class="text-danger">Hết hàng</strong>`
                            }
                        </p>
                        ${product.khuyenmai > 0 
                            ? `<span class="text-muted text-decoration-line-through">${product.gia} VNĐ</span>
                               <strong class="text-danger">${product.gia_khuyenmai} VNĐ</strong>`
                            : `<strong>${product.gia} VNĐ</strong>`
                        }
                    </div>
                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent text-center">
                        <a class="btn btn-outline-dark mt-auto" href="../donhang/chitietsanpham.php?id=${product.id}">Xem Chi Tiết</a>
                    </div>
                </div>
            </div>
        `;
        productList.innerHTML += productHTML;
    });
}