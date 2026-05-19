<?php
// Include the main configuration for credentials
require_once __DIR__ . '/config.php';

// A simple function to connect to the database using PDO
function getDbConnection() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        // For a real app, log this error instead of displaying it
        die("Database connection failed: " . $e->getMessage());
    }
}
?>
