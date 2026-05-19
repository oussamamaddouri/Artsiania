<?php
require_once __DIR__ . '/../config/db.php';

try {
    $db = getDbConnection();
    
    // The new hash you just generated for 'admin123'
    $new_hash = '$2y$10$aQjWswsPNx.xKSR2AiP3E.pPILKLZI5pSxysQ64iH6XbXw1hfHtm2';
    $email = 'admin@artisania.com';

    $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
    
    if ($stmt->execute([$new_hash, $email])) {
        echo "<h1>Success!</h1><p>Admin password has been updated. You can now login with <strong>admin123</strong>.</p>";
        echo "<a href='login.php'>Go to Login Page</a>";
    } else {
        echo "Failed to update password.";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
