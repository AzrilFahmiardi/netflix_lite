<?php
require_once('../components/session_check.php');

requireLogin();

require_once('../config/database.php');

$response = [
    'success' => false,
    'message' => 'An error occurred',
    'in_watchlist' => false
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from session
    $user_id = getCurrentUserId();
    
    // Get movie ID from POST data
    $movie_id = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;
    
    if ($movie_id <= 0) {
        $response['message'] = 'Invalid movie selected';
    } else {
        $check_sql = "SELECT id FROM watchlist WHERE user_id = ? AND movie_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $movie_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Remove from watchlist
            $delete_sql = "DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("ii", $user_id, $movie_id);
            
            if ($delete_stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Movie removed from watchlist';
                $response['in_watchlist'] = false;
                $response['action'] = 'removed';
            } else {
                $response['message'] = 'Error removing movie from watchlist';
            }
        } else {
            // Add to watchlist
            $add_sql = "INSERT INTO watchlist (user_id, movie_id) VALUES (?, ?)";
            $add_stmt = $conn->prepare($add_sql);
            $add_stmt->bind_param("ii", $user_id, $movie_id);
            
            if ($add_stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Movie added to watchlist';
                $response['in_watchlist'] = true;
                $response['action'] = 'added';
            } else {
                $response['message'] = 'Error adding movie to watchlist';
            }
        }
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
