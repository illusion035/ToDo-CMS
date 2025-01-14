<?php
// Database configuration
$host = 'localhost';
$dbname = ''; // Replace with your database name
$username = ''; // Replace with your database username
$password = ''; // Replace with your database password
$charset = 'utf8mb4';

// PDO connection setup
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch data as associative array
    PDO::ATTR_EMULATE_PREPARES => false, // Use native prepared statements
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>
