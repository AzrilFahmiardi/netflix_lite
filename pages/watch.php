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

// Update view count
$sql = "UPDATE movies SET view_count = view_count + 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $movieId);
$stmt->execute();

// Get similar movies (same genre)
$sql = "SELECT DISTINCT m.* FROM movies m
        JOIN movie_genres mg1 ON m.id = mg1.movie_id
        JOIN movie_genres mg2 ON mg1.genre_id = mg2.genre_id
        WHERE mg2.movie_id = ? AND m.id != ?
        LIMIT 6";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $movieId, $movieId);
$stmt->execute();
$similarMovies = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch <?= $movie['title'] ?> - StreamFlix</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .video-container {
            position: relative;
            width: 100%;
            padding-top: 56.25%; /* 16:9 aspect ratio */
            background-color: #000;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .movie-info-panel {
            background: rgba(26, 26, 62, 0.8);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .similar-movie {
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .similar-movie:hover {
            transform: translateY(-5px);
        }
        
        .similar-movie img {
            border-radius: 6px;
        }
        
        .similar-movie .title {
            font-size: 14px;
            margin-top: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <?php include_once('../components/navbar.php'); ?>
    
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-lg-8">
                <!-- Video Player -->
                <div class="video-container">
                    <iframe 
                        src="https://www.youtube.com/embed/<?= $movie['movie_youtube_id'] ? $movie['movie_youtube_id'] : $movie['trailer_youtube_id'] ?>?autoplay=1&controls=1&rel=0" 
                        title="<?= $movie['title'] ?>"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
                
                <!-- Movie Info -->
                <div class="movie-info-panel">
                    <h1 class="mb-2"><?= $movie['title'] ?></h1>
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2"><?= $movie['rating'] ?> <i class="fas fa-star"></i></span>
                        <span class="badge bg-secondary me-2"><?= $movie['release_year'] ?></span>
                        <span class="badge bg-secondary me-2"><?= $movie['duration_minutes'] ?> min</span>
                        <span class="ms-auto">
                            <button class="btn btn-sm btn-outline-light">
                                <i class="fas fa-plus me-1"></i> Add to Watchlist
                            </button>
                        </span>
                    </div>
                    <p><?= $movie['description'] ?></p>
                    <p><strong>Director:</strong> <?= $movie['director'] ?></p>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Similar Movies -->
                <div class="card bg-dark">
                    <div class="card-header">
                        <h5 class="mb-0">Similar Movies</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php while($similarMovie = $similarMovies->fetch_assoc()): ?>
                                <div class="col-6 similar-movie">
                                    <a href="watch.php?id=<?= $similarMovie['id'] ?>">
                                        <?php 
                                        $posterUrl = !empty($similarMovie['poster_url']) ? 
                                            (strpos($similarMovie['poster_url'], 'http') === 0 ? $similarMovie['poster_url'] : '../' . $similarMovie['poster_url']) : 
                                            'https://img.youtube.com/vi/' . $similarMovie['trailer_youtube_id'] . '/mqdefault.jpg';
                                        ?>
                                        <img src="<?= $posterUrl ?>" alt="<?= $similarMovie['title'] ?>" class="img-fluid">
                                        <div class="title"><?= $similarMovie['title'] ?></div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Comments Section -->
                <div class="card bg-dark mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Reviews</h5>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addReviewModal">
                            <i class="fas fa-plus me-1"></i> Add Review
                        </button>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <div class="reviews-container">
                            <!-- Placeholder for reviews - To be implemented -->
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-comments fa-3x mb-3"></i>
                                <p>Be the first to review this movie</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Review Modal -->
    <div class="modal fade" id="addReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header">
                    <h5 class="modal-title">Write a Review</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm" action="add_review.php" method="post">
                        <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div class="rating-input">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" />
                                    <label for="star<?= $i ?>"><i class="fas fa-star"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="review_text" class="form-label">Your Review</label>
                            <textarea class="form-control" id="review_text" name="review_text" rows="4" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
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
