let revenueChartInstance; // Khai báo biến để giữ thể hiện của biểu đồ Doanh thu

// Hàm format tiền tệ (thêm dấu phẩy phân cách hàng nghìn)
function formatCurrency(amount) {
    // Chuyển đổi thành số nguyên nếu cần thiết trước khi format
    const num = parseFloat(amount);
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(num);
}

// Hàm khởi tạo/cập nhật biểu đồ doanh thu
function updateRevenueChart(labels, data) {
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    
    // Nếu biểu đồ đã tồn tại, hủy bỏ nó trước khi tạo mới
    if (revenueChartInstance) {
        revenueChartInstance.destroy();
    }
    
    revenueChartInstance = new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: labels, // Dữ liệu labels động (ngày)
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: data, // Dữ liệu doanh thu động
                borderColor: '#0a66ff',
                backgroundColor: 'rgba(10, 102, 255, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true },
                // Đã loại bỏ cấu hình 'type: time' để sử dụng trục category mặc định, giúp biểu đồ hiển thị đúng
                x: {}
            }
        }
    });
}

// Hàm chính để tải và hiển thị dữ liệu
async function loadRevenueData() {
    const timeFilter = document.getElementById('timeFilter').value;
    // const shopFilter = document.getElementById('shopFilter').value; 

    try {
        const response = await fetch(`../Controller/get_revenue.php?time=${timeFilter}`);
        if (response.status === 401) {
             console.error("Lỗi: Người dùng chưa đăng nhập hoặc phiên hết hạn.");
             alert("Phiên làm việc hết hạn. Vui lòng đăng nhập lại.");
             window.location.href = '../View/login.php'; 
             return;
        }

        const result = await response.json();

        if (result.status === "success") {
            const { summary, chart, table } = result;

            // 1. Cập nhật Summary Cards
            document.getElementById('summaryRevenue').innerText = formatCurrency(summary.total_revenue);
            document.getElementById('summaryOrders').innerText = new Intl.NumberFormat().format(summary.total_orders);
            document.getElementById('summaryProducts').innerText = new Intl.NumberFormat().format(summary.total_products);

            // 2. Cập nhật Biểu đồ Doanh thu
            const chartLabels = chart.map(item => item.date); // Lấy ngày
            const chartData = chart.map(item => parseFloat(item.revenue)); // Lấy doanh thu
            updateRevenueChart(chartLabels, chartData);

            // 3. Cập nhật Bảng Chi tiết
            const tableBody = document.getElementById('revenueTableBody');
            tableBody.innerHTML = ''; // Xóa dữ liệu cũ
            
            table.forEach(item => {
                const row = tableBody.insertRow();
                row.insertCell(0).innerText = item.date; // Thời gian
                row.insertCell(1).innerText = new Intl.NumberFormat().format(item.order_count); // Số đơn hàng
                row.insertCell(2).innerText = formatCurrency(item.total_amount); // Doanh thu
                row.insertCell(3).innerText = formatCurrency(item.total_discount); // Giảm giá
                row.insertCell(4).innerText = formatCurrency(item.final_amount); // Doanh thu thuần
            });

        } else {
            console.error("Lỗi tải dữ liệu: " + result.message);
        }
    } catch (error) {
        console.error('Lỗi kết nối API:', error);
    }
}


// Tải dữ liệu khi trang được load lần đầu
document.addEventListener('DOMContentLoaded', loadRevenueData);