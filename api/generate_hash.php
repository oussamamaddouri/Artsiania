<?php
$password = 'admin123';

// Generate a new password hash using the recommended default algorithm
$newHash = password_hash($password, PASSWORD_DEFAULT);

echo "<h1>New Password Hash Generator</h1>";
echo "<p><strong>Password:</strong> " . htmlspecialchars($password) . "</p>";
echo "<hr>";
echo "<p>Copy the entire hash below and use it in the UPDATE query.</p>";
echo "<p><strong>New Generated Hash:</strong></p>";
echo "<pre style='font-size:1.2rem; background-color:#f0f0f0; padding:10px; border:1px solid #ccc;'>" . htmlspecialchars($newHash) . "</pre>";
?>
