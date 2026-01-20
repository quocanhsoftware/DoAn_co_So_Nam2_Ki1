// ============================
// KHAI BÁO TOÀN CỤC
// ============================
let employeeDataMap = {}; // Lưu trữ dữ liệu nhân viên theo ID
const defaultAvatar = "../picture/default_user.jpg"; 

// ============================
// Load Employees from Server
// ============================
function loadEmployees() {
    const search = document.getElementById("searchEmployee").value;
    const status = document.querySelector('input[name="employeeStatus"]:checked').value;
    const department = document.getElementById("departmentSelect").value;
    const position = document.getElementById("positionSelect").value;

    const url = `../Controller/get_employee.php?search=${encodeURIComponent(search)}&status=${status}&department=${department}&position=${position}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById("employeeTableBody");
            tbody.innerHTML = "";
            employeeDataMap = {}; // Reset map khi load lại

            // Nếu không có nhân viên
            if (!data.data || data.data.length === 0) {
                // Colspan = 9 (8 cột dữ liệu + 1 cột Thao tác)
                tbody.innerHTML = ` 
                    <tr class="empty-state">
                        <td colspan="9" class="py-5 text-center">
                            <i class="bi bi-person-lines-fill display-6 text-muted d-block mb-3"></i>
                            <p class="mb-1 fw-semibold">Gian hàng chưa có nhân viên.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            // Render bảng
            data.data.forEach(emp => {
                // LƯU DỮ LIỆU VÀO MAP
                employeeDataMap[emp.id] = emp; 
                
                const avatar = emp.avatar ? `../uploads/${emp.avatar}` : defaultAvatar;

                tbody.innerHTML += `
                    <tr>
                       <td><input type="checkbox" class="row-checkbox"></td>
                        <td><img src="${avatar}" class="rounded-circle" width="40" height="40"></td>
                        <td class="employee-id" data-id="${emp.id}">${emp.employee_code}</td>
                       
                        <td>${emp.fullname}</td>
                        <td>${emp.phone}</td> 
                        <td>${emp.password}</td>
                        <td>${emp.cccd}</td>
                       
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-btn" data-id="${emp.id}" title="Sửa">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(error => console.error("Lỗi load nhân viên:", error));
}

// ============================
// Gắn sự kiện Lọc & Tìm kiếm
// ============================
document.getElementById("searchEmployee").addEventListener("keyup", loadEmployees);
document.getElementById("departmentSelect").addEventListener("change", loadEmployees);
document.getElementById("positionSelect").addEventListener("change", loadEmployees);
document.querySelectorAll('input[name="employeeStatus"]').forEach(radio => {
    radio.addEventListener("change", loadEmployees);
});

// ============================
// Logic cho Modal Sửa nhân viên (Tái sử dụng Add Modal)
// ============================

// 1. Gắn Event Delegation để bắt sự kiện click nút Sửa
document.getElementById("employeeTableBody").addEventListener('click', function (e) {
    const editButton = e.target.closest('.edit-btn');
    if (editButton) {
        const employeeId = editButton.getAttribute('data-id');
        fillEditForm(employeeId); // Chạy hàm sửa
    }
    // Đã loại bỏ logic delete-one-btn ở đây
});


// 2. Hàm điền dữ liệu vào form Sửa
function fillEditForm(employeeId) {
    const emp = employeeDataMap[employeeId];
    const modalElement = document.getElementById('addEmployeeModal');
    
    if (!emp || !modalElement) {
        alert('Lỗi: Không tìm thấy dữ liệu nhân viên.');
        return;
    }

    const modal = new bootstrap.Modal(modalElement);
    const form = document.getElementById('addEmployeeForm');

    // a. Cập nhật tiêu đề Modal và hành động Form
    document.getElementById('addEmployeeLabel').textContent = 'Sửa thông tin Nhân viên: ' + emp.fullname;
    form.action = '../Controller/update_employee.php'; // ĐỔI ACTION SANG UPDATE
    
    // b. Điền dữ liệu vào form
    document.getElementById('employeeIdInput').value = emp.id; // GẮN ID NHÂN VIÊN VÀO TRƯỜNG ẨN
    document.getElementById('employeeCode').value = emp.employee_code || '';
    document.getElementById('password').value = emp.password || '';
    document.getElementById('fullname').value = emp.fullname || '';
    document.getElementById('phone').value = emp.phone || '';
    document.getElementById('cccd').value = emp.cccd || '';
    
    // Selects
    document.getElementById('department').value = emp.department_id || '';
    document.getElementById('position').value = emp.position_id || '';

    // c. Xử lý ảnh đại diện
    const photoPreview = document.getElementById('employeePhotoPreview');
    const defaultSrc = photoPreview.getAttribute("data-default-src");
    photoPreview.src = emp.avatar ? `../uploads/${emp.avatar}` : defaultSrc; 

    // d. Hiển thị modal
    modal.show();
}

// 3. Reset form về trạng thái THÊM khi Modal đóng
document.getElementById('addEmployeeModal').addEventListener('hidden.bs.modal', function (event) {
    const form = document.getElementById('addEmployeeForm');
    
    // Nếu form đang ở trạng thái UPDATE, reset về trạng thái ADD
    if (form.action.includes('update_employee.php')) {
        document.getElementById('addEmployeeLabel').textContent = 'Thêm nhân viên';
        form.action = '../Controller/add_employee.php'; // Trở lại action thêm mặc định
        document.getElementById('employeeIdInput').value = ''; // Xoá ID nhân viên
        form.reset();
        
        // Reset ảnh preview
        const photoPreview = document.getElementById('employeePhotoPreview');
        photoPreview.src = photoPreview.getAttribute("data-default-src");
    }
});


// ============================
// Xử lý Submit Form (Cho cả Thêm và Sửa)
// ============================
document.getElementById("addEmployeeForm").addEventListener("submit", function (e) {
    e.preventDefault(); 

    const form = e.target;
    const formData = new FormData(form);

    fetch(form.action, { // SỬ DỤNG form.action ĐÃ THAY ĐỔI
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert(data.message);
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('addEmployeeModal'));
            modal.hide();
            
            loadEmployees(); // Tải lại danh sách nhân viên
            
        } else {
            alert("Lỗi: " + data.message);
        }
    })
    .catch(err => {
        console.error("Lỗi xử lý form nhân viên:", err);
        alert("Đã xảy ra lỗi trong quá trình xử lý form.");
    });
});

// ============================
// Xử lý Ảnh Preview
// ============================
const photoInput = document.getElementById("employeePhoto");
const photoPreview = document.getElementById("employeePhotoPreview");

if (photoInput && photoPreview) {
    photoInput.addEventListener("change", function () {
        const file = photoInput.files && photoInput.files[0];
        const defaultSrc = photoPreview.getAttribute("data-default-src");

        if (!file) {
            photoPreview.src = defaultSrc;
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            photoPreview.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });
}


// ============================
// Xử lý Xóa (Chuyển trạng thái nghỉ) HÀNG LOẠT
// ============================

// Khối code này chạy khi nút #deleteSelectedBtn được bấm
const deleteSelectedBtn = document.getElementById("deleteSelectedBtn");
if (deleteSelectedBtn) {
    deleteSelectedBtn.addEventListener("click", function () {
        const selectedCheckboxes = document.querySelectorAll(".row-checkbox:checked");

        if (!selectedCheckboxes.length) {
            alert("Vui lòng chọn nhân viên để chuyển trạng thái nghỉ.");
            return;
        }

        const ids = Array.from(selectedCheckboxes).map(cb => {
            return cb.closest("tr").querySelector(".employee-id").dataset.id;
        });

        if (!confirm(`Bạn có chắc chắn muốn chuyển ${ids.length} nhân viên sang trạng thái đã nghỉ không?`)) return;

        fetch("../Controller/change_status_employee.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ ids })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                alert("Chuyển trạng thái thành công!");
                loadEmployees(); // reload bảng
            } else {
                alert("Lỗi: " + data.message);
            }
        })
        .catch(err => console.log(err));
    });
}


// ============================
// Logic Checkbox Select All
// ============================
document.getElementById("selectAll").addEventListener("change", function () {
    const isChecked = this.checked;
    const checkboxes = document.querySelectorAll(".row-checkbox");

    checkboxes.forEach(cb => cb.checked = isChecked);
});

document.addEventListener("change", function (e) {
    if (e.target.classList.contains("row-checkbox")) {
        const all = document.querySelectorAll(".row-checkbox");
        const checked = document.querySelectorAll(".row-checkbox:checked");
        const selectAll = document.getElementById("selectAll");

        selectAll.checked = (all.length === checked.length);
    }
});


// ============================
// Load lần đầu
// ============================
document.addEventListener("DOMContentLoaded", () => {
    // Đảm bảo action mặc định là add_employee.php
    document.getElementById('addEmployeeForm').action = '../Controller/add_employee.php';
    loadEmployees();
});