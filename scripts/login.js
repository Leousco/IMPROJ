console.log("JS Loaded");

function togglePassword(fieldId, icon) {
    const input = document.getElementById(fieldId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("login-form");

    form.addEventListener("submit", function (event) {
        console.log("Submit intercepted");
        event.preventDefault();

        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Logging in...';

        const formData = new FormData(form);

        fetch("/IMPROJ/backend/controllers/LoginController.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            console.log("Response:", data);

            if (data.success) {
                window.location.href = "/IMPROJ/views/landing_page.php"; 
            } else {
                alert(data.message);
                btn.disabled = false;
                btn.innerHTML = 'Log In';
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = 'Log In';
        });
    });
});
