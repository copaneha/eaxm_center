<?php
session_start();

// 1. Saare session variables ko unset karein
$_SESSION = array();

// 2. Browser ki session cookie delete karein
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Server se session destroy karein
session_destroy();

// 4. Wapas login page par bhej dein
header("Location: index.php");
exit();
?>