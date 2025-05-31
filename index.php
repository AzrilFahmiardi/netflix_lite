<?php
// Include database connection
require_once('config/database.php');

// Start session for login check
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $isLoggedIn ? $_SESSION['name'] : '';
$userInitial = $isLoggedIn ? substr($_SESSION['name'], 0, 1) : '';

// Get trending movies for preview section
$trendingQuery = "SELECT * FROM movies ORDER BY view_count DESC LIMIT 4";
$trendingMovies = $conn->query($trendingQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamFlix - Premium Movie Streaming</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <!-- Navigation -->
    <?php include_once('components/navbar.php'); ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div>
                        <h1 class="display-4 fw-bold mb-4">
                            Stream Your <span class="gradient-text">Favorite Movies</span> Anywhere
                        </h1>
                        <p class="lead mb-4 text-light">
                            Discover thousands of movies and TV shows. Watch on any device, anytime.
                        </p>
                        <p class="mb-4 text-light">
                            Join millions of viewers and start your entertainment journey today.
                        </p>
                        
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="pages/register.php" class="btn btn-primary-gradient btn-lg">
                                <i class="bi bi-rocket-takeoff me-2"></i>Start Watching
                            </a>
                            <a href="#movies" class="btn btn-outline-gradient btn-lg">
                                <i class="bi bi-compass me-2"></i>Explore Content
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <img src="assets/images/hero/interstellar.jpg" 
                             alt="Entertainment" class="img-fluid hero-image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Why Choose StreamFlix?</h2>
            
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="glass-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="bi bi-lightning-fill"></i>
                        </div>
                        <h4 class="gradient-text">Easy Access</h4>
                        <h6 class="text-white mb-3">Stream Anytime</h6>
                        <p class="text-light">No signup or complex setup. Just click and start watching your favorite films instantly.</p>
                    </div>
                </div>
                
                <div class="col-md-4 text-center">
                    <div class="glass-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="bi bi-collection-play-fill"></i>
                        </div>
                        <h4 class="gradient-text">Wide Selection</h4>
                        <h6 class="text-white mb-3">Diverse Movie Library</h6>
                        <p class="text-light">Enjoy a curated collection of movies from various genres — action, drama, comedy, and more.</p>
                    </div>
                </div>
                
                <div class="col-md-4 text-center">
                    <div class="glass-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="gradient-text">No Ads</h4>
                        <h6 class="text-white mb-3">Ad-Free Experience</h6>
                        <p class="text-light">Watch your movies without interruptions. No annoying ads, just pure streaming enjoyment.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Movies Preview Section -->
    <section id="movies" class="py-5">
        <div class="container">
            <h2 class="section-title">Trending Now</h2>
            
            <div class="row g-4">
                <?php 
                if ($trendingMovies && $trendingMovies->num_rows > 0):
                    while ($movie = $trendingMovies->fetch_assoc()): 
                        // Get poster URL (from uploaded file or YouTube thumbnail)
                        $posterUrl = !empty($movie['poster_url']) ? 
                            $movie['poster_url'] : 
                            'https://img.youtube.com/vi/' . $movie['trailer_youtube_id'] . '/mqdefault.jpg';
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="movie-card">
                        <img src="<?= $posterUrl ?>" alt="<?= $movie['title'] ?>">
                        <div class="p-3">
                            <h5 class="mb-2 text-white"><?= $movie['title'] ?></h5>
                            <p class="text-light small mb-2"><?= $movie['genre'] ?> • <?= $movie['release_year'] ?> • <span class="badge">HD</span></p>
                            <div class="d-flex align-items-center">
                                <small class="text-light"><i class="bi bi-star-fill text-warning me-1"></i><?= $movie['rating'] ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                    // Fallback to static content if no movies in database
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="movie-card">
                        <img src="https://images.unsplash.com/photo-1440404653325-ab127d49abc1?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Movie 1">
                        <div class="p-3">
                            <h5 class="mb-2 text-white">The Great Adventure</h5>
                            <p class="text-light small mb-2">Action • 2023 • <span class="badge">HD</span></p>
                            <div class="d-flex align-items-center">
                                <small class="text-light"><i class="bi bi-star-fill text-warning me-1"></i>8.5</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="movie-card">
                        <img src="https://images.unsplash.com/photo-1489599824261-58cee4e46fae?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Movie 2">
                        <div class="p-3">
                            <h5 class="mb-2 text-white">Mystery Island</h5>
                            <p class="text-light small mb-2">Thriller • 2023 • <span class="badge">4K</span></p>
                            <div class="d-flex align-items-center">
                                <small class="text-light"><i class="bi bi-star-fill text-warning me-1"></i>9.2</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="movie-card">
                        <img src="https://images.unsplash.com/photo-1478720568477-b0ac8d8373b0?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Movie 3">
                        <div class="p-3">
                            <h5 class="mb-2 text-white">Space Odyssey</h5>
                            <p class="text-light small mb-2">Sci-Fi • 2023 • <span class="badge">4K</span></p>
                            <div class="d-flex align-items-center">
                                <small class="text-light"><i class="bi bi-star-fill text-warning me-1"></i>8.8</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="movie-card">
                        <img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Movie 4">
                        <div class="p-3">
                            <h5 class="mb-2 text-white">Love Stories</h5>
                            <p class="text-light small mb-2">Romance • 2023 • <span class="badge">HD</span></p>
                            <div class="d-flex align-items-center">
                                <small class="text-light"><i class="bi bi-star-fill text-warning me-1"></i>7.9</small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="pages/browse.php" class="btn btn-primary-gradient btn-lg">
                    <i class="bi bi-play-circle me-2"></i>Browse All Content
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5 class="gradient-text">StreamFlix</h5>
                    <p class="text-light">Your premium streaming destination for unlimited entertainment.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-white">Company</h6>
                    <ul class="list-unstyled text-light">
                        <li><a href="#" class="text-light text-decoration-none">About Us</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Careers</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Press</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="text-white">Support</h6>
                    <ul class="list-unstyled text-light">
                        <li><a href="#" class="text-light text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Contact Us</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="text-white">Follow Us</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: rgba(108, 92, 231, 0.2);">
            
            <div class="text-center text-light">
                <p>&copy; 2023 StreamFlix. All rights reserved. | Made with <i class="bi bi-heart-fill text-danger"></i> for movie lovers</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
