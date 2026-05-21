<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Database Config with Docker Fallbacks
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_USER', getenv('DB_USER') ?: 'artisania_user');
define('DB_PASS', getenv('DB_PASS') ?: 'secure_password_here');
define('DB_NAME', getenv('DB_NAME') ?: 'artisania_db');

// Root of the project (one level up from this file)
define('BASE_PATH', dirname(__DIR__)); 

define('SITE_NAME', 'Carthage Caverne');

// Handle BASE_URL for both Vercel and local Docker
if (getenv('VERCEL_URL')) {
    define('BASE_URL', 'https://' . getenv('VERCEL_URL'));
} else {
    // In Docker local, your domain root is the "api" folder
    define('BASE_URL', 'http://localhost:8080');
}