<?php
// Pastikan tidak ada output sebelum session_start()
session_start();

// Hancurkan semua data session
$_SESSION = [];

// Hapus cookie session secara menyeluruh
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect dengan header anti-cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location: admin.php?logout=1"); // Redirect ke halaman login
exit();
?>