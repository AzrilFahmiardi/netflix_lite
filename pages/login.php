<?php
// Start session
session_start();

// Include database connection
require_once('../config/database.php');

$error = '';

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Check if fields are empty
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, password, first_name, last_name FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['first_name'] . " " . $user['last_name'];
                $_SESSION['logged_in'] = true;
                
                // Update last login time (using created_at instead of last_login)
                // Check if last_login column exists in the table
                $checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'last_login'");
                if ($checkColumn->num_rows > 0) {
                    // If column exists, update it
                    $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $update_stmt->bind_param("i", $user['id']);
                    $update_stmt->execute();
                } else {
                    // If column doesn't exist, we can update created_at instead or skip this step
                    // $conn->query("ALTER TABLE users ADD COLUMN last_login DATETIME");
                }
                
                // Redirect to browse page
                header("Location: browse.php");
                exit;
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "User not found";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StreamFlix</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7 col-sm-9">
                    <div class="auth-card">
                        <div class="text-center">
                            <a href="../index.php" class="navbar-brand auth-brand">
                                <i class="fas fa-play-circle"></i> StreamFlix
                            </a>
                            <h1 class="auth-title">Welcome Back</h1>
                            <p class="auth-subtitle">Sign in to continue to StreamFlix</p>
                        </div>
                        
                        <?php if($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="login.php" method="post">
                            <div class="mb-4">
                                <label for="username" class="form-label">Username or Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required autofocus placeholder="Enter your username or email">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between">
                                    <label for="password" class="form-label">Password</label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                                    <span class="input-group-text" onclick="togglePassword()" style="cursor:pointer;width:50px;">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary-gradient btn-lg btn-auth">
                                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center auth-footer">
                            <p class="text-light m-0">Don't have an account? <a href="register.php" class="auth-link">Create Account</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
