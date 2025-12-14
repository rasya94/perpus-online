<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'online_library');

define('BORROW_LIMIT', 5);
define('BORROW_DAYS', 14); // 2 minggu

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>