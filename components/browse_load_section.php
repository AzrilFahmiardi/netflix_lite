<?php
header('Content-Type: application/json');

try {
    require_once('../config/database.php');
    
    $section = $_POST['section'] ?? '';
    $page = max(1, intval($_POST['page'] ?? 1));
    $moviesPerPage = 6;
    $offset = ($page - 1) * $moviesPerPage;
    
    // Define section configurations
    $sections = [
        'trending' => [
            'query' => "SELECT * FROM movies ORDER BY view_count DESC LIMIT $moviesPerPage OFFSET $offset",
            'count_query' => "SELECT COUNT(*) as total FROM movies",
            'page_param' => 'trending_page',
            'section_id' => 'trending-section'
        ],
        'newest' => [
            'query' => "SELECT * FROM movies ORDER BY release_year DESC LIMIT $moviesPerPage OFFSET $offset",
            'count_query' => "SELECT COUNT(*) as total FROM movies",
            'page_param' => 'newest_page',
            'section_id' => 'newest-section'
        ],
        'action' => [
            'query' => "SELECT DISTINCT m.* FROM movies m 
                       JOIN movie_genres mg ON m.id = mg.movie_id 
                       JOIN genres g ON mg.genre_id = g.id 
                       WHERE g.name = 'Action' 
                       ORDER BY m.view_count DESC
                       LIMIT $moviesPerPage OFFSET $offset",
            'count_query' => "SELECT COUNT(DISTINCT m.id) as total FROM movies m 
                              JOIN movie_genres mg ON m.id = mg.movie_id 
                              JOIN genres g ON mg.genre_id = g.id 
                              WHERE g.name = 'Action'",
            'page_param' => 'action_page',
            'section_id' => 'action-section'
        ],
        'scifi' => [
            'query' => "SELECT DISTINCT m.* FROM movies m 
                       JOIN movie_genres mg ON m.id = mg.movie_id 
                       JOIN genres g ON mg.genre_id = g.id 
                       WHERE g.name = 'Sci-Fi' 
                       ORDER BY m.view_count DESC
                       LIMIT $moviesPerPage OFFSET $offset",
            'count_query' => "SELECT COUNT(DISTINCT m.id) as total FROM movies m 
                              JOIN movie_genres mg ON m.id = mg.movie_id 
                              JOIN genres g ON mg.genre_id = g.id 
                              WHERE g.name = 'Sci-Fi'",
            'page_param' => 'scifi_page',
            'section_id' => 'scifi-section'
        ]
    ];
    
    if (!isset($sections[$section])) {
        throw new Exception('Invalid section');
    }
    
    $config = $sections[$section];
    
    // Get movies
    $movies = $conn->query($config['query']);
    if (!$movies) {
        throw new Exception('Failed to fetch movies: ' . $conn->error);
    }
    
    // Get total count for pagination
    $countResult = $conn->query($config['count_query']);
    $total = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($total / $moviesPerPage);
    
    // Generate content HTML
    ob_start();
    ?>
    <div class="row g-4">
        <?php while($movie = $movies->fetch_assoc()): 
            $posterUrl = !empty($movie['poster_url']) ? 
                (strpos($movie['poster_url'], 'http') === 0 ? $movie['poster_url'] : '../' . $movie['poster_url']) : 
                'https://img.youtube.com/vi/' . $movie['trailer_youtube_id'] . '/mqdefault.jpg';
        ?>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="movie-card">
                    <a href="movie.php?id=<?= $movie['id'] ?>">
                        <div class="movie-poster">
                            <img src="<?= htmlspecialchars($posterUrl) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                            <div class="play-btn">
                                <i class="bi bi-play-fill"></i>
                            </div>
                        </div>
                        <div class="p-3">
                            <h5 class="mb-2 text-white"><?= htmlspecialchars($movie['title']) ?></h5>
                            <p class="text-light small mb-2"><?= htmlspecialchars($movie['genre']) ?> • <?= $movie['release_year'] ?> • <span class="badge">HD</span></p>
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
    $pagination = generateAjaxPagination($page, $totalPages, $config['page_param'], $config['section_id']);
    
    echo json_encode([
        'success' => true,
        'content' => $content,
        'pagination' => $pagination
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function generateAjaxPagination($currentPage, $totalPages, $pageParam, $sectionId) {
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
