<?php
// Include database connection
require_once('../config/database.php');
// Include session check for authentication
require_once('../components/session_check.php');

// Require login for this page
requireLogin();

// Get all available genres
$genresQuery = "SELECT * FROM genres ORDER BY name";
$genres = $conn->query($genresQuery);
$allGenres = [];
while ($genreRow = $genres->fetch_assoc()) {
    $allGenres[] = $genreRow;
}
$genres->data_seek(0); // Reset pointer for later use

// Pagination settings
$moviesPerPage = 6;
$trendingPage = isset($_GET['trending_page']) ? max(1, (int)$_GET['trending_page']) : 1;
$newestPage = isset($_GET['newest_page']) ? max(1, (int)$_GET['newest_page']) : 1;

// Dynamic genre pagination settings
$genrePages = [];
foreach ($allGenres as $genre) {
    $paramName = strtolower(str_replace(' ', '_', $genre['name'])) . '_page';
    $genrePages[$genre['id']] = [
        'name' => $genre['name'],
        'param' => $paramName,
        'page' => isset($_GET[$paramName]) ? max(1, (int)$_GET[$paramName]) : 1,
        'slug' => strtolower(str_replace(' ', '-', $genre['name']))
    ];
}

// Calculate offset for each section
$trendingOffset = ($trendingPage - 1) * $moviesPerPage;
$newestOffset = ($newestPage - 1) * $moviesPerPage;

// Get featured movies for hero section
$featuredQuery = "SELECT * FROM movies WHERE is_featured = TRUE ORDER BY view_count DESC LIMIT 1";
$featuredResult = $conn->query($featuredQuery);
$featuredMovie = $featuredResult->fetch_assoc();

// Get total counts for pagination
$trendingCountQuery = "SELECT COUNT(*) as total FROM movies";
$trendingCountResult = $conn->query($trendingCountQuery);
$trendingTotal = $trendingCountResult->fetch_assoc()['total'];
$trendingTotalPages = ceil($trendingTotal / $moviesPerPage);

$newestCountQuery = "SELECT COUNT(*) as total FROM movies";
$newestCountResult = $conn->query($newestCountQuery);
$newestTotal = $newestCountResult->fetch_assoc()['total'];
$newestTotalPages = ceil($newestTotal / $moviesPerPage);

// Calculate genre counts and fetch movies for each genre
$genreMovies = [];
$genreTotalPages = [];

foreach ($allGenres as $genre) {
    $genreId = $genre['id'];
    $genreName = $genre['name'];
    $genrePage = $genrePages[$genreId]['page'];
    $genreOffset = ($genrePage - 1) * $moviesPerPage;
    
    // Count total movies for this genre
    $countQuery = "SELECT COUNT(DISTINCT m.id) as total FROM movies m 
                 JOIN movie_genres mg ON m.id = mg.movie_id 
                 JOIN genres g ON mg.genre_id = g.id 
                 WHERE g.id = $genreId";
    $countResult = $conn->query($countQuery);
    $total = $countResult->fetch_assoc()['total'];
    $genreTotalPages[$genreId] = ceil($total / $moviesPerPage);
    
    // Get movies for this genre
    $moviesQuery = "SELECT m.* FROM movies m 
                  JOIN movie_genres mg ON m.id = mg.movie_id 
                  JOIN genres g ON mg.genre_id = g.id 
                  WHERE g.id = $genreId 
                  ORDER BY m.view_count DESC
                  LIMIT $moviesPerPage OFFSET $genreOffset";
    $genreMovies[$genreId] = $conn->query($moviesQuery);
}

// Get movies by category with pagination
$trendingQuery = "SELECT * FROM movies ORDER BY view_count DESC LIMIT $moviesPerPage OFFSET $trendingOffset";
$trendingMovies = $conn->query($trendingQuery);

$newestQuery = "SELECT * FROM movies ORDER BY release_year DESC LIMIT $moviesPerPage OFFSET $newestOffset";
$newestMovies = $conn->query($newestQuery);

// Function to generate Bootstrap pagination with simple AJAX
function generatePagination($currentPage, $totalPages, $pageParam, $sectionId, $otherParams = []) {
    if ($totalPages <= 1) return '';
    
    $pagination = '<nav aria-label="Page navigation" class="d-flex justify-content-center mt-4">';
    $pagination .= '<ul class="pagination" data-section="' . $sectionId . '" data-page-param="' . $pageParam . '">';
    
    // Previous button
    $prevDisabled = $currentPage <= 1 ? 'disabled' : '';
    $prevPage = max(1, $currentPage - 1);
    
    $pagination .= '<li class="page-item ' . $prevDisabled . '">';
    if ($prevDisabled) {
        $pagination .= '<span class="page-link"><span aria-hidden="true">&laquo;</span></span>';
    } else {
        $pagination .= '<a class="page-link ajax-pagination" href="#" data-page="' . $prevPage . '" aria-label="Previous">';
        $pagination .= '<span aria-hidden="true">&laquo;</span>';
        $pagination .= '</a>';
    }
    $pagination .= '</li>';
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    // First page
    if ($startPage > 1) {
        $pagination .= '<li class="page-item"><a class="page-link ajax-pagination" href="#" data-page="1">1</a></li>';
        if ($startPage > 2) {
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Page range
    for ($i = $startPage; $i <= $endPage; $i++) {
        $active = $i == $currentPage ? 'active' : '';
        
        $pagination .= '<li class="page-item ' . $active . '">';
        if ($active) {
            $pagination .= '<span class="page-link">' . $i . '</span>';
        } else {
            $pagination .= '<a class="page-link ajax-pagination" href="#" data-page="' . $i . '">' . $i . '</a>';
        }
        $pagination .= '</li>';
    }
    
    // Last page
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $pagination .= '<li class="page-item"><a class="page-link ajax-pagination" href="#" data-page="' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // Next button
    $nextDisabled = $currentPage >= $totalPages ? 'disabled' : '';
    $nextPage = min($totalPages, $currentPage + 1);
    
    $pagination .= '<li class="page-item ' . $nextDisabled . '">';
    if ($nextDisabled) {
        $pagination .= '<span class="page-link"><span aria-hidden="true">&raquo;</span></span>';
    } else {
        $pagination .= '<a class="page-link ajax-pagination" href="#" data-page="' . $nextPage . '" aria-label="Next">';
        $pagination .= '<span aria-hidden="true">&raquo;</span>';
        $pagination .= '</a>';
    }
    $pagination .= '</li>';
    
    $pagination .= '</ul></nav>';
    
    return $pagination;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Movies - StreamFlix</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/browse.css">
</head>
<body>
    <!-- Navigation bar -->
    <?php include_once('../components/navbar.php'); ?>
    
    <!-- Hero Section - Featured Movie -->
    <?php if($featuredMovie): 
        // Simple direct poster URL handling without helpers or YouTube fallback
        $posterUrl = !empty($featuredMovie['poster_url']) ? 
            (strpos($featuredMovie['poster_url'], 'http') === 0 ? $featuredMovie['poster_url'] : '../' . $featuredMovie['poster_url']) : 
            '../assets/images/default-poster.jpg';
    ?>
    <section class="hero-section" style="background-image: linear-gradient(to bottom, rgba(15, 15, 35, 0.8), rgba(15, 15, 35, 0.95)), url(<?= $posterUrl ?>);">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title"><?= $featuredMovie['title'] ?></h1>
                <div class="hero-info mb-3">
                    <span class="hero-badge"><i class="bi bi-star-fill text-warning me-1"></i> <?= $featuredMovie['rating'] ?></span>
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
                        <i class="bi bi-play-circle me-2"></i>Watch Now
                    </a>
                    <a href="movie.php?id=<?= $featuredMovie['id'] ?>" class="btn btn-outline-light">
                        <i class="bi bi-info-circle me-2"></i>Details
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
                                    <?php 
                                    $genres->data_seek(0);
                                    while($genre = $genres->fetch_assoc()): 
                                    ?>
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
            <section class="movie-section mb-5" id="trending-section">
                <h3 class="category-title">Trending Now</h3>
                <div class="movie-slider" id="trending-content">
                    <div class="row g-4">
                        <?php while($movie = $trendingMovies->fetch_assoc()): 
                            // Simple direct poster URL handling without helpers or YouTube fallback
                            $posterUrl = !empty($movie['poster_url']) ? 
                                (strpos($movie['poster_url'], 'http') === 0 ? $movie['poster_url'] : '../' . $movie['poster_url']) : 
                                '../assets/images/default-poster.jpg';
                        ?>
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                                <div class="movie-card">
                                    <a href="movie.php?id=<?= $movie['id'] ?>">
                                        <div class="movie-poster">
                                            <img src="<?= $posterUrl ?>" alt="<?= $movie['title'] ?>">
                                            <div class="play-btn">
                                                <i class="bi bi-play-fill"></i>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <h5 class="mb-2 text-white"><?= $movie['title'] ?></h5>
                                            <p class="text-light small mb-2"><?= $movie['genre'] ?> • <?= $movie['release_year'] ?> • <span class="badge">HD</span></p>
                                            <div class="d-flex align-items-center">
                                                <small class="text-light"><i class="bi bi-star-fill text-warning me-1"></i><?= $movie['rating'] ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php
                // Prepare all page params for pagination
                $allPageParams = [];
                foreach ($genrePages as $genreId => $genreInfo) {
                    $allPageParams[$genreInfo['param']] = $genreInfo['page'];
                }
                echo generatePagination($trendingPage, $trendingTotalPages, 'trending_page', 'trending-section', $allPageParams);
                ?>
            </section>
            
            <section class="movie-section mb-5" id="newest-section">
                <h3 class="category-title">Newest Releases</h3>
                <div class="movie-slider" id="newest-content">
                    <div class="row g-4">
                        <?php while($movie = $newestMovies->fetch_assoc()): 
                            // Simple direct poster URL handling without helpers or YouTube fallback
                            $posterUrl = !empty($movie['poster_url']) ? 
                                (strpos($movie['poster_url'], 'http') === 0 ? $movie['poster_url'] : '../' . $movie['poster_url']) : 
                                '../assets/images/default-poster.jpg';
                        ?>
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                                <div class="movie-card">
                                    <a href="movie.php?id=<?= $movie['id'] ?>">
                                        <div class="movie-poster">
                                            <img src="<?= $posterUrl ?>" alt="<?= $movie['title'] ?>">
                                            <div class="play-btn">
                                                <i class="bi bi-play-fill"></i>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <h5 class="mb-2 text-white"><?= $movie['title'] ?></h5>
                                            <p class="text-light small mb-2"><?= $movie['genre'] ?> • <?= $movie['release_year'] ?> • <span class="badge">HD</span></p>
                                            <div class="d-flex align-items-center">
                                                <small class="text-light"><i class="bi bi-star-fill text-warning me-1"></i><?= $movie['rating'] ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?= generatePagination($newestPage, $newestTotalPages, 'newest_page', 'newest-section', $allPageParams) ?>
            </section>
            
            <!-- Dynamic Genre Sections -->
            <?php foreach ($allGenres as $genre): 
                $genreId = $genre['id'];
                $genreName = $genre['name'];
                $genreSlug = $genrePages[$genreId]['slug'];
                $genrePage = $genrePages[$genreId]['page'];
                $genreParam = $genrePages[$genreId]['param'];
                
                // Skip if there's no movies for this genre
                if ($genreTotalPages[$genreId] == 0) continue;
                
                $movies = $genreMovies[$genreId];
            ?>
            <section class="movie-section mb-5" id="<?= $genreSlug ?>-section">
                <h3 class="category-title"><?= $genreName ?> Movies</h3>
                <div class="movie-slider" id="<?= $genreSlug ?>-content">
                    <div class="row g-4">
                        <?php while($movies && $movie = $movies->fetch_assoc()): 
                            // Simple direct poster URL handling without helpers or YouTube fallback
                            $posterUrl = !empty($movie['poster_url']) ? 
                                (strpos($movie['poster_url'], 'http') === 0 ? $movie['poster_url'] : '../' . $movie['poster_url']) : 
                                '../assets/images/default-poster.jpg';
                        ?>
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                                <div class="movie-card">
                                    <a href="movie.php?id=<?= $movie['id'] ?>">
                                        <div class="movie-poster">
                                            <img src="<?= $posterUrl ?>" alt="<?= $movie['title'] ?>">
                                            <div class="play-btn">
                                                <i class="bi bi-play-fill"></i>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <h5 class="mb-2 text-white"><?= $movie['title'] ?></h5>
                                            <p class="text-light small mb-2"><?= $movie['genre'] ?> • <?= $movie['release_year'] ?> • <span class="badge">HD</span></p>
                                            <div class="d-flex align-items-center">
                                                <small class="text-light"><i class="bi bi-star-fill text-warning me-1"></i><?= $movie['rating'] ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?= generatePagination($genrePage, $genreTotalPages[$genreId], $genreParam, $genreSlug.'-section', $allPageParams) ?>
            </section>
            <?php endforeach; ?>
            
        </div>
    </div>
    
    <!-- Footer -->
    <?php include_once('../components/footer.php'); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
    // Simple AJAX Pagination
    document.addEventListener('DOMContentLoaded', function() {
        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.ajax-pagination')) {
                e.preventDefault();
                
                const link = e.target.closest('.ajax-pagination');
                const pagination = link.closest('.pagination');
                const section = link.closest('.movie-section');
                
                const sectionId = pagination.dataset.section;
                const pageParam = pagination.dataset.pageParam;
                const page = parseInt(link.dataset.page);
                
                loadPage(section, sectionId, pageParam, page);
            }
        });
        
        function loadPage(section, sectionId, pageParam, page) {
            const contentDiv = section.querySelector('.movie-slider');
            const paginationDiv = section.querySelector('.pagination').closest('nav');
            
            // Add loading state
            section.style.opacity = '0.6';
            section.style.pointerEvents = 'none';
            
            // Scroll to section
            const navbarHeight = 80;
            const sectionTop = section.offsetTop - navbarHeight;
            window.scrollTo({
                top: sectionTop,
                behavior: 'smooth'
            });
            
            // Prepare form data
            const formData = new FormData();
            formData.append('section', getSectionType(pageParam));
            formData.append('page', page);
            
            // Make AJAX request
            fetch('../components/browse_load_section.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update content
                    contentDiv.innerHTML = data.content;
                    paginationDiv.outerHTML = data.pagination;
                    
                    // Update URL without refresh
                    const url = new URL(window.location);
                    url.searchParams.set(pageParam, page);
                    history.replaceState(null, '', url);
                } else {
                    console.error('Error:', data.message);
                }
                
                // Remove loading state
                section.style.opacity = '1';
                section.style.pointerEvents = 'auto';
            })
            .catch(error => {
                console.error('Network error:', error);
                
                // Remove loading state
                section.style.opacity = '1';
                section.style.pointerEvents = 'auto';
            });
        }
        
        function getSectionType(pageParam) {
            if (pageParam === 'trending_page') return 'trending';
            if (pageParam === 'newest_page') return 'newest';
            
            // Better handling for dynamic genre page params
            if (pageParam.endsWith('_page')) {
                // Extract genre name and convert to slug format for the section parameter
                const genreName = pageParam.replace('_page', '');
                const genreSlug = genreName.replace(/_/g, '-');
                return genreSlug;
            }
            
            return 'trending';
        }
    });
    </script>
</body>
</html>
