<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error_message = '';
if (isset($_SESSION['success_message'])) { $success_message = $_SESSION['success_message']; unset($_SESSION['success_message']); }
if (isset($_SESSION['error_message'])) { $error_message = $_SESSION['error_message']; unset($_SESSION['error_message']); }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error_message = 'Email and password are required.';
    } else {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // === NEW: ROLE-BASED REDIRECTION LOGIC ===
            switch ($user['role']) {
                case 'admin':
                    header('Location: admin/dashboard.php');
                    break;
                case 'artisan':
                    header('Location: dashboard_artisan.php');
                    break;
                case 'livreur':
                    header('Location: dashboard_livreur.php');
                    break;
                case 'client':
                default: // Default case for clients and any other roles
                    header('Location: dashboard_client.php');
                    break;
            }
            exit();

        } else {
            $error_message = 'Invalid email or password.';
        }
    }
}

// === HTML Section ===
require_once __DIR__ . '/../src/views/templates/header.php';
?>

<div class="container" style="max-width: 500px; margin-top: 5rem;">
    <h2 class="text-center mb-4 section-title">Login</h2>
    
    <!-- Display any messages -->
    <?php if (!empty($success_message)): ?><div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>
    <?php if (!empty($error_message)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>
    
    <form action="login.php" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-custom w-100">Login</button>
    </form>
    <div class="text-center mt-3">
        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>
