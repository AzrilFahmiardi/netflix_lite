<!DOCTYPE html>
<html>
<head>
    <title>Netflix Lite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #141414;
            color: #fff;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar styles */
        .sidebar {
            width: 250px;
            background-color: #0a0a0a;
            padding: 20px 0;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            transition: all 0.3s ease;
            border-right: 1px solid #333;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #333;
            text-align: center;
        }
        
        .sidebar-header h2 {
            color: #e50914;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            color: #999;
            font-size: 14px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-title {
            padding: 10px 20px;
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li {
            padding: 5px 20px;
            margin: 5px 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .sidebar-menu li:hover, .sidebar-menu li.active {
            background-color: rgba(229, 9, 20, 0.1);
            border-left: 4px solid #e50914;
        }
        
        .sidebar-menu a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main content styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #333;
        }
        
        .header h1 {
            color: #e50914;
            font-size: 28px;
        }
        
        /* Tabs styling */
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #333;
        }
        
        .tab-button {
            background: none;
            border: none;
            padding: 10px 20px;
            color: #999;
            cursor: pointer;
            margin-right: 10px;
            font-size: 16px;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .tab-button:hover {
            color: #fff;
        }
        
        .tab-button.active {
            color: #e50914;
            border-bottom-color: #e50914;
        }
        
        /* Content styling */
        .content {
            padding: 20px 0;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Card styling */
        .card {
            background-color: #222;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #333;
        }
        
        .card-header h3 {
            color: #e50914;
            font-size: 18px;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #333 0%, #222 100%);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card i {
            font-size: 32px;
            color: #e50914;
            margin-bottom: 15px;
        }
        
        .stat-card h3 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .stat-card p {
            color: #999;
            font-size: 14px;
        }
        
        /* Form styling */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-section {
            background-color: #333;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .form-section h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #e50914;
            font-size: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid #444;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #ccc;
            font-size: 14px;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 10px;
            background-color: #222;
            border: 1px solid #444;
            border-radius: 4px;
            color: #fff;
            font-size: 14px;
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #e50914;
        }
        
        button {
            padding: 10px 20px;
            background-color: #e50914;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        button:hover {
            background-color: #f40612;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #444;
        }
        
        .btn-secondary:hover {
            background-color: #555;
        }
        
        /* Table styling */
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background-color: #333;
            color: #fff;
            text-align: left;
            padding: 12px;
            font-size: 14px;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #333;
            font-size: 14px;
        }
        
        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .action-links a {
            display: inline-block;
            margin-right: 10px;
            color: #e50914;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .action-links a:hover {
            color: #f40612;
            transform: translateY(-2px);
        }
        
        /* Alerts */
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .success {
            background-color: rgba(40, 167, 69, 0.2);
            border: 1px solid rgba(40, 167, 69, 0.5);
            color: #28a745;
        }
        
        .error {
            background-color: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #dc3545;
        }

        /* Responsive design */
        @media (max-width: 991px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h2, .sidebar-header p, .menu-title {
                display: none;
            }
            
            .sidebar-menu a span {
                display: none;
            }
            
            .sidebar-menu i {
                margin-right: 0;
                font-size: 20px;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Netflix Lite</h2>
            <p>Admin Dashboard</p>
        </div>
        
        <div class="sidebar-menu">
            <p class="menu-title">Main</p>
            <ul>
                <li class="active"><a href="#" onclick="showTab('dashboard')"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li><a href="#" onclick="showTab('users')"><i class="fas fa-users"></i> <span>Users</span></a></li>
                <li><a href="#" onclick="showTab('films')"><i class="fas fa-film"></i> <span>Films</span></a></li>
                <li><a href="#" onclick="showTab('reviews')"><i class="fas fa-star"></i> <span>Reviews</span></a></li>
            </ul>
            
            <p class="menu-title">Data Management</p>
            <ul>
                <li><a href="#" onclick="showTab('add-data')"><i class="fas fa-plus-circle"></i> <span>Add Data</span></a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main content area -->
    <div class="main-content">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <div class="user-info">
                <i class="fas fa-user-shield"></i> Admin
            </div>
        </div>
        
        <!-- Database connection and data retrieval -->
        <?php
        $host = "localhost";
        $user = "root";
        $pass = "12345678";
        $db   = "netflix_lite";

        $conn = new mysqli($host, $user, $pass, $db);
        
        // Display connection status message
        $connectionMessage = "";
        if ($conn->connect_error) {
            $connectionMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Connection failed: " . $conn->connect_error . "</div>";
        } else {
            $connectionMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Connection successful!</div>";
        }

        // Process form submissions
        $formMessage = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Add User
            if (isset($_POST['add_user'])) {
                $id = $_POST['user_id'];
                $name = $_POST['user_name'];
                $sql = "INSERT INTO users (id, name) VALUES ('$id', '$name')";
                
                if ($conn->query($sql) === TRUE) {
                    $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> User added successfully!</div>";
                } else {
                    $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error: " . $conn->error . "</div>";
                }
            }
            
            // Add Film
            if (isset($_POST['add_film'])) {
                $id = $_POST['film_id'];
                $title = $_POST['film_title'];
                $duration = $_POST['film_duration'];
                $sql = "INSERT INTO films (id, title, duration) VALUES ('$id', '$title', '$duration')";
                
                if ($conn->query($sql) === TRUE) {
                    $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Film added successfully!</div>";
                } else {
                    $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error: " . $conn->error . "</div>";
                }
            }
            
            // Add Review (updated to remove ID)
            if (isset($_POST['add_review'])) {
                $film_id = $_POST['film_id_select'];
                $user_id = $_POST['user_id_select'];
                $review = $_POST['review_text'];
                $sql = "INSERT INTO reviews (film_id, user_id, review) VALUES ('$film_id', '$user_id', '$review')";
                
                if ($conn->query($sql) === TRUE) {
                    $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Review added successfully!</div>";
                } else {
                    $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error: " . $conn->error . "</div>";
                }
            }

            // Update User
            if (isset($_POST['update_user'])) {
                $id = $_POST['user_id'];
                $name = $_POST['user_name'];
                $sql = "UPDATE users SET name='$name' WHERE id='$id'";
                
                if ($conn->query($sql) === TRUE) {
                    $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> User updated successfully!</div>";
                } else {
                    $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error updating user: " . $conn->error . "</div>";
                }
            }

            // Update Film
            if (isset($_POST['update_film'])) {
                $id = $_POST['film_id'];
                $title = $_POST['film_title'];
                $duration = $_POST['film_duration'];
                $sql = "UPDATE films SET title='$title', duration='$duration' WHERE id='$id'";
                
                if ($conn->query($sql) === TRUE) {
                    $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Film updated successfully!</div>";
                } else {
                    $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error updating film: " . $conn->error . "</div>";
                }
            }
        }

        // Delete operations
        if (isset($_GET['action']) && $_GET['action'] == 'delete') {
            if (isset($_GET['type']) && isset($_GET['id'])) {
                $id = $_GET['id'];
                $type = $_GET['type'];
                
                switch ($type) {
                    case 'user':
                        // First delete related reviews
                        $sql = "DELETE FROM reviews WHERE user_id = '$id'";
                        $conn->query($sql);
                        
                        // Then delete the user
                        $sql = "DELETE FROM users WHERE id = '$id'";
                        if ($conn->query($sql) === TRUE) {
                            $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> User deleted successfully!</div>";
                        } else {
                            $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error deleting user: " . $conn->error . "</div>";
                        }
                        break;
                    
                    case 'film':
                        // First delete related reviews
                        $sql = "DELETE FROM reviews WHERE film_id = '$id'";
                        $conn->query($sql);
                        
                        // Then delete the film
                        $sql = "DELETE FROM films WHERE id = '$id'";
                        if ($conn->query($sql) === TRUE) {
                            $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Film deleted successfully!</div>";
                        } else {
                            $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error deleting film: " . $conn->error . "</div>";
                        }
                        break;
                        
                    case 'review':
                        $sql = "DELETE FROM reviews WHERE id = '$id'";
                        if ($conn->query($sql) === TRUE) {
                            $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Review deleted successfully!</div>";
                        } else {
                            $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error deleting review: " . $conn->error . "</div>";
                        }
                        break;
                }
            }
        }

        // Edit form logic
        $edit_user = null;
        $edit_film = null;
        
        if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            if (isset($_GET['type']) && isset($_GET['id'])) {
                $id = $_GET['id'];
                $type = $_GET['type'];
                
                if ($type == 'user') {
                    $sql = "SELECT * FROM users WHERE id = '$id'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $edit_user = $result->fetch_assoc();
                    }
                } elseif ($type == 'film') {
                    $sql = "SELECT * FROM films WHERE id = '$id'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $edit_film = $result->fetch_assoc();
                    }
                }
            }
        }

        // DATA 
        $sql = "SELECT * FROM users";
        $users = $conn->query($sql);
        $usersCount = $users->num_rows;

        $sql = "SELECT * FROM films";
        $films = $conn->query($sql);
        $filmsCount = $films->num_rows;

        $sql = "SELECT r.id, f.title, u.name, r.review 
                    FROM reviews r
                    JOIN films f ON f.id = r.film_id
                    JOIN users u ON u.id = r.user_id";
        $reviews = $conn->query($sql);
        $reviewsCount = $reviews->num_rows;
        ?>

        <!-- Display connection and form messages -->
        <?php echo $connectionMessage; ?>
        <?php echo $formMessage; ?>
        
        <!-- Tab content -->
        <div class="content">
            <!-- Dashboard Tab -->
            <div id="dashboard" class="tab-content active">
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <h3><?php echo $usersCount; ?></h3>
                        <p>Total Users</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fas fa-film"></i>
                        <h3><?php echo $filmsCount; ?></h3>
                        <p>Total Films</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fas fa-star"></i>
                        <h3><?php echo $reviewsCount; ?></h3>
                        <p>Total Reviews</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Recent Reviews</h3>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Film</th>
                                    <th>User</th>
                                    <th>Review</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $recentReviews = $conn->query("SELECT r.id, f.title, u.name, r.review 
                                                             FROM reviews r
                                                             JOIN films f ON f.id = r.film_id
                                                             JOIN users u ON u.id = r.user_id
                                                             ORDER BY r.id DESC LIMIT 5");
                                
                                if ($recentReviews->num_rows > 0):
                                    while($row = $recentReviews->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['title'] ?></td>
                                            <td><?= $row['name'] ?></td>
                                            <td><?= $row['review'] ?></td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="3">No reviews found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Users Tab -->
            <div id="users" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>User Management</h3>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Re-execute the query to get fresh data
                                $users = $conn->query("SELECT * FROM users");
                                
                                if ($users->num_rows > 0):
                                    while($row = $users->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= $row['name'] ?></td>
                                            <td class="action-links">
                                                <a href="index.php?action=edit&type=user&id=<?= $row['id'] ?>#users"><i class="fas fa-edit"></i> Edit</a>
                                                <a href="index.php?action=delete&type=user&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user? All related reviews will also be deleted.')"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="3">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php if ($edit_user): ?>
                    <div class="card">
                        <div class="card-header">
                            <h3>Edit User</h3>
                        </div>
                        <form method="post" action="index.php#users">
                            <div class="form-group">
                                <label for="user_id">ID User:</label>
                                <input type="text" id="user_id" name="user_id" value="<?= $edit_user['id'] ?>" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="user_name">Name:</label>
                                <input type="text" id="user_name" name="user_name" value="<?= $edit_user['name'] ?>" required>
                            </div>
                            
                            <button type="submit" name="update_user">Update User</button>
                            <a href="index.php#users"><button type="button" class="btn-secondary">Cancel</button></a>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Films Tab -->
            <div id="films" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Film Management</h3>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Duration (mins)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Re-execute the query to get fresh data
                                $films = $conn->query("SELECT * FROM films");
                                
                                if ($films->num_rows > 0):
                                    while($row = $films->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= $row['title'] ?></td>
                                            <td><?= $row['duration'] ?></td>
                                            <td class="action-links">
                                                <a href="index.php?action=edit&type=film&id=<?= $row['id'] ?>#films"><i class="fas fa-edit"></i> Edit</a>
                                                <a href="index.php?action=delete&type=film&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this film? All related reviews will also be deleted.')"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="4">No films found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php if ($edit_film): ?>
                    <div class="card">
                        <div class="card-header">
                            <h3>Edit Film</h3>
                        </div>
                        <form method="post" action="index.php#films">
                            <div class="form-group">
                                <label for="film_id">ID Film:</label>
                                <input type="text" id="film_id" name="film_id" value="<?= $edit_film['id'] ?>" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="film_title">Title:</label>
                                <input type="text" id="film_title" name="film_title" value="<?= $edit_film['title'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="film_duration">Duration (mins):</label>
                                <input type="number" id="film_duration" name="film_duration" value="<?= $edit_film['duration'] ?>" required>
                            </div>
                            
                            <button type="submit" name="update_film">Update Film</button>
                            <a href="index.php#films"><button type="button" class="btn-secondary">Cancel</button></a>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Reviews Tab -->
            <div id="reviews" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Review Management</h3>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Film</th>
                                    <th>User</th>
                                    <th>Review</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Re-execute the query to get fresh data
                                $reviews = $conn->query("SELECT r.id, f.title, u.name, r.review 
                                                      FROM reviews r
                                                      JOIN films f ON f.id = r.film_id
                                                      JOIN users u ON u.id = r.user_id");
                                
                                if ($reviews->num_rows > 0):
                                    while($row = $reviews->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= $row['title'] ?></td>
                                            <td><?= $row['name'] ?></td>
                                            <td><?= $row['review'] ?></td>
                                            <td class="action-links">
                                                <a href="index.php?action=delete&type=review&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this review?')"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="5">No reviews found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Add Data Tab -->
            <div id="add-data" class="tab-content">
                <div class="form-grid">
                    <!-- User Form -->
                    <div class="form-section">
                        <h3><i class="fas fa-user-plus"></i> Add New User</h3>
                        <form method="post" action="index.php#add-data">
                            <div class="form-group">
                                <label for="user_id">ID User:</label>
                                <input type="text" id="user_id" name="user_id" placeholder="Enter user ID" required>
                            </div>
                            <div class="form-group">
                                <label for="user_name">Name:</label>
                                <input type="text" id="user_name" name="user_name" placeholder="Enter user name" required>
                            </div>
                            
                            <button type="submit" name="add_user"><i class="fas fa-plus-circle"></i> Add User</button>
                        </form>
                    </div>
                    
                    <!-- Film Form -->
                    <div class="form-section">
                        <h3><i class="fas fa-film"></i> Add New Film</h3>
                        <form method="post" action="index.php#add-data">
                            <div class="form-group">
                                <label for="film_id">ID Film:</label>
                                <input type="text" id="film_id" name="film_id" placeholder="Enter film ID" required>
                            </div>
                            <div class="form-group">
                                <label for="film_title">Title:</label>
                                <input type="text" id="film_title" name="film_title" placeholder="Enter film title" required>
                            </div>
                            <div class="form-group">
                                <label for="film_duration">Duration (mins):</label>
                                <input type="number" id="film_duration" name="film_duration" placeholder="Enter duration in minutes" required>
                            </div>
                            
                            <button type="submit" name="add_film"><i class="fas fa-plus-circle"></i> Add Film</button>
                        </form>
                    </div>
                </div>
                
                <!-- Review Form (updated to remove ID field) -->
                <div class="form-section">
                    <h3><i class="fas fa-star"></i> Add New Review</h3>
                    <form method="post" action="index.php#add-data">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="film_id_select">Film:</label>
                                <select id="film_id_select" name="film_id_select" required>
                                    <option value="">Select Film</option>
                                    <?php 
                                    $film_options = $conn->query("SELECT id, title FROM films");
                                    while ($film = $film_options->fetch_assoc()): ?>
                                        <option value="<?= $film['id'] ?>"><?= $film['title'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="user_id_select">User:</label>
                                <select id="user_id_select" name="user_id_select" required>
                                    <option value="">Select User</option>
                                    <?php 
                                    $user_options = $conn->query("SELECT id, name FROM users");
                                    while ($user = $user_options->fetch_assoc()): ?>
                                        <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="review_text">Review:</label>
                            <textarea id="review_text" name="review_text" rows="4" placeholder="Write review here" required></textarea>
                        </div>
                        
                        <button type="submit" name="add_review"><i class="fas fa-plus-circle"></i> Add Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Tab switching functionality
        function showTab(tabId) {
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show the selected tab content
            document.getElementById(tabId).classList.add('active');
            
            // Update active state in sidebar
            document.querySelectorAll('.sidebar-menu li').forEach(item => {
                item.classList.remove('active');
            });
            
            // Find the clicked menu item and make it active
            event.currentTarget.parentElement.classList.add('active');
            
            // Update URL hash for proper navigation
            window.location.hash = tabId;
        }
        
        // Check URL hash on page load to show the correct tab
        window.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash.replace('#', '');
            if (hash && document.getElementById(hash)) {
                showTab(hash);
                
                // Find and activate the correct sidebar item
                document.querySelectorAll('.sidebar-menu li').forEach(item => {
                    const link = item.querySelector('a');
                    if (link && link.getAttribute('onclick').includes(hash)) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>
