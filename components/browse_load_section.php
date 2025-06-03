<?php
header('Content-Type: application/json');

try {
    require_once('../config/database.php');
    require_once('../components/session_check.php');
    
    // Check if user is logged in
    requireLogin();
    
    // Check if request is AJAX
    if (empty($_POST['section']) || empty($_POST['page'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request parameters']);
        exit;
    }
    
    // Get parameters
    $section = $_POST['section'];
    $page = max(1, (int)$_POST['page']);
    $moviesPerPage = 6;
    $offset = ($page - 1) * $moviesPerPage;
    
    // Function to generate pagination
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
    
    // Get movies based on section type
    switch ($section) {
        case 'trending':
            $countQuery = "SELECT COUNT(*) as total FROM movies";
            $query = "SELECT * FROM movies ORDER BY view_count DESC LIMIT $moviesPerPage OFFSET $offset";
            $pageParam = 'trending_page';
            $sectionId = 'trending-section';
            break;
        case 'newest':
            $countQuery = "SELECT COUNT(*) as total FROM movies";
            $query = "SELECT * FROM movies ORDER BY release_year DESC LIMIT $moviesPerPage OFFSET $offset";
            $pageParam = 'newest_page';
            $sectionId = 'newest-section';
            break;
        case 'action':
            $countQuery = "SELECT COUNT(DISTINCT m.id) as total FROM movies m 
                         JOIN movie_genres mg ON m.id = mg.movie_id 
                         JOIN genres g ON mg.genre_id = g.id 
                         WHERE g.name = 'Action'";
            $query = "SELECT m.* FROM movies m 
                    JOIN movie_genres mg ON m.id = mg.movie_id 
                    JOIN genres g ON mg.genre_id = g.id 
                    WHERE g.name = 'Action' 
                    ORDER BY m.view_count DESC
                    LIMIT $moviesPerPage OFFSET $offset";
            $pageParam = 'action_page';
            $sectionId = 'action-section';
            break;
        case 'scifi':
            $countQuery = "SELECT COUNT(DISTINCT m.id) as total FROM movies m 
                        JOIN movie_genres mg ON m.id = mg.movie_id 
                        JOIN genres g ON mg.genre_id = g.id 
                        WHERE g.name = 'Sci-Fi'";
            $query = "SELECT m.* FROM movies m 
                   JOIN movie_genres mg ON m.id = mg.movie_id 
                   JOIN genres g ON mg.genre_id = g.id 
                   WHERE g.name = 'Sci-Fi' 
                   ORDER BY m.view_count DESC
                   LIMIT $moviesPerPage OFFSET $offset";
            $pageParam = 'scifi_page';
            $sectionId = 'scifi-section';
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid section']);
            exit;
    }
    
    // Get total count for pagination
    $countResult = $conn->query($countQuery);
    $total = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($total / $moviesPerPage);
    
    // Get movies
    $movies = $conn->query($query);
    
    // Generate HTML content
    ob_start();
    ?>
    <div class="row g-4">
        <?php while($movie = $movies->fetch_assoc()): 
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
    <?php
    $content = ob_get_clean();
    
    // Generate pagination
    $pagination = generatePagination($page, $totalPages, $pageParam, $sectionId);
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'content' => $content,
        'pagination' => $pagination,
        'currentPage' => $page,
        'totalPages' => $totalPages
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
