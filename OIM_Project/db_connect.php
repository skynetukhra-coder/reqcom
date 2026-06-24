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

/* CREATE CONNECTION WITH DEBUGGING */
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
} catch (Exception $e) {
    echo "CONNECTION_ERROR: " . $e->getMessage();
    exit();
}

/* SET CONNECTION CHARSET AND COLLATION TO PREVENT MIXED COLLATION ERRORS */
$conn->set_charset("utf8mb4");
$conn->query("SET collation_connection = utf8mb4_general_ci");

/* CHECK CONNECTION */
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>