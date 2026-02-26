<?php
session_start();
session_destroy(); // Clears all session data
header("Location: index.php"); // Send them back to the login page
exit();
?>