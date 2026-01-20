const form = document.getElementById("shopForm");

form.addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append("user_id", document.getElementById("user_id").value);
    formData.append("nameshop", document.getElementById("nameshop").value.trim());
    formData.append("password", document.getElementById("password").value.trim());

    fetch("../Controller/save_shop.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Tạo shop thành công!");
            window.location.href = "index.php";
        } else {
            alert(data.message);
        }
    });
});
