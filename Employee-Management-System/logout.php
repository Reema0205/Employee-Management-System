<?php
/**
 * logout.php
 * Destroys the session and sends the user back to the login page.
 * This is what the "↩ Logout" link in the dashboard sidebar calls.
 */
session_start();
$_SESSION = [];
session_unset();
session_destroy();

// Also clear the session cookie in the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

header("Location: login.html");
exit;
?>
