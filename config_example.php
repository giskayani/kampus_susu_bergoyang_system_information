<?php
// Database configuration
// Copy this file to config.php and fill in your local database details.

define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');     // <-- e.g., 'root'
define('DB_PASS', 'your_db_password'); // <-- e.g., '' or 'your_password'
define('DB_NAME', 'kampus_susu');  // This is the required database name

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Koneksi database gagal: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    date_default_timezone_set('Asia/Jakarta');
    
} catch (Exception $e) {
    error_log($e->getMessage());
    die("Terjadi kesalahan sistem. Pastikan Anda telah membuat 'config.php' dan database 'kampus_susu' ada.");
}