document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.querySelector('input[name="password"]');
    const toggle = document.querySelector('.toggle-password');
    const eyeOpen = toggle.querySelector('.eye-open');
    const eyeClose = toggle.querySelector('.eye-close');

    toggle.addEventListener("click", () => {
        const isHidden = passwordInput.type === "password";
        passwordInput.type = isHidden ? "text" : "password";

        // Ẩn/hiện icon
        eyeOpen.style.display = isHidden ? "none" : "block";
        eyeClose.style.display = isHidden ? "block" : "none";
    });
});
