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

// Get movie cast - using the same approach as in movie.php
$sql = "SELECT cc.name, cc.role, mcc.role_in_movie, mcc.character_name 
        FROM cast_crew cc
        JOIN movie_cast_crew mcc ON cc.id = mcc.person_id
        WHERE mcc.movie_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $movieId);
$stmt->execute();
$castResult = $stmt->get_result();

// Get movie reviews - same as movie.php
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
    <title>Watch <?= $movie['title'] ?> - StreamFlix</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/watch.css">
</head>
<body>
    <!-- Navigation bar -->
    <?php include_once('../components/navbar.php'); ?>
    
    <div class="container-fluid mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Video Player -->
                <div class="video-container">
                    <iframe 
                        src="<?= $movie['movie_url'] ? $movie['movie_url'] . '?autoplay=1&controls=1&rel=0' : 'https://www.youtube.com/embed/' . $movie['trailer_youtube_id'] . '?autoplay=1&controls=1&rel=0' ?>" 
                        title="<?= $movie['title'] ?>"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
                
                <!-- Movie Info -->
                <div class="movie-info-panel">
                    <h1 class="mb-3"><?= $movie['title'] ?></h1>
                    <div class="movie-metadata">
                        <span class="badge bg-primary me-2 p-2"><i class="fas fa-star me-1"></i> <?= $movie['rating'] ?></span>
                        <span class="badge bg-secondary me-2 p-2"><?= $movie['release_year'] ?></span>
                        <span class="badge bg-secondary me-2 p-2"><i class="fas fa-clock me-1"></i> <?= $movie['duration_minutes'] ?> min</span>
                    </div>
                    <div class="movie-description">
                        <?= $movie['description'] ?>
                    </div>
                    <p><strong>Director:</strong> <?= $movie['director'] ?></p>
                    
                    <!-- Cast Section -->
                    <?php if ($castResult->num_rows > 0): ?>
                    <div class="cast-crew-section mt-4">
                        <h4 class="mb-3">Cast & Crew</h4>
                        <div class="cast-list">
                            <?php 
                            $castResult->data_seek(0);
                            while ($castMember = $castResult->fetch_assoc()): 
                                $initial = substr($castMember['name'], 0, 1);
                            ?>
                                <div class="cast-card">
                                    <div class="cast-avatar"><?= $initial ?></div>
                                    <div class="cast-info">
                                        <h5><?= $castMember['name'] ?></h5>
                                        <p>
                                            <?php if (!empty($castMember['character_name'])): ?>
                                                as <?= $castMember['character_name'] ?>
                                            <?php else: ?>
                                                <?= $castMember['role_in_movie'] ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Comments Section -->
                <div class="movie-info-panel mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">User Reviews</h3>
                        <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#addReviewModal">
                            <i class="fas fa-star me-2"></i>Write a Review
                        </button>
                    </div>
                    
                    <?php if ($reviewsResult->num_rows > 0): ?>
                        <?php while ($review = $reviewsResult->fetch_assoc()): 
                            $reviewerName = $review['first_name'] . ' ' . $review['last_name'];
                            $reviewerInitial = substr($review['first_name'], 0, 1);
                            $reviewDate = date('M d, Y', strtotime($review['created_at']));
                        ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="reviewer">
                                        <div class="reviewer-avatar"><?= $reviewerInitial ?></div>
                                        <div class="reviewer-info">
                                            <h5><?= $reviewerName ?></h5>
                                            <p><?= $reviewDate ?></p>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <i class="fas fa-star text-warning me-1"></i> <?= $review['rating'] ?>/5
                                    </div>
                                </div>
                                <div class="review-text">
                                    <?= $review['review_text'] ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No reviews yet. Be the first to review this movie!
                        </div>
                    <?php endif; ?>
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
                    <form id="reviewForm" action="../actions/add_review.php" method="post">
                        <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                        
                        <div class="mb-3">
                            <label for="rating" class="form-label">Your Rating</label>
                            <div class="rating-stars">
                                <div class="d-flex gap-2">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                    <span class="star-rating" data-value="<?= $i ?>">
                                        <i class="far fa-star fa-2x"></i>
                                    </span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <input type="hidden" name="rating" id="rating" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="review" class="form-label">Your Review</label>
                            <textarea class="form-control bg-dark text-light" id="review" name="review_text" rows="5" placeholder="What did you think of the movie?" required></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-gradient">Submit Review</button>
                        </div>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Star rating functionality
        const stars = document.querySelectorAll('.star-rating');
        const ratingInput = document.getElementById('rating');
        
        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                const ratingValue = parseInt(this.getAttribute('data-value'));
                highlightStars(ratingValue);
            });
            
            star.addEventListener('mouseout', function() {
                const currentRating = parseInt(ratingInput.value) || 0;
                highlightStars(currentRating);
            });
            
            star.addEventListener('click', function() {
                const ratingValue = parseInt(this.getAttribute('data-value'));
                ratingInput.value = ratingValue;
                highlightStars(ratingValue);
            });
        });
        
        function highlightStars(rating) {
            stars.forEach(star => {
                const starValue = parseInt(star.getAttribute('data-value'));
                const starIcon = star.querySelector('i');
                
                if (starValue <= rating) {
                    starIcon.classList.remove('far');
                    starIcon.classList.add('fas', 'text-warning');
                } else {
                    starIcon.classList.remove('fas', 'text-warning');
                    starIcon.classList.add('far');
                }
            });
        }
    });
    </script>
</body>
</html>
