<?php
$password = 'admin123';

echo "<h1>PHP Hashing Sanity Check</h1>";
echo "<p><strong>Password being tested:</strong> " . htmlspecialchars($password) . "</p>";

// Generate a brand new hash right now.
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<p><strong>Newly generated hash:</strong> " . htmlspecialchars($hash) . "</p>";
echo "<hr>";

// Immediately try to verify it.
if (password_verify($password, $hash)) {
    echo "<h2>RESULT: <span style='color:green;'>SUCCESS!</span></h2>";
    echo "<p>This proves password_hash() and password_verify() ARE working correctly on your server.</p>";
    echo "<p>The problem must be a subtle issue with the data coming from the form POST request.</p>";
} else {
    echo "<h2>RESULT: <span style='color:red;'>FAILURE.</span></h2>";
    echo "<p>This is a critical error. It means your PHP installation's cryptographic functions are not working correctly. This is a server configuration issue.</p>";
}

echo "<hr>";
echo "PHP Version: " . phpversion();
?>
