function updateDateTime() {
    const now = new Date();

    // Lấy ngày, tháng, năm
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0'); // Tháng từ 0-11
    const year = now.getFullYear();

    // Lấy giờ, phút, giây
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
   

    // Ghép chuỗi hiển thị
    const dateTimeString = `${day}/${month}/${year} ${hours}:${minutes}`;

    // Cập nhật vào thẻ div
    document.getElementById('dateTime').textContent = dateTimeString;
}

// Cập nhật ngay khi load trang
updateDateTime();

// Cập nhật mỗi giây
setInterval(updateDateTime, 1000);
