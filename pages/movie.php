<?php
// Include database connection
require_once('../config/database.php');

// Get movie ID from URL
if (!isset($_GET['id'])) {
    header('Location: browse.php');
    exit;
}

$movieId = $_GET['id'];

// Get movie details
$sql = "SELECT * FROM movies WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $movieId);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

// If movie doesn't exist, redirect to browse page
if (!$movie) {
    header('Location: browse.php');
    exit;
}

// Get movie genres
$sql = "SELECT g.name FROM genres g 
        JOIN movie_genres mg ON g.id = mg.genre_id 
        WHERE mg.movie_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $movieId);
$stmt->execute();
$genreResult = $stmt->get_result();

// Get movie cast
$sql = "SELECT cc.name, cc.role, mcc.role_in_movie, mcc.character_name 
        FROM cast_crew cc
        JOIN movie_cast_crew mcc ON cc.id = mcc.person_id
        WHERE mcc.movie_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $movieId);
$stmt->execute();
$castResult = $stmt->get_result();

// Get movie reviews
$sql = "SELECT ur.*, u.username, u.first_name, u.last_name
        FROM user_reviews ur
        JOIN users u ON ur.user_id = u.id
        WHERE ur.movie_id = ?
        ORDER BY ur.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $movieId);
$stmt->execute();
$reviewsResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $movie['title'] ?> - StreamFlix</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/movie.css">
</head>
<body>
    <!-- Navigation bar -->
    <?php include_once('../components/navbar.php'); ?>
    
    <!-- Movie Hero Section -->
    <div class="movie-hero" style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)), url(<?= !empty($movie['poster_url']) ? $movie['poster_url'] : 'https://img.youtube.com/vi/' . $movie['trailer_youtube_id'] . '/maxresdefault.jpg' ?>);">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-lg-3 mb-4 mb-md-0">
                    <div class="movie-poster-container">
                        <img src="<?= !empty($movie['poster_url']) ? $movie['poster_url'] : 'https://img.youtube.com/vi/' . $movie['trailer_youtube_id'] . '/mqdefault.jpg' ?>" alt="<?= $movie['title'] ?>" class="movie-poster img-fluid">
                    </div>
                </div>
                <div class="col-md-8 col-lg-9">
                    <div class="movie-info">
                        <h1 class="movie-title"><?= $movie['title'] ?></h1>
                        <div class="movie-meta">
                            <span class="badge bg-primary me-2"><?= $movie['rating'] ?> <i class="fas fa-star"></i></span>
                            <span class="badge bg-secondary me-2"><?= $movie['release_year'] ?></span>
                            <span class="badge bg-secondary me-2"><?= $movie['duration_minutes'] ?> min</span>
                            <?php while ($genre = $genreResult->fetch_assoc()): ?>
                                <span class="badge bg-secondary me-2"><?= $genre['name'] ?></span>
                            <?php endwhile; ?>
                        </div>
                        <p class="movie-description"><?= $movie['description'] ?></p>
                        <div class="movie-director">
                            <strong>Director:</strong> <?= $movie['director'] ?>
                        </div>
                        <div class="movie-actions mt-4">
                            <a href="watch.php?id=<?= $movie['id'] ?>" class="btn btn-primary-gradient me-2">
                                <i class="fas fa-play me-2"></i>Watch Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Movie Content -->
    <div class="container movie-content py-5">
        <!-- Rest of your movie detail content -->
    </div>
    
    <!-- Footer -->
    <?php include_once('../components/footer.php'); ?>

    <!-- Add Review Modal -->
    <div class="modal fade" id="addReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header">
                    <h5 class="modal-title">Write a Review</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm" action="../actions/add_review.php" method="post">
                        <!-- ...existing code... -->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
