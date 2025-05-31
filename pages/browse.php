<?php
// Include database connection
require_once('../config/database.php');

// Get featured movies for hero section
$featuredQuery = "SELECT * FROM movies WHERE is_featured = TRUE ORDER BY view_count DESC LIMIT 1";
$featuredResult = $conn->query($featuredQuery);
$featuredMovie = $featuredResult->fetch_assoc();

// Get movies by category
$trendingQuery = "SELECT * FROM movies ORDER BY view_count DESC LIMIT 10";
$trendingMovies = $conn->query($trendingQuery);

$newestQuery = "SELECT * FROM movies ORDER BY release_year DESC LIMIT 10";
$newestMovies = $conn->query($newestQuery);

$actionQuery = "SELECT m.* FROM movies m 
                JOIN movie_genres mg ON m.id = mg.movie_id 
                JOIN genres g ON mg.genre_id = g.id 
                WHERE g.name = 'Action' 
                LIMIT 10";
$actionMovies = $conn->query($actionQuery);

$scifiQuery = "SELECT m.* FROM movies m 
               JOIN movie_genres mg ON m.id = mg.movie_id 
               JOIN genres g ON mg.genre_id = g.id 
               WHERE g.name = 'Sci-Fi' 
               LIMIT 10";
$scifiMovies = $conn->query($scifiQuery);

// Get all available genres
$genresQuery = "SELECT * FROM genres ORDER BY name";
$genres = $conn->query($genresQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Movies - StreamFlix</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/browse.css">
</head>
<body>
    <!-- Navigation bar -->
    <?php include_once('../components/navbar.php'); ?>
    
    <!-- Hero Section - Featured Movie -->
    <?php if($featuredMovie): 
        $posterUrl = !empty($featuredMovie['poster_url']) ? 
            (strpos($featuredMovie['poster_url'], 'http') === 0 ? $featuredMovie['poster_url'] : '../' . $featuredMovie['poster_url']) : 
            'https://img.youtube.com/vi/' . $featuredMovie['trailer_youtube_id'] . '/maxresdefault.jpg';
    ?>
    <section class="hero-section" style="background-image: linear-gradient(to bottom, rgba(15, 15, 35, 0.8), rgba(15, 15, 35, 0.95)), url(<?= $posterUrl ?>);">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title"><?= $featuredMovie['title'] ?></h1>
                <div class="hero-info mb-3">
                    <span class="hero-badge"><i class="fas fa-star text-warning me-1"></i> <?= $featuredMovie['rating'] ?></span>
                    <span class="hero-badge"><?= $featuredMovie['release_year'] ?></span>
                    <span class="hero-badge"><?= $featuredMovie['duration_minutes'] ?> min</span>
                    <?php 
                    // Get genres for this movie
                    $movieGenresQuery = "SELECT g.name FROM genres g JOIN movie_genres mg ON g.id = mg.genre_id WHERE mg.movie_id = " . $featuredMovie['id'] . " LIMIT 2";
                    $movieGenres = $conn->query($movieGenresQuery);
                    if($movieGenres):
                        while($genre = $movieGenres->fetch_assoc()):
                    ?>
                        <span class="hero-badge"><?= $genre['name'] ?></span>
                    <?php 
                        endwhile;
                    endif;
                    ?>
                </div>
                <p class="hero-description text-light"><?= $featuredMovie['description'] ?></p>
                <div class="d-flex gap-2">
                    <a href="watch.php?id=<?= $featuredMovie['id'] ?>" class="btn btn-primary-gradient">
                        <i class="fas fa-play me-2"></i>Watch Now
                    </a>
                    <a href="movie.php?id=<?= $featuredMovie['id'] ?>" class="btn btn-outline-light">
                        <i class="fas fa-info-circle me-2"></i>Details
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Main Content -->
    <div class="browse-content">
        <div class="container">
            <!-- Filter Section -->
            <div class="filter-section mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h2 class="section-title">Browse Movies</h2>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            <div class="dropdown me-2">
                                <button class="btn btn-dark dropdown-toggle" type="button" id="genreDropdown" data-bs-toggle="dropdown">
                                    Genre
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="genreDropdown">
                                    <li><a class="dropdown-item" href="browse.php">All Genres</a></li>
                                    <?php while($genre = $genres->fetch_assoc()): ?>
                                        <li><a class="dropdown-item" href="browse.php?genre=<?= $genre['id'] ?>"><?= $genre['name'] ?></a></li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-dark dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown">
                                    Sort By
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="sortDropdown">
                                    <li><a class="dropdown-item" href="browse.php?sort=trending">Trending</a></li>
                                    <li><a class="dropdown-item" href="browse.php?sort=newest">Newest</a></li>
                                    <li><a class="dropdown-item" href="browse.php?sort=rating">Rating</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Movie Categories -->
            <section class="movie-section mb-5">
                <h3 class="category-title">Trending Now</h3>
                <div class="movie-slider">
                    <div class="row g-4">
                        <?php while($movie = $trendingMovies->fetch_assoc()): 
                            $posterUrl = !empty($movie['poster_url']) ? 
                                (strpos($movie['poster_url'], 'http') === 0 ? $movie['poster_url'] : '../' . $movie['poster_url']) : 
                                'https://img.youtube.com/vi/' . $movie['trailer_youtube_id'] . '/mqdefault.jpg';
                        ?>
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                                <div class="movie-card">
                                    <a href="movie.php?id=<?= $movie['id'] ?>">
                                        <div class="movie-poster">
                                            <img src="<?= $posterUrl ?>" alt="<?= $movie['title'] ?>">
                                            <div class="play-btn">
                                                <i class="fas fa-play"></i>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <h5 class="mb-2 text-white"><?= $movie['title'] ?></h5>
                                            <p class="text-light small mb-2"><?= $movie['genre'] ?> • <?= $movie['release_year'] ?> • <span class="badge">HD</span></p>
                                            <div class="d-flex align-items-center">
                                                <small class="text-light"><i class="fas fa-star text-warning me-1"></i><?= $movie['rating'] ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </section>
            
            <section class="movie-section mb-5">
                <h3 class="category-title">Newest Releases</h3>
                <div class="movie-slider">
                    <div class="row g-4">
                        <?php 
                        // Reset result pointer to beginning
                        $newestMovies->data_seek(0);
                        while($movie = $newestMovies->fetch_assoc()): 
                            $posterUrl = !empty($movie['poster_url']) ? 
                                (strpos($movie['poster_url'], 'http') === 0 ? $movie['poster_url'] : '../' . $movie['poster_url']) : 
                                'https://img.youtube.com/vi/' . $movie['trailer_youtube_id'] . '/mqdefault.jpg';
                        ?>
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                                <div class="movie-card">
                                    <a href="movie.php?id=<?= $movie['id'] ?>">
                                        <div class="movie-poster">
                                            <img src="<?= $posterUrl ?>" alt="<?= $movie['title'] ?>">
                                            <div class="play-btn">
                                                <i class="fas fa-play"></i>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <h5 class="mb-2 text-white"><?= $movie['title'] ?></h5>
                                            <p class="text-light small mb-2"><?= $movie['genre'] ?> • <?= $movie['release_year'] ?> • <span class="badge">HD</span></p>
                                            <div class="d-flex align-items-center">
                                                <small class="text-light"><i class="fas fa-star text-warning me-1"></i><?= $movie['rating'] ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </section>
            
            <section class="movie-section mb-5">
                <h3 class="category-title">Action Movies</h3>
                <div class="movie-slider">
                    <div class="row g-4">
                        <?php while($movie = $actionMovies->fetch_assoc()): 
                            $posterUrl = !empty($movie['poster_url']) ? 
                                (strpos($movie['poster_url'], 'http') === 0 ? $movie['poster_url'] : '../' . $movie['poster_url']) : 
                                'https://img.youtube.com/vi/' . $movie['trailer_youtube_id'] . '/mqdefault.jpg';
                        ?>
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                                <div class="movie-card">
                                    <a href="movie.php?id=<?= $movie['id'] ?>">
                                        <div class="movie-poster">
                                            <img src="<?= $posterUrl ?>" alt="<?= $movie['title'] ?>">
                                            <div class="play-btn">
                                                <i class="fas fa-play"></i>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <h5 class="mb-2 text-white"><?= $movie['title'] ?></h5>
                                            <p class="text-light small mb-2"><?= $movie['genre'] ?> • <?= $movie['release_year'] ?> • <span class="badge">HD</span></p>
                                            <div class="d-flex align-items-center">
                                                <small class="text-light"><i class="fas fa-star text-warning me-1"></i><?= $movie['rating'] ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </section>
            
            <section class="movie-section mb-5">
                <h3 class="category-title">Sci-Fi Movies</h3>
                <div class="movie-slider">
                    <div class="row g-4">
                        <?php while($movie = $scifiMovies->fetch_assoc()): 
                            $posterUrl = !empty($movie['poster_url']) ? 
                                (strpos($movie['poster_url'], 'http') === 0 ? $movie['poster_url'] : '../' . $movie['poster_url']) : 
                                'https://img.youtube.com/vi/' . $movie['trailer_youtube_id'] . '/mqdefault.jpg';
                        ?>
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                                <div class="movie-card">
                                    <a href="movie.php?id=<?= $movie['id'] ?>">
                                        <div class="movie-poster">
                                            <img src="<?= $posterUrl ?>" alt="<?= $movie['title'] ?>">
                                            <div class="play-btn">
                                                <i class="fas fa-play"></i>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <h5 class="mb-2 text-white"><?= $movie['title'] ?></h5>
                                            <p class="text-light small mb-2"><?= $movie['genre'] ?> • <?= $movie['release_year'] ?> • <span class="badge">HD</span></p>
                                            <div class="d-flex align-items-center">
                                                <small class="text-light"><i class="fas fa-star text-warning me-1"></i><?= $movie['rating'] ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include_once('../components/footer.php'); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
