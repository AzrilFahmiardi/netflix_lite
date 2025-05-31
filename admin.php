<?php
// Include database connection
require_once('config/database.php');

// Process form submissions
$formMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add Movie with poster upload functionality
    if (isset($_POST['add_movie'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $director = $_POST['director'];
        $release_year = $_POST['release_year'];
        $duration_minutes = $_POST['duration_minutes'];
        $genre = $_POST['genre'];
        $rating = $_POST['rating'];
        $trailer_youtube_id = $_POST['trailer_youtube_id'];
        $movie_youtube_id = $_POST['movie_youtube_id'];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        // Handle poster upload
        $poster_url = '';
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['poster']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                // Create a unique filename
                $new_filename = uniqid() . '_' . str_replace(' ', '_', strtolower($title)) . '.' . $ext;
                $upload_dir = 'assets/images/posters/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['poster']['tmp_name'], $upload_path)) {
                    $poster_url = $upload_path;
                }
            }
        }
        
        $sql = "INSERT INTO movies (title, description, director, release_year, duration_minutes, genre, rating, trailer_youtube_id, movie_youtube_id, is_featured, poster_url) 
                VALUES ('$title', '$description', '$director', '$release_year', '$duration_minutes', '$genre', '$rating', '$trailer_youtube_id', '$movie_youtube_id', '$is_featured', '$poster_url')";
        
        if ($conn->query($sql) === TRUE) {
            $movie_id = $conn->insert_id;
            $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Movie added successfully!</div>";
            
            // Add genres if selected
            if (isset($_POST['genres']) && is_array($_POST['genres'])) {
                foreach ($_POST['genres'] as $genre_id) {
                    $sql = "INSERT INTO movie_genres (movie_id, genre_id) VALUES ('$movie_id', '$genre_id')";
                    $conn->query($sql);
                }
            }
        } else {
            $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error: " . $conn->error . "</div>";
        }
    }
    
    // Add Genre
    if (isset($_POST['add_genre'])) {
        $name = $_POST['genre_name'];
        $description = $_POST['genre_description'];
        
        $sql = "INSERT INTO genres (name, description) VALUES ('$name', '$description')";
        
        if ($conn->query($sql) === TRUE) {
            $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Genre added successfully!</div>";
        } else {
            $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error: " . $conn->error . "</div>";
        }
    }
    
    // Add Cast/Crew
    if (isset($_POST['add_cast'])) {
        $name = $_POST['cast_name'];
        $role = $_POST['cast_role'];
        $nationality = $_POST['nationality'];
        $bio = $_POST['bio'];
        $birth_date = $_POST['birth_date'];
        
        $sql = "INSERT INTO cast_crew (name, role, nationality, bio, birth_date) 
                VALUES ('$name', '$role', '$nationality', '$bio', '$birth_date')";
        
        if ($conn->query($sql) === TRUE) {
            $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Cast/Crew member added successfully!</div>";
        } else {
            $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error: " . $conn->error . "</div>";
        }
    }
    
    // Add Cast to Movie
    if (isset($_POST['add_movie_cast'])) {
        $movie_id = $_POST['movie_id'];
        $person_id = $_POST['person_id'];
        $role_in_movie = $_POST['role_in_movie'];
        $character_name = $_POST['character_name'];
        
        $sql = "INSERT INTO movie_cast_crew (movie_id, person_id, role_in_movie, character_name) 
                VALUES ('$movie_id', '$person_id', '$role_in_movie', '$character_name')";
        
        if ($conn->query($sql) === TRUE) {
            $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Cast/Crew added to movie successfully!</div>";
        } else {
            $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error: " . $conn->error . "</div>";
        }
    }

    // Update Movie with poster upload functionality
    if (isset($_POST['update_movie'])) {
        $id = $_POST['movie_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $director = $_POST['director'];
        $release_year = $_POST['release_year'];
        $duration_minutes = $_POST['duration_minutes'];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        // Handle poster upload
        $poster_sql = '';
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['poster']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                // Create a unique filename
                $new_filename = uniqid() . '_' . str_replace(' ', '_', strtolower($title)) . '.' . $ext;
                $upload_dir = 'assets/images/posters/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['poster']['tmp_name'], $upload_path)) {
                    // Delete the old poster if exists
                    $old_poster_query = "SELECT poster_url FROM movies WHERE id = '$id'";
                    $old_poster_result = $conn->query($old_poster_query);
                    if ($old_poster_result && $old_poster_result->num_rows > 0) {
                        $old_poster = $old_poster_result->fetch_assoc()['poster_url'];
                        if ($old_poster && file_exists($old_poster)) {
                            unlink($old_poster);
                        }
                    }
                    
                    $poster_sql = ", poster_url = '$upload_path'";
                }
            }
        }
        
        $sql = "UPDATE movies SET 
                title='$title', 
                description='$description',
                director='$director',
                release_year='$release_year',
                duration_minutes='$duration_minutes',
                is_featured='$is_featured'
                $poster_sql
                WHERE id='$id'";
        
        if ($conn->query($sql) === TRUE) {
            $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Movie updated successfully!</div>";
        } else {
            $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error updating movie: " . $conn->error . "</div>";
        }
    }
    
    // Update Genre
    if (isset($_POST['update_genre'])) {
        $id = $_POST['genre_id'];
        $name = $_POST['genre_name'];
        $description = $_POST['genre_description'];
        
        $sql = "UPDATE genres SET name='$name', description='$description' WHERE id='$id'";
        
        if ($conn->query($sql) === TRUE) {
            $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Genre updated successfully!</div>";
        } else {
            $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error updating genre: " . $conn->error . "</div>";
        }
    }
}

// Delete operations
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    if (isset($_GET['type']) && isset($_GET['id'])) {
        $id = $_GET['id'];
        $type = $_GET['type'];
        
        switch ($type) {
            case 'movie':
                // First delete related records from junction tables
                $conn->query("DELETE FROM movie_genres WHERE movie_id = '$id'");
                $conn->query("DELETE FROM movie_cast_crew WHERE movie_id = '$id'");
                $conn->query("DELETE FROM user_reviews WHERE movie_id = '$id'");
                
                // Then delete the movie
                $sql = "DELETE FROM movies WHERE id = '$id'";
                if ($conn->query($sql) === TRUE) {
                    $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Movie deleted successfully!</div>";
                } else {
                    $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error deleting movie: " . $conn->error . "</div>";
                }
                break;
                
            case 'genre':
                // Check if this genre is used in any movies
                $result = $conn->query("SELECT COUNT(*) as count FROM movie_genres WHERE genre_id = '$id'");
                $row = $result->fetch_assoc();
                
                if ($row['count'] > 0) {
                    $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Cannot delete genre: It's used in " . $row['count'] . " movies</div>";
                } else {
                    $sql = "DELETE FROM genres WHERE id = '$id'";
                    if ($conn->query($sql) === TRUE) {
                        $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Genre deleted successfully!</div>";
                    } else {
                        $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error deleting genre: " . $conn->error . "</div>";
                    }
                }
                break;
                
            case 'cast':
                // Check if this cast/crew member is used in any movies
                $result = $conn->query("SELECT COUNT(*) as count FROM movie_cast_crew WHERE person_id = '$id'");
                $row = $result->fetch_assoc();
                
                if ($row['count'] > 0) {
                    $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Cannot delete: This person appears in " . $row['count'] . " movies</div>";
                } else {
                    $sql = "DELETE FROM cast_crew WHERE id = '$id'";
                    if ($conn->query($sql) === TRUE) {
                        $formMessage = "<div class='alert success'><i class='fas fa-check-circle'></i> Cast member deleted successfully!</div>";
                    } else {
                        $formMessage = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error deleting cast member: " . $conn->error . "</div>";
                    }
                }
                break;
                
            case 'review':
                $sql = "DELETE FROM user_reviews WHERE id = '$id'";
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
$edit_movie = null;
$edit_genre = null;

if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    if (isset($_GET['type']) && isset($_GET['id'])) {
        $id = $_GET['id'];
        $type = $_GET['type'];
        
        if ($type == 'movie') {
            $sql = "SELECT * FROM movies WHERE id = '$id'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $edit_movie = $result->fetch_assoc();
            }
        } elseif ($type == 'genre') {
            $sql = "SELECT * FROM genres WHERE id = '$id'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $edit_genre = $result->fetch_assoc();
            }
        }
    }
}

// Get data for dashboard
$sql = "SELECT COUNT(*) as count FROM users";
$result = $conn->query($sql);
$usersCount = $result ? $result->fetch_assoc()['count'] : 0;

$sql = "SELECT COUNT(*) as count FROM movies";
$result = $conn->query($sql);
$moviesCount = $result ? $result->fetch_assoc()['count'] : 0;

$sql = "SELECT COUNT(*) as count FROM genres";
$result = $conn->query($sql);
$genresCount = $result ? $result->fetch_assoc()['count'] : 0;

$sql = "SELECT COUNT(*) as count FROM cast_crew";
$result = $conn->query($sql);
$castCount = $result ? $result->fetch_assoc()['count'] : 0;

$sql = "SELECT COUNT(*) as count FROM user_reviews";
$result = $conn->query($sql);
$reviewsCount = $result ? $result->fetch_assoc()['count'] : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>StreamFlix - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add this to your admin.php style section */
.poster-preview {
    border: 1px solid rgba(108, 92, 231, 0.2);
    padding: 10px;
    border-radius: 8px;
    background-color: rgba(0, 0, 0, 0.2);
    text-align: center;
}

.poster-preview img {
    max-height: 300px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    max-height: 200px;
    overflow-y: auto;
    padding: 10px;
    background: rgba(26, 26, 62, 0.8);
    border-radius: 8px;
    border: 1px solid rgba(108, 92, 231, 0.2);
}

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #0f0f23;
            color: #fff;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar styles */
        .sidebar {
            width: 250px;
            background-color: #1a1a3e;
            padding: 20px 0;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            transition: all 0.3s ease;
            border-right: 1px solid rgba(108, 92, 231, 0.2);
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(108, 92, 231, 0.2);
            text-align: center;
        }
        
        .sidebar-header h2 {
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: 800;
        }
        
        .sidebar-header p {
            color: #b2b2b2;
            font-size: 14px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-title {
            padding: 10px 20px;
            color: #b2b2b2;
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
            border-radius: 8px;
        }
        
        .sidebar-menu li:hover, .sidebar-menu li.active {
            background-color: rgba(108, 92, 231, 0.1);
            border-left: 4px solid #6c5ce7;
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
            background: #0f0f23;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(108, 92, 231, 0.2);
        }
        
        .header h1 {
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 28px;
            font-weight: 700;
        }
        
        /* Tabs styling */
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(108, 92, 231, 0.2);
        }
        
        .tab-button {
            background: none;
            border: none;
            padding: 10px 20px;
            color: #b2b2b2;
            cursor: pointer;
            margin-right: 10px;
            font-size: 16px;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            border-radius: 8px 8px 0 0;
        }
        
        .tab-button:hover {
            color: #fff;
            background: rgba(108, 92, 231, 0.1);
        }
        
        .tab-button.active {
            color: #6c5ce7;
            border-bottom-color: #6c5ce7;
            background: rgba(108, 92, 231, 0.1);
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
            background-color: #1a1a3e;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(108, 92, 231, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(108, 92, 231, 0.2);
        }
        
        .card-header h3 {
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 18px;
            font-weight: 600;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #1a1a3e 0%, #2a2a5e 100%);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
            border: 1px solid rgba(108, 92, 231, 0.1);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            border-color: rgba(108, 92, 231, 0.3);
        }
        
        .stat-card i {
            font-size: 32px;
            color: #6c5ce7;
            margin-bottom: 15px;
        }
        
        .stat-card h3 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .stat-card p {
            color: #b2b2b2;
            font-size: 14px;
        }
        
        /* Form styling */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-section {
            background-color: #1a1a3e;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 20px;
            border: 1px solid rgba(108, 92, 231, 0.1);
        }
        
        .form-section h3 {
            margin-top: 0;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(108, 92, 231, 0.2);
            font-weight: 600;
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
            background-color: rgba(26, 26, 62, 0.8);
            border: 2px solid rgba(108, 92, 231, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #6c5ce7;
        }
        
        button {
            padding: 10px 20px;
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        button:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: #444;
        }
        
        .btn-secondary:hover {
            background: #555;
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
            background-color: #1a1a3e;
            color: #fff;
            text-align: left;
            padding: 12px;
            font-size: 14px;
            border-bottom: 1px solid rgba(108, 92, 231, 0.2);
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid rgba(108, 92, 231, 0.1);
            font-size: 14px;
        }
        
        tbody tr:hover {
            background-color: rgba(108, 92, 231, 0.05);
        }
        
        .action-links a {
            display: inline-block;
            margin-right: 10px;
            color: #6c5ce7;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .action-links a:hover {
            color: #a29bfe;
            transform: translateY(-1px);
        }
        
        /* Alerts */
        .alert {
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid;
        }
        
        .success {
            background-color: rgba(40, 167, 69, 0.1);
            border-color: #28a745;
            color: #28a745;
        }
        
        .error {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: #dc3545;
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
            <h2>StreamFlix</h2>
            <p>Admin Dashboard</p>
        </div>
        
        <div class="sidebar-menu">
            <p class="menu-title">Dashboard</p>
            <ul>
                <li class="active"><a href="#" onclick="showTab('dashboard')"><i class="fas fa-tachometer-alt"></i> <span>Overview</span></a></li>
            </ul>
            
            <p class="menu-title">Content Management</p>
            <ul>
                <li><a href="#" onclick="showTab('movies')"><i class="fas fa-film"></i> <span>Movies</span></a></li>
                <li><a href="#" onclick="showTab('genres')"><i class="fas fa-tags"></i> <span>Genres</span></a></li>
                <li><a href="#" onclick="showTab('cast')"><i class="fas fa-user-tie"></i> <span>Cast & Crew</span></a></li>
                <li><a href="#" onclick="showTab('reviews')"><i class="fas fa-star"></i> <span>Reviews</span></a></li>
            </ul>
            
            <p class="menu-title">System</p>
            <ul>
                <li><a href="#" onclick="showTab('users')"><i class="fas fa-users-cog"></i> <span>User Management</span></a></li>
                <li><a href="index.php"><i class="fas fa-sign-out-alt"></i> <span>Exit Admin</span></a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main content area -->
    <div class="main-content">
        <div class="header">
            <h1>StreamFlix Admin</h1>
            <div class="user-info">
                <i class="fas fa-user-shield"></i> Admin
            </div>
        </div>
        
        <!-- Display connection and form messages -->
        <?php if ($conn->connect_error): ?>
        <div class='alert error'><i class='fas fa-exclamation-circle'></i> Connection failed: <?= $conn->connect_error ?></div>
        <?php else: ?>
        <div class='alert success'><i class='fas fa-check-circle'></i> Connected to database: <?= $db ?></div>
        <?php endif; ?>
        
        <?php echo $formMessage; ?>
        
        <!-- Tab content -->
        <div class="content">
            <!-- Dashboard Tab -->
            <div id="dashboard" class="tab-content active">
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <i class="fas fa-film"></i>
                        <h3><?php echo $moviesCount; ?></h3>
                        <p>Total Movies</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <h3><?php echo $usersCount; ?></h3>
                        <p>Registered Users</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fas fa-tags"></i>
                        <h3><?php echo $genresCount; ?></h3>
                        <p>Movie Genres</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fas fa-user-tie"></i>
                        <h3><?php echo $castCount; ?></h3>
                        <p>Cast & Crew</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Featured Movies</h3>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Year</th>
                                    <th>Director</th>
                                    <th>Rating</th>
                                    <th>Views</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $featuredMovies = $conn->query("SELECT * FROM movies WHERE is_featured = 1 ORDER BY view_count DESC LIMIT 5");
                                
                                if ($featuredMovies && $featuredMovies->num_rows > 0):
                                    while($row = $featuredMovies->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['title'] ?></td>
                                            <td><?= $row['release_year'] ?></td>
                                            <td><?= $row['director'] ?></td>
                                            <td><?= $row['rating'] ?></td>
                                            <td><?= $row['view_count'] ?></td>
                                            <td class="action-links">
                                                <a href="admin.php?action=edit&type=movie&id=<?= $row['id'] ?>#movies"><i class="fas fa-edit"></i> Edit</a>
                                            </td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="6">No featured movies found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
                                    <th>Movie</th>
                                    <th>User</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $recentReviews = $conn->query("SELECT r.id, m.title, CONCAT(u.first_name, ' ', u.last_name) as user_name, 
                                                             r.rating, r.review_text, r.created_at 
                                                             FROM user_reviews r
                                                             JOIN movies m ON m.id = r.movie_id
                                                             JOIN users u ON u.id = r.user_id
                                                             ORDER BY r.created_at DESC LIMIT 5");
                                
                                if ($recentReviews && $recentReviews->num_rows > 0):
                                    while($row = $recentReviews->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['title'] ?></td>
                                            <td><?= $row['user_name'] ?></td>
                                            <td><?= $row['rating'] ?>/5</td>
                                            <td><?= substr($row['review_text'], 0, 100) . (strlen($row['review_text']) > 100 ? '...' : '') ?></td>
                                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
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
            
            <!-- Movies Tab -->
            <div id="movies" class="tab-content">
                <div class="action-buttons mb-4">
                    <button class="btn-primary" onclick="showTab('add-movie')">
                        <i class="fas fa-plus"></i> Add New Movie
                    </button>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Movie Management</h3>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Director</th>
                                    <th>Year</th>
                                    <th>Duration</th>
                                    <th>Rating</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $movies = $conn->query("SELECT * FROM movies ORDER BY release_year DESC");
                                
                                if ($movies && $movies->num_rows > 0):
                                    while($row = $movies->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= $row['title'] ?></td>
                                            <td><?= $row['director'] ?></td>
                                            <td><?= $row['release_year'] ?></td>
                                            <td><?= $row['duration_minutes'] ?> min</td>
                                            <td><?= $row['rating'] ?></td>
                                            <td><?= $row['is_featured'] ? '<span class="badge featured">Yes</span>' : 'No' ?></td>
                                            <td class="action-links">
                                                <a href="admin.php?action=edit&type=movie&id=<?= $row['id'] ?>#movies"><i class="fas fa-edit"></i> Edit</a>
                                                <a href="admin.php?action=delete&type=movie&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this movie? All related reviews will also be deleted.')"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="8">No movies found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php if ($edit_movie): ?>
                    <div class="card">
                        <div class="card-header">
                            <h3>Edit Movie</h3>
                        </div>
                        <form method="post" action="admin.php#movies" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="movie_id">ID Movie:</label>
                                <input type="text" id="movie_id" name="movie_id" value="<?= $edit_movie['id'] ?>" required readonly>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="title">Title:</label>
                                        <input type="text" id="title" name="title" value="<?= $edit_movie['title'] ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description:</label>
                                        <textarea id="description" name="description" rows="3"><?= $edit_movie['description'] ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="director">Director:</label>
                                        <input type="text" id="director" name="director" value="<?= $edit_movie['director'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="release_year">Release Year:</label>
                                        <input type="number" id="release_year" name="release_year" value="<?= $edit_movie['release_year'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="duration_minutes">Duration (mins):</label>
                                        <input type="number" id="duration_minutes" name="duration_minutes" value="<?= $edit_movie['duration_minutes'] ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox-wrapper">
                                            <input type="checkbox" id="is_featured" name="is_featured" <?= $edit_movie['is_featured'] ? 'checked' : '' ?>>
                                            <label for="is_featured">Feature this movie on homepage</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Current Poster:</label>
                                        <div class="poster-preview">
                                            <?php if (!empty($edit_movie['poster_url'])): ?>
                                                <img src="<?= $edit_movie['poster_url'] ?>" alt="Movie poster" style="max-width: 100%; height: auto; border-radius: 8px;">
                                            <?php else: ?>
                                                <img src="https://img.youtube.com/vi/<?= $edit_movie['trailer_youtube_id'] ?>/mqdefault.jpg" alt="YouTube thumbnail" style="max-width: 100%; height: auto; border-radius: 8px;">
                                                <p class="text-center text-muted mt-2">Using YouTube thumbnail</p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="poster">Update Poster:</label>
                                            <input type="file" id="poster" name="poster" class="form-control" accept="image/jpeg, image/png, image/webp">
                                            <small class="form-hint text-light">Recommended size: 500x750 pixels</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="update_movie">Update Movie</button>
                            <a href="admin.php#movies"><button type="button" class="btn-secondary">Cancel</button></a>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Genres Tab -->
            <div id="genres" class="tab-content">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3><?= $edit_genre ? 'Edit Genre' : 'Add New Genre' ?></h3>
                            </div>
                            <form method="post" action="admin.php#genres">
                                <?php if ($edit_genre): ?>
                                    <input type="hidden" name="genre_id" value="<?= $edit_genre['id'] ?>">
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="genre_name">Genre Name:</label>
                                    <input type="text" id="genre_name" name="genre_name" value="<?= $edit_genre ? $edit_genre['name'] : '' ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="genre_description">Description:</label>
                                    <textarea id="genre_description" name="genre_description" rows="3"><?= $edit_genre ? $edit_genre['description'] : '' ?></textarea>
                                </div>
                                
                                <button type="submit" name="<?= $edit_genre ? 'update_genre' : 'add_genre' ?>">
                                    <?= $edit_genre ? 'Update Genre' : 'Add Genre' ?>
                                </button>
                                
                                <?php if ($edit_genre): ?>
                                    <a href="admin.php#genres"><button type="button" class="btn-secondary">Cancel</button></a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3>Genre Management</h3>
                            </div>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Movies</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $genres = $conn->query("SELECT g.*, COUNT(mg.movie_id) as movie_count 
                                                             FROM genres g
                                                             LEFT JOIN movie_genres mg ON g.id = mg.genre_id
                                                             GROUP BY g.id
                                                             ORDER BY g.name");
                                        
                                        if ($genres && $genres->num_rows > 0):
                                            while($row = $genres->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row['id'] ?></td>
                                                    <td><?= $row['name'] ?></td>
                                                    <td><?= $row['description'] ?></td>
                                                    <td><?= $row['movie_count'] ?></td>
                                                    <td class="action-links">
                                                        <a href="admin.php?action=edit&type=genre&id=<?= $row['id'] ?>#genres"><i class="fas fa-edit"></i> Edit</a>
                                                        <?php if ($row['movie_count'] == 0): ?>
                                                            <a href="admin.php?action=delete&type=genre&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this genre?')"><i class="fas fa-trash-alt"></i> Delete</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile;
                                        else: ?>
                                            <tr>
                                                <td colspan="5">No genres found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cast & Crew Tab -->
            <div id="cast" class="tab-content">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>Add New Cast/Crew Member</h3>
                            </div>
                            <form method="post" action="admin.php#cast">
                                <div class="form-group">
                                    <label for="cast_name">Full Name:</label>
                                    <input type="text" id="cast_name" name="cast_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="cast_role">Role:</label>
                                    <select id="cast_role" name="cast_role" required>
                                        <option value="actor">Actor</option>
                                        <option value="director">Director</option>
                                        <option value="producer">Producer</option>
                                        <option value="writer">Writer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="nationality">Nationality:</label>
                                    <input type="text" id="nationality" name="nationality">
                                </div>
                                <div class="form-group">
                                    <label for="bio">Biography:</label>
                                    <textarea id="bio" name="bio" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="birth_date">Birth Date:</label>
                                    <input type="date" id="birth_date" name="birth_date">
                                </div>
                                
                                <button type="submit" name="add_cast">Add Cast/Crew</button>
                            </form>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h3>Add Cast to Movie</h3>
                            </div>
                            <form method="post" action="admin.php#cast">
                                <div class="form-group">
                                    <label for="movie_id">Movie:</label>
                                    <select id="movie_id" name="movie_id" required>
                                        <option value="">Select Movie</option>
                                        <?php 
                                        $movie_options = $conn->query("SELECT id, title FROM movies ORDER BY title");
                                        if($movie_options):
                                            while ($movie = $movie_options->fetch_assoc()): ?>
                                                <option value="<?= $movie['id'] ?>"><?= $movie['title'] ?></option>
                                            <?php endwhile;
                                        endif; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="person_id">Person:</label>
                                    <select id="person_id" name="person_id" required>
                                        <option value="">Select Person</option>
                                        <?php 
                                        $person_options = $conn->query("SELECT id, name, role FROM cast_crew ORDER BY name");
                                        if($person_options):
                                            while ($person = $person_options->fetch_assoc()): ?>
                                                <option value="<?= $person['id'] ?>"><?= $person['name'] ?> (<?= ucfirst($person['role']) ?>)</option>
                                            <?php endwhile;
                                        endif; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="role_in_movie">Role in Movie:</label>
                                    <select id="role_in_movie" name="role_in_movie" required>
                                        <option value="">Select Role</option>
                                        <option value="Lead Actor">Lead Actor</option>
                                        <option value="Supporting Actor">Supporting Actor</option>
                                        <option value="Director">Director</option>
                                        <option value="Producer">Producer</option>
                                        <option value="Writer">Writer</option>
                                        <option value="Music Composer">Music Composer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="character_name">Character Name (for actors):</label>
                                    <input type="text" id="character_name" name="character_name">
                                </div>
                                
                                <button type="submit" name="add_movie_cast">Add to Movie</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3>Cast & Crew Management</h3>
                            </div>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Role</th>
                                            <th>Nationality</th>
                                            <th>Movies</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $cast = $conn->query("SELECT cc.*, COUNT(mcc.movie_id) as movie_count 
                                                          FROM cast_crew cc
                                                          LEFT JOIN movie_cast_crew mcc ON cc.id = mcc.person_id
                                                          GROUP BY cc.id
                                                          ORDER BY cc.name");
                                        
                                        if ($cast && $cast->num_rows > 0):
                                            while($row = $cast->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row['id'] ?></td>
                                                    <td><?= $row['name'] ?></td>
                                                    <td><?= ucfirst($row['role']) ?></td>
                                                    <td><?= $row['nationality'] ?></td>
                                                    <td><?= $row['movie_count'] ?></td>
                                                    <td class="action-links">
                                                        <?php if ($row['movie_count'] == 0): ?>
                                                            <a href="admin.php?action=delete&type=cast&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this cast/crew member?')"><i class="fas fa-trash-alt"></i> Delete</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile;
                                        else: ?>
                                            <tr>
                                                <td colspan="6">No cast & crew found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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
                                    <th>Movie</th>
                                    <th>User</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $reviews = $conn->query("SELECT r.*, m.title, CONCAT(u.first_name, ' ', u.last_name) as user_name 
                                                      FROM user_reviews r
                                                      JOIN movies m ON m.id = r.movie_id
                                                      JOIN users u ON u.id = r.user_id
                                                      ORDER BY r.created_at DESC");
                                
                                if ($reviews && $reviews->num_rows > 0):
                                    while($row = $reviews->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= $row['title'] ?></td>
                                            <td><?= $row['user_name'] ?></td>
                                            <td><?= $row['rating'] ?>/5</td>
                                            <td><?= substr($row['review_text'], 0, 100) . (strlen($row['review_text']) > 100 ? '...' : '') ?></td>
                                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                            <td class="action-links">
                                                <a href="admin.php?action=delete&type=review&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this review?')"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="7">No reviews found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Users Tab - View Only -->
            <div id="users" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>User Management</h3>
                        <span class="info-badge">View Only</span>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Country</th>
                                    <th>Joined Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                                
                                if ($users && $users->num_rows > 0):
                                    while($row = $users->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= $row['username'] ?></td>
                                            <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                                            <td><?= $row['email'] ?></td>
                                            <td><?= $row['country'] ?></td>
                                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                            <td><?= $row['is_active'] ? '<span class="badge active">Active</span>' : '<span class="badge inactive">Inactive</span>' ?></td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="7">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Add Movie Tab -->
            <div id="add-movie" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Add New Movie</h3>
                    </div>
                    <form method="post" action="admin.php#movies" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Movie Title:</label>
                                    <input type="text" id="title" name="title" placeholder="Enter movie title" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description:</label>
                                    <textarea id="description" name="description" placeholder="Enter movie description" rows="4"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="director">Director:</label>
                                    <input type="text" id="director" name="director" placeholder="Movie director">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="release_year">Release Year:</label>
                                            <input type="number" id="release_year" name="release_year" min="1900" max="2099" step="1" placeholder="YYYY">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="duration_minutes">Duration (minutes):</label>
                                            <input type="number" id="duration_minutes" name="duration_minutes" min="1" placeholder="Duration in minutes" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="poster">Movie Poster:</label>
                                    <input type="file" id="poster" name="poster" class="form-control" accept="image/jpeg, image/png, image/webp">
                                    <small class="form-hint text-light">Recommended size: 500x750 pixels (JPG, PNG, WebP)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="genre">Primary Genre:</label>
                                    <input type="text" id="genre" name="genre" placeholder="Primary genre">
                                </div>
                                <div class="form-group">
                                    <label>Select Genres:</label>
                                    <div class="checkbox-grid">
                                        <?php 
                                        $genres_list = $conn->query("SELECT * FROM genres ORDER BY name");
                                        if ($genres_list && $genres_list->num_rows > 0):
                                            while($genre = $genres_list->fetch_assoc()): ?>
                                                <div class="checkbox-wrapper">
                                                    <input type="checkbox" id="genre_<?= $genre['id'] ?>" name="genres[]" value="<?= $genre['id'] ?>">
                                                    <label for="genre_<?= $genre['id'] ?>"><?= $genre['name'] ?></label>
                                                </div>
                                            <?php endwhile;
                                        endif; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="rating">Rating:</label>
                                    <input type="number" id="rating" name="rating" step="0.1" min="0" max="10" placeholder="Movie rating (0-10)">
                                </div>
                                <div class="form-group">
                                    <label for="trailer_youtube_id">YouTube Trailer ID:</label>
                                    <input type="text" id="trailer_youtube_id" name="trailer_youtube_id" placeholder="e.g. dQw4w9WgXcQ">
                                    <small class="form-hint">The ID in the YouTube URL: youtube.com/watch?v=<strong>dQw4w9WgXcQ</strong></small>
                                </div>
                                <div class="form-group">
                                    <label for="movie_youtube_id">YouTube Movie ID (optional):</label>
                                    <input type="text" id="movie_youtube_id" name="movie_youtube_id" placeholder="YouTube ID for full movie">
                                </div>
                                <div class="form-group">
                                    <div class="checkbox-wrapper">
                                        <input type="checkbox" id="is_featured" name="is_featured">
                                        <label for="is_featured">Feature this movie on homepage</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="add_movie" class="btn-primary">
                                <i class="fas fa-plus-circle"></i> Add Movie
                            </button>
                            <button type="button" class="btn-secondary" onclick="showTab('movies')">Cancel</button>
                        </div>
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
            if (event && event.currentTarget) {
                event.currentTarget.parentElement.classList.add('active');
            }
            
            // Update URL hash for proper navigation
            if (tabId !== 'add-movie') {
                window.location.hash = tabId;
            }
        }
        
        // Check URL hash on page load to show the correct tab
        window.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash.replace('#', '');
            if (hash && document.getElementById(hash)) {
                showTab(hash);
                
                // Find and activate the correct sidebar item
                document.querySelectorAll('.sidebar-menu li').forEach(item => {
                    const link = item.querySelector('a');
                    if (link && link.getAttribute('onclick') && link.getAttribute('onclick').includes(hash)) {
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
