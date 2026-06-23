<?php
$config_file = __DIR__ . '/db_config.php';
if (file_exists($config_file)) {
    require_once $config_file;
} else {
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_USER')) define('DB_USER', 'root');
    if (!defined('DB_PASS')) define('DB_PASS', '1234');
    if (!defined('DB_NAME')) define('DB_NAME', 'oim');
}

/* CREATE CONNECTION */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

/* CHECK CONNECTION */
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>