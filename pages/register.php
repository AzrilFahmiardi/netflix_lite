<?php
require_once('../config/database.php');

$error = '';
$success = '';

// Registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // user inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $country = isset($_POST['country']) ? trim($_POST['country']) : 'Indonesia';
    
    // validation
    if (empty($username) || empty($email) || empty($password) || empty($first_name) || empty($last_name)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        $check_username = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check_username->bind_param("s", $username);
        $check_username->execute();
        $check_username->store_result();
        
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();
        
        if ($check_username->num_rows > 0) {
            $error = "Username already exists";
        } elseif ($check_email->num_rows > 0) {
            $error = "Email already exists";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insert_user = $conn->prepare("INSERT INTO users (username, email, password, first_name, last_name, country, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW())");
            $insert_user->bind_param("ssssss", $username, $email, $hashed_password, $first_name, $last_name, $country);
            
            if ($insert_user->execute()) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed: " . $conn->error;
            }
        }
    }
}

// List of countries
$countries = [
    "Indonesia", "United States", "United Kingdom", "Canada", "Australia",
    "Singapore", "Malaysia", "Japan", "South Korea", "India", "Germany",
    "France", "Brazil", "Mexico", "Argentina", "South Africa", "Nigeria",
    "China", "Russia", "Netherlands", "Italy", "Spain", "Portugal", "Sweden"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - StreamFlix</title>
    
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
                <div class="col-lg-7 col-md-9">
                    <div class="auth-card">
                        <div class="text-center">
                            <a href="../index.php" class="navbar-brand auth-brand">
                                <i class="fas fa-play-circle"></i> StreamFlix
                            </a>
                            <h1 class="auth-title">Join StreamFlix Today</h1>
                            <p class="auth-subtitle">Create your account to start streaming</p>
                        </div>
                        
                        <?php if($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i> <?= $success ?>
                                <div class="mt-3">
                                    <a href="login.php" class="btn btn-primary-gradient btn-sm">
                                        <i class="fas fa-sign-in-alt me-1"></i> Log In Now
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <form action="register.php" method="post">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '' ?>" required placeholder="Your first name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>" required placeholder="Your last name">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="username" class="form-label">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                        <input type="text" class="form-control" id="username" name="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required placeholder="Choose a unique username">
                                    </div>
                                    <small class="text-light">This will be your display name on StreamFlix</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required placeholder="Your email address">
                                    </div>
                                    <small class="text-light">We'll never share your email with anyone else</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="country" class="form-label">Country</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                        <select class="form-control" id="country" name="country">
                                            <?php foreach($countries as $country): ?>
                                                <option value="<?= $country ?>" <?= (isset($_POST['country']) && $_POST['country'] == $country) || (!isset($_POST['country']) && $country == 'Indonesia') ? 'selected' : '' ?>>
                                                    <?= $country ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="password" name="password" required placeholder="Create a password">
                                            <span class="input-group-text" onclick="togglePassword('password')" style="cursor:pointer;width:50px;">
                                                <i class="fas fa-eye" id="toggleIcon1"></i>
                                            </span>
                                        </div>
                                        <small class="text-light">Must be at least 6 characters long</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-4">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                                            <span class="input-group-text" onclick="togglePassword('confirm_password')" style="cursor:pointer;width:50px;">
                                                <i class="fas fa-eye" id="toggleIcon2"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="agree_terms" required>
                                        <label class="form-check-label text-light" for="agree_terms">
                                            I agree to the <a href="#" class="auth-link">Terms of Service</a> and <a href="#" class="auth-link">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary-gradient btn-lg btn-auth">
                                        <i class="fas fa-user-plus me-2"></i> Create Account
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                        <div class="text-center auth-footer">
                            <p class="text-light m-0">Already have an account? <a href="login.php" class="auth-link">Sign In</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(fieldId === 'password' ? 'toggleIcon1' : 'toggleIcon2');
            
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
