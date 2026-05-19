<?php

// The plain text password we are testing
$password = 'admin123';

// The exact hash from your database for the admin user
$hash = '$2y$10$gO6hM7f0SAxR.ApveLX5fOvy2EKBW.Yxoc.C239N/35dIeA4f8hWC';

echo "<h1>Password Verification Test</h1>";
echo "<p><strong>Password to test:</strong> " . htmlspecialchars($password) . "</p>";
echo "<p><strong>Hash from database:</strong> " . htmlspecialchars($hash) . "</p>";
echo "<hr>";

if (password_verify($password, $hash)) {
    echo "<h2>RESULT: <span style='color:green;'>Verification SUCCESSFUL!</span></h2>";
    echo "<p>This means your login script should work. There might be an invisible character issue in your form input.</p>";
} else {
    echo "<h2>RESULT: <span style='color:red;'>Verification FAILED.</span></h2>";
    echo "<p>This confirms the issue is with your PHP server's hashing configuration.</p>";
}

echo "<hr>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

?>
