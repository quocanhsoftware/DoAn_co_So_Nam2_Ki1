
    const paymentMethod = document.getElementById('paymentMethod');
    const qrSection = document.getElementById('qrCodeSection');
    const qrContainer = document.getElementById('qrcode');
    const qrAmount = document.getElementById('qrAmount');
    const discountInput = document.querySelector('input[name="discount"]');
    const TOTAL_AMOUNT = window.TOTAL_AMOUNT;


    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    function getFinalAmount() {
        const discount = parseFloat(discountInput.value) || 0;
        return Math.max(0, TOTAL_AMOUNT - discount);
    }

    function renderVietQR() {
        const finalAmount = getFinalAmount();

        qrAmount.textContent = formatVND(finalAmount);
        qrContainer.innerHTML = '';

        const bankBin = '970422'; // MB Bank
        const accountNo = '1908200666888';
        const addInfo = encodeURIComponent('Thanh toán hóa đơn');

        const qrUrl = `https://img.vietqr.io/image/${bankBin}-${accountNo}-compact2.png?amount=${finalAmount}&addInfo=${addInfo}`;

        const img = document.createElement('img');
        img.src = qrUrl;
        img.style.width = '220px';
        img.style.height = '220px';

        qrContainer.appendChild(img);
    }

    function handlePaymentChange() {
        if (paymentMethod.value === 'bank') {
            qrSection.style.display = 'block';
            renderVietQR();
        } else {
            qrSection.style.display = 'none';
            qrContainer.innerHTML = '';
        }
    }

    paymentMethod.addEventListener('change', handlePaymentChange);

    discountInput.addEventListener('input', () => {
        if (paymentMethod.value === 'bank') {
            renderVietQR();
        }
    });

    document.addEventListener('DOMContentLoaded', handlePaymentChange);
