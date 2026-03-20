<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <script>
        // Clear all client-side storage
        localStorage.clear();
        sessionStorage.clear();
        // Redirect to login page
        window.location.href = "login.php";
    </script>
</head>
<body>
    <p>Logging out...</p>
</body>
</html>