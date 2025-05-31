<?php
// Include session check
require_once('../components/session_check.php');

// Require user to be logged in
requireLogin();

// Include database connection
require_once('../config/database.php');

// Get user ID from session
$user_id = getCurrentUserId();

// Get user details from database
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get user's review count
$sql = "SELECT COUNT(*) as review_count FROM user_reviews WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$review_count = $result->fetch_assoc()['review_count'];

// Handle profile update
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $country = trim($_POST['country']);
    
    // Check if email is already in use by another user
    $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $email, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $message = 'Email already in use by another account';
        $message_type = 'error';
    } else {
        // Update profile
        $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, country = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssi", $first_name, $last_name, $email, $country, $user_id);
        
        if ($update_stmt->execute()) {
            $message = 'Profile updated successfully';
            $message_type = 'success';
            
            // Update session name
            $_SESSION['name'] = $first_name . ' ' . $last_name;
            
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        } else {
            $message = 'Error updating profile';
            $message_type = 'error';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        // Check if new passwords match
        if ($new_password === $confirm_password) {
            // Check password length
            if (strlen($new_password) >= 6) {
                // Hash new password and update
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($update_stmt->execute()) {
                    $message = 'Password changed successfully';
                    $message_type = 'success';
                } else {
                    $message = 'Error changing password';
                    $message_type = 'error';
                }
            } else {
                $message = 'New password must be at least 6 characters';
                $message_type = 'error';
            }
        } else {
            $message = 'New passwords do not match';
            $message_type = 'error';
        }
    } else {
        $message = 'Current password is incorrect';
        $message_type = 'error';
    }
}

// Recent activity: Get user's recent reviews
$sql = "SELECT r.*, m.title, m.poster_url, m.trailer_youtube_id
        FROM user_reviews r
        JOIN movies m ON r.movie_id = m.id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
        LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_reviews = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - StreamFlix</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .profile-section {
            background-color: #1a1a3e;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(108, 92, 231, 0.2);
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            margin-right: 20px;
        }
        
        .profile-info h1 {
            margin-bottom: 5px;
            font-size: 24px;
        }
        
        .profile-info p {
            margin-bottom: 0;
            color: #b2b2b2;
        }
        
        .profile-stats {
            display: flex;
            margin-top: 10px;
        }
        
        .stat-item {
            margin-right: 20px;
            text-align: center;
        }
        
        .stat-item .number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 0;
        }
        
        .stat-item .label {
            color: #b2b2b2;
            font-size: 14px;
        }
        
        .nav-pills .nav-link {
            color: #fff;
            border-radius: 8px;
            padding: 10px 20px;
        }
        
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        }
        
        .form-label {
            color: #ccc;
        }
        
        .activity-card {
            background-color: rgba(26, 26, 62, 0.5);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(108, 92, 231, 0.1);
        }
        
        .activity-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .activity-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .activity-movie-poster {
            width: 60px;
            height: 80px;
            border-radius: 8px;
            margin-right: 15px;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .activity-timestamp {
            color: #b2b2b2;
            font-size: 12px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <?php include_once('../components/navbar.php'); ?>
    
    <div class="container mt-5 pt-5">
        <!-- Display messages if any -->
        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="profile-section">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?= substr($user['first_name'], 0, 1) ?>
                </div>
                <div class="profile-info">
                    <h1><?= $user['first_name'] . ' ' . $user['last_name'] ?></h1>
                    <p>@<?= $user['username'] ?> • <?= $user['country'] ?></p>
                    <p>Member since <?= date('F Y', strtotime($user['created_at'])) ?></p>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <p class="number"><?= $review_count ?></p>
                            <p class="label">Reviews</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="profile-section">
                    <ul class="nav nav-pills mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" type="button" role="tab">Account Settings</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">Security</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="profileTabsContent">
                        <div class="tab-pane fade show active" id="account" role="tabpanel" aria-labelledby="account-tab">
                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" value="<?= $user['username'] ?>" readonly disabled>
                                    <div class="form-text text-muted">Username cannot be changed</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= $user['first_name'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= $user['last_name'] ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" value="<?= $user['country'] ?>">
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn btn-primary-gradient">Save Changes</button>
                            </form>
                        </div>
                        
                        <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <div class="form-text text-muted">Password must be at least 6 characters long</div>
                                </div>
                                
                                <button type="submit" name="change_password" class="btn btn-primary-gradient">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="profile-section">
                    <h4 class="mb-4">Recent Activity</h4>
                    
                    <?php if ($recent_reviews && $recent_reviews->num_rows > 0): ?>
                        <?php while ($review = $recent_reviews->fetch_assoc()): 
                            $posterUrl = !empty($review['poster_url']) ? 
                                (strpos($review['poster_url'], 'http') === 0 ? $review['poster_url'] : '../' . $review['poster_url']) : 
                                'https://img.youtube.com/vi/' . $review['trailer_youtube_id'] . '/mqdefault.jpg';
                        ?>
                            <div class="activity-card">
                                <div class="activity-header">
                                    <img src="<?= $posterUrl ?>" class="activity-movie-poster" alt="<?= $review['title'] ?>">
                                    <div class="activity-content">
                                        <h5 class="activity-title"><?= $review['title'] ?></h5>
                                        <p class="activity-timestamp">
                                            <i class="far fa-clock me-1"></i> 
                                            <?= date('M d, Y', strtotime($review['created_at'])) ?>
                                        </p>
                                        <div>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $review['rating']): ?>
                                                    <i class="fas fa-star text-warning"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star text-muted"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-2 mb-0"><?= $review['review_text'] ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x mb-3 text-muted"></i>
                            <p class="text-muted">No recent reviews</p>
                            <a href="browse.php" class="btn btn-sm btn-primary-gradient mt-2">Discover Movies to Review</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="profile-section">
                    <h4 class="mb-3">Account Status</h4>
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-auto">Membership Status</div>
                        <span class="badge bg-success">Active</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-auto">Account Type</div>
                        <span class="badge bg-primary">Standard</span>
                    </div>
                    <p class="text-muted mb-0 small">Your account is in good standing</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include_once('../components/footer.php'); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
