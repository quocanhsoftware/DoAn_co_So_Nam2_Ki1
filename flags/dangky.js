document.addEventListener('DOMContentLoaded', function() {
    // 1. Nút đóng
    const closeButton = document.querySelector('.close-button');
    closeButton.addEventListener('click', function() {
        alert('Đóng form đăng ký');
    });

   // 2️⃣ Captcha ngẫu nhiên
function generateCaptcha() {
    const chars = '0123456789';
    let captcha = '';
    for (let i = 0; i < 4; i++) {
        captcha += chars[Math.floor(Math.random() * chars.length)];
    }
    document.getElementById('captcha').textContent = captcha;
}
const refreshCaptchaBtn = document.getElementById('refreshCaptcha');
refreshCaptchaBtn.addEventListener('click', generateCaptcha);
generateCaptcha();

// 3️⃣ Dropdown quốc gia (chỉ hiển thị tên quốc gia)
// Fetch quốc gia từ DB
fetch('get_countries.php')
.then(res => res.json())
.then(data => {
    // 1️⃣ Dropdown quốc gia cho số điện thoại (hiển thị cờ + mã)
    // 1️⃣ Dropdown quốc gia cho số điện thoại (hiển thị cờ + mã, tự đóng sau khi chọn)
const phoneCountry = document.getElementById('phoneCountry');
const phoneMenu = document.createElement('div');
phoneMenu.className = 'select-items phone-menu';

data.forEach(c => {
    const option = document.createElement('div');
    option.innerHTML = `
        <img src="${c.flag}" width="20" height="14">
        <span class="phone-option-text">${c.name} (${c.code})</span>
    `;
    option.dataset.flag = c.flag;
    option.dataset.code = c.code;

    option.addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('countryFlag').src = this.dataset.flag;
        document.getElementById('countryCode').textContent = this.dataset.code;
        // 🔻 tự đóng menu sau khi chọn
        phoneMenu.classList.remove('open');
    });

    phoneMenu.appendChild(option);
});

phoneCountry.appendChild(phoneMenu);

// Toggle mở/đóng khi click vào vùng chọn cờ
phoneCountry.addEventListener('click', (e) => {
    e.stopPropagation();
    phoneMenu.classList.toggle('open');
});

// Click ra ngoài thì đóng menu
document.addEventListener('click', () => phoneMenu.classList.remove('open'));


    // 2️⃣ Dropdown quốc gia kinh doanh (border + dropdown)
// 2️⃣ Dropdown quốc gia kinh doanh (border + dropdown, tự thu khi chọn)
const businessContainer = document.getElementById('businessCountry');
const selected = businessContainer.querySelector('.select-selected');
const arrow = businessContainer.querySelector('.arrow');
const items = businessContainer.querySelector('.select-items');

// Render danh sách quốc gia
data.forEach(c => {
    const opt = document.createElement('div');
    opt.textContent = c.name;
    opt.dataset.id = c.id;

    opt.addEventListener('click', function(e) {
        e.stopPropagation(); // tránh lan ra ngoài
        selected.textContent = this.textContent;
        selected.dataset.value = this.dataset.id;

        // 🔻 Tự thu dropdown lại ngay khi chọn
        items.classList.remove('open');

        // Load regions tương ứng
        fetch('get_regions.php?country_id=' + this.dataset.id)
            .then(res => res.json())
            .then(rData => {
                const regionSelect = document.getElementById('region');
                regionSelect.innerHTML = '<option disabled selected>Chọn khu vực</option>';
                rData.forEach(r => {
                    const o = document.createElement('option');
                    o.value = r.id;
                    o.textContent = r.name;
                    regionSelect.appendChild(o);
                });
            });
    });

    items.appendChild(opt);
});

// Toggle mở/đóng khi click
businessContainer.addEventListener('click', (e) => {
    e.stopPropagation();
    items.classList.toggle('open');
});

// Click ra ngoài thì đóng dropdown
document.addEventListener('click', () => {
    items.classList.remove('open');
});

});

// 4️⃣ Submit form
const registerForm = document.getElementById('registerForm');
registerForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const fullname = document.getElementById('fullname').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const region = document.getElementById('region').value;
    const captchaInput = document.getElementById('captchaInput').value.trim();
    const captchaText = document.getElementById('captcha').textContent.trim();
    const termsAccepted = document.getElementById('terms').checked;
    const selectedCountry = document.querySelector('.select-selected');
    const country_id = selectedCountry.dataset.value;

    if (!fullname) { alert('Nhập họ tên'); return; }
    if (!phone) { alert('Nhập số điện thoại'); return; }
    if (!country_id) { alert('Quốc gia đang kinh doanh'); return; }
    if (!region) { alert('Chọn khu vực'); return; }
    if (captchaInput !== captchaText) { alert('Mã xác thực không đúng'); return; }
    if (!termsAccepted) { alert('Chưa đồng ý điều khoản'); return; }

    const formData = new FormData();
    formData.append('fullname', fullname);
    formData.append('phone', phone);
    formData.append('country', country_id);
    formData.append('region', region);

    fetch('register.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                registerForm.reset();
                generateCaptcha();
            }
        })
        .catch(err => {
            alert('Lỗi kết nối đến server.');
            console.error(err);
        });
});


});
