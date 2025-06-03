<?php
header('Content-Type: application/json');

try {
    require_once('../config/database.php');
    require_once('../components/session_check.php');
    
    requireLogin();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }
    
    $section = isset($_POST['section']) ? $_POST['section'] : 'trending';
    $page = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
    
    $moviesPerPage = 6;
    $offset = ($page - 1) * $moviesPerPage;
    
    // Initialize variables
    $movies = null;
    $totalPages = 0;
    $pageParam = '';
    $sectionId = '';
    
    // Generate pagination
    function generatePagination($currentPage, $totalPages, $pageParam, $sectionId) {
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
    
    if ($section === 'trending') {
        // Trending Movies
        $countQuery = "SELECT COUNT(*) as total FROM movies";
        $countResult = $conn->query($countQuery);
        $total = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($total / $moviesPerPage);
        
        $moviesQuery = "SELECT * FROM movies ORDER BY view_count DESC LIMIT $moviesPerPage OFFSET $offset";
        $movies = $conn->query($moviesQuery);
        
        $pageParam = 'trending_page';
        $sectionId = 'trending-section';
    } 
    else if ($section === 'newest') {
        // Newest Movies
        $countQuery = "SELECT COUNT(*) as total FROM movies";
        $countResult = $conn->query($countQuery);
        $total = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($total / $moviesPerPage);
        
        $moviesQuery = "SELECT * FROM movies ORDER BY release_year DESC LIMIT $moviesPerPage OFFSET $offset";
        $movies = $conn->query($moviesQuery);
        
        $pageParam = 'newest_page';
        $sectionId = 'newest-section';
    } 
    else {
        $genreSlug = $section;
        $genreName = str_replace('-', ' ', $genreSlug); 
        
        // Query genre by name
        $genreQuery = "SELECT id, name FROM genres WHERE LOWER(REPLACE(name, ' ', '-')) = ? OR LOWER(name) = ?";
        $stmt = $conn->prepare($genreQuery);
        $genreNameLower = strtolower($genreName);
        $genreSlugLower = strtolower($genreSlug);
        $stmt->bind_param("ss", $genreSlugLower, $genreNameLower);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $genre = $result->fetch_assoc();
            $genreId = $genre['id'];
            $actualGenreName = $genre['name'];
            
            $countQuery = "SELECT COUNT(DISTINCT m.id) as total FROM movies m 
                         JOIN movie_genres mg ON m.id = mg.movie_id 
                         JOIN genres g ON mg.genre_id = g.id 
                         WHERE g.id = ?";
            $stmt = $conn->prepare($countQuery);
            $stmt->bind_param("i", $genreId);
            $stmt->execute();
            $total = $stmt->get_result()->fetch_assoc()['total'];
            $totalPages = ceil($total / $moviesPerPage);
            
            $moviesQuery = "SELECT m.* FROM movies m 
                          JOIN movie_genres mg ON m.id = mg.movie_id 
                          JOIN genres g ON mg.genre_id = g.id 
                          WHERE g.id = ? 
                          ORDER BY m.view_count DESC
                          LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($moviesQuery);
            $stmt->bind_param("iii", $genreId, $moviesPerPage, $offset);
            $stmt->execute();
            $movies = $stmt->get_result();
            
            $pageParam = strtolower(str_replace(' ', '_', $actualGenreName)) . '_page';
            $sectionId = $genreSlug . '-section';
        } else {
            $countQuery = "SELECT COUNT(*) as total FROM movies";
            $countResult = $conn->query($countQuery);
            $total = $countResult->fetch_assoc()['total'];
            $totalPages = ceil($total / $moviesPerPage);
            
            $moviesQuery = "SELECT * FROM movies ORDER BY view_count DESC LIMIT $moviesPerPage OFFSET $offset";
            $movies = $conn->query($moviesQuery);
            
            $pageParam = 'trending_page';
            $sectionId = 'trending-section';
        }
    }
    
    // Generate HTML content
    ob_start();
    ?>
    <div class="row g-4">
        <?php while($movie = $movies->fetch_assoc()): 
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
    <?php
    $content = ob_get_clean();
    
    // Generate pagination
    $pagination = generatePagination($page, $totalPages, $pageParam, $sectionId);
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'content' => $content,
        'pagination' => $pagination,
        'section' => $section,
        'page' => $page,
        'totalPages' => $totalPages
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
