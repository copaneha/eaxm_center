<?php
// 1. Session start karein
session_start();

// 2. Saare session variables ko khali (unset) karein
$_SESSION = array();

// 3. Agar session cookie exist karti hai toh use bhi delete karein
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Session ko puri tarah destroy karein
session_destroy();

// 5. User ko login page par redirect karein
header("Location: dashboard.php");
exit();
?>