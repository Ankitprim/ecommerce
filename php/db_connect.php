<?php
// Database connection details
$host = 'localhost';
$db_name = 'ecommerce_db';
$username = 'root';
$password = ''; // Use your MySQL root password (often empty in XAMPP)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Start the session on every page that includes this file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>