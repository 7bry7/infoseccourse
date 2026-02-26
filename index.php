<!DOCTYPE html>
<html>
<head>
    <title>Lab Activity 02</title>
    <style>
        body { background: #121212; color: white; font-family: sans-serif; display: flex; justify-content: center; padding-top: 50px; }
        .card { border: 1px solid #444; padding: 20px; width: 300px; text-align: center; border-radius: 10px; }
        input { width: 90%; padding: 10px; margin: 10px 0; background: #222; border: 1px solid #555; color: white; }
        input:disabled { background: #111; color: #666; cursor: not-allowed; }
        button { width: 97%; padding: 10px; background: #28a745; border: none; color: white; cursor: pointer; margin-top: 10px; }
        button:disabled { background: #1e5a2d; cursor: not-allowed; }
        #error-msg { color: #ff4444; font-size: 0.8em; margin-top: 10px; }
    </style>
</head>
<body>

<div class="card">
    <h2>LOGIN PAGE</h2>
    <input type="text" id="username" placeholder="USERNAME">
    <input type="password" id="password" placeholder="PASSWORD">
    <button id="loginBtn" onclick="processLogin()">LOGIN</button>

    <hr style="margin: 20px 0; border: 0.5px solid #444;">

    <input type="text" id="otp_input" placeholder="ENTER OTP" disabled>
    <button id="validateBtn" onclick="processOTP()" disabled>VALIDATE</button>

    <p id="error-msg"></p>
</div>

<script>
    async function processLogin() {
        const user = document.getElementById('username').value;
        const pass = document.getElementById('password').value;
        const errorMsg = document.getElementById('error-msg');

        // Send data to PHP without reloading
        const response = await fetch('backend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=login&username=${user}&password=${pass}`
        });

        const data = await response.json();

        if (data.status === 'success') {
            alert("OTP Generated: " + data.otp); // You see the OTP here
            document.getElementById('otp_input').disabled = false;
            document.getElementById('validateBtn').disabled = false;
            errorMsg.innerText = "";
        } else {
            errorMsg.innerText = data.message;
        }
    }

    async function processOTP() {
        const otp = document.getElementById('otp_input').value;
        
        const response = await fetch('backend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=verify&otp=${otp}`
        });

        const data = await response.json();

        if (data.status === 'success') {
            window.location.href = 'home.php'; // Redirect on success
        } else {
            document.getElementById('error-msg').innerText = data.message;
        }
    }
</script>
</body>
</html>