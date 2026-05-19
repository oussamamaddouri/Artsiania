<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // --- NEW: Get the role from the form ---
    $role = $_POST['role'];

    // --- NEW: Create a list of allowed roles for public registration ---
    $allowed_roles = ['client', 'artisan', 'livreur'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error_message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Invalid email format.';
    } elseif (!in_array($role, $allowed_roles)) {
        // Security check: ensure the role is not 'admin' or something malicious
        $error_message = 'Invalid role selected.';
    } else {
        $db = getDbConnection();

        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error_message = 'Username or email already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // --- NEW: Use the selected role in the INSERT query ---
            $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $hashed_password, $role])) {
                $_SESSION['success_message'] = 'Registration successful! You can now log in.';
                header('Location: login.php');
                exit();
            } else {
                $error_message = 'Registration failed. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/../src/views/templates/header.php';
?>

<div class="container" style="max-width: 500px; margin-top: 5rem;">
    <h2 class="text-center mb-4 section-title">Create Your Account</h2>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        
        <!-- === NEW: ROLE SELECTION DROPDOWN === -->
        <div class="mb-3">
            <label for="role" class="form-label">I am a...</label>
            <select class="form-select" id="role" name="role" required>
                <option value="client">Client (I want to buy products)</option>
                <option value="artisan">Artisan (I want to sell products)</option>
                <option value="livreur">Livreur (I want to deliver orders)</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-custom w-100">Register</button>
    </form>
    <div class="text-center mt-3">
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>