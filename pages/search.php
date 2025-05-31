<?php
// Include database connection
require_once('../config/database.php');

// Get search query
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

// If no search query provided, redirect to browse page
if (empty($searchQuery)) {
    header('Location: browse.php');
    exit;
}

// Get search results
$sql = "SELECT * FROM movies 
        WHERE title LIKE ? 
        OR description LIKE ? 
        OR director LIKE ?
        ORDER BY view_count DESC";
$stmt = $conn->prepare($sql);

$searchParam = "%" . $searchQuery . "%";
$stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
$stmt->execute();
$searchResults = $stmt->get_result();
$resultCount = $searchResults->num_rows;

// Get all available genres for filter
$genresQuery = "SELECT * FROM genres ORDER BY name";
$genres = $conn->query($genresQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?= htmlspecialchars($searchQuery) ?>" - StreamFlix</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation bar -->
    <?php include_once('../components/navbar.php'); ?>
    
    <!-- Main Content -->
    <div class="container mt-5 pt-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="section-title">
                    Search Results for "<?= htmlspecialchars($searchQuery) ?>"
                </h2>
                <p class="text-muted">Found <?= $resultCount ?> results</p>
            </div>
            <div class="col-md-4">
                <form action="search.php" method="get" class="d-flex">
                    <input type="text" name="q" class="form-control" placeholder="Search again..." value="<?= htmlspecialchars($searchQuery) ?>">
                    <button type="submit" class="btn btn-primary ms-2">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <?php if ($resultCount > 0): ?>
            <!-- Search Results -->
            <div class="row">
                <?php while($movie = $searchResults->fetch_assoc()): ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="movie-card">
                            <a href="movie.php?id=<?= $movie['id'] ?>">
                                <div class="movie-poster">
                                    <?php 
                                    $posterUrl = !empty($movie['poster_url']) ? 
                                        (strpos($movie['poster_url'], 'http') === 0 ? $movie['poster_url'] : '../' . $movie['poster_url']) : 
                                        'https://img.youtube.com/vi/' . $movie['trailer_youtube_id'] . '/mqdefault.jpg';
                                    ?>
                                    <img src="<?= $posterUrl ?>" alt="<?= $movie['title'] ?>" class="img-fluid">
                                    <div class="overlay">
                                        <div class="overlay-content">
                                            <div class="rating">
                                                <i class="fas fa-star"></i> <?= $movie['rating'] ?>
                                            </div>
                                            <div class="play-button">
                                                <i class="fas fa-play"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="movie-info">
                                    <h5 class="movie-title"><?= $movie['title'] ?></h5>
                                    <div class="movie-details">
                                        <span><?= $movie['release_year'] ?></span>
                                        <span><?= $movie['duration_minutes'] ?> min</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <!-- No Results -->
            <div class="text-center py-5">
                <i class="fas fa-search fa-4x mb-4 text-muted"></i>
                <h3>No results found</h3>
                <p class="text-muted">We couldn't find any movies matching "<?= htmlspecialchars($searchQuery) ?>"</p>
                <a href="browse.php" class="btn btn-primary mt-3">Browse All Movies</a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Footer -->
    <?php include_once('../components/footer.php'); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
