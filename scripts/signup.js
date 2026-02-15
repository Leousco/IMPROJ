
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

function sendOtp() {
    const email = document.getElementById('email').value;
    const full_name = document.getElementById('full_name').value;
    const department = document.getElementById('department').value;
    const employee_id = document.getElementById('employee_id').value;
    const password = document.getElementById('password').value;

    const btn = document.getElementById('signup-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending OTP...';

    fetch('/IMPROJ/backend/controllers/AuthController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `email=${encodeURIComponent(email)}&full_name=${encodeURIComponent(full_name)}&department=${encodeURIComponent(department)}&employee_id=${encodeURIComponent(employee_id)}&password=${encodeURIComponent(password)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('otp-container').style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = 'Verify OTP';
            btn.onclick = verifyOtp; 
            alert('OTP sent! Check your email.');
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = 'Send OTP';
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = 'Send OTP';
    });
}


function verifyOtp() {
    const email = document.getElementById('email').value;
    const otp = document.getElementById('otp').value;

    const btn = document.getElementById('signup-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';

    fetch('/IMPROJ/backend/controllers/VerifyOtpController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `email=${encodeURIComponent(email)}&otp=${encodeURIComponent(otp)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Signup complete! You can now log in.');
            window.location.href = '/IMPROJ/views/login.php';
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = 'Verify OTP';
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = 'Verify OTP';
    });
}


document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('signup-btn');
    btn.onclick = sendOtp;
});



