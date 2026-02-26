<?php
session_start();

// Redirect back to login if they try to access this page without logging in
if (!isset($_SESSION['authenticated'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <style>
        body { 
            background-color: #121212; 
            color: white; 
            font-family: sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
        }
        .container { 
            border: 1px solid #444; 
            padding: 50px; 
            border-radius: 10px; 
            text-align: center; 
            width: 400px;
        }
        h1 { color: #28a745; margin-bottom: 10px; }
        h3 { color: #bbb; margin-bottom: 30px; }
        .logout-btn { 
            text-decoration: none; 
            color: #ff4444; 
            border: 1px solid #ff4444; 
            padding: 10px 20px; 
            border-radius: 5px; 
        }
        .logout-btn:hover { background: #ff4444; color: white; }
    </style>
</head>
<body>

    <div class="container">
        <h1>HOME PAGE</h1>
        <hr style="border: 0.5px solid #444; margin: 20px 0;">
        
        <h3>WELCOME: <?php echo $_SESSION['authenticated']; ?></h3>
        
        <br><br>
        <a href="logout.php" class="logout-btn">LOGOUT</a>
    </div>

</body>
</html>