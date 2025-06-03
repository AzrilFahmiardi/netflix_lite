<?php
require_once('../components/session_check.php');

requireLogin();

require_once('../config/database.php');

$response = [
    'success' => false,
    'message' => 'An error occurred'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from session
    $user_id = getCurrentUserId();
    
    // Get form data
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $movie_id = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;
    
    switch ($action) {
        case 'add_to_watchlist':
            // Add movie to watchlist
            $sql = "INSERT INTO watchlist (user_id, movie_id) VALUES ('$user_id', '$movie_id')";
            if ($conn->query($sql) === TRUE) {
                $response['success'] = true;
                $response['message'] = 'Movie added to watchlist';
            } else {
                $response['message'] = 'Error adding to watchlist: ' . $conn->error;
            }
            break;
            
        case 'remove_from_watchlist':
            // Remove movie from watchlist
            $sql = "DELETE FROM watchlist WHERE user_id = '$user_id' AND movie_id = '$movie_id'";
            if ($conn->query($sql) === TRUE) {
                $response['success'] = true;
                $response['message'] = 'Movie removed from watchlist';
            } else {
                $response['message'] = 'Error removing from watchlist: ' . $conn->error;
            }
            break;
            
        case 'add_review':
            // Add or update review for a movie
            $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
            $review_text = isset($_POST['review_text']) ? $_POST['review_text'] : '';
            
            $check_review = $conn->query("SELECT * FROM user_reviews WHERE user_id = '$user_id' AND movie_id = '$movie_id'");
            
            if ($check_review && $check_review->num_rows > 0) {
                $sql = "UPDATE user_reviews SET rating = '$rating', review_text = '$review_text' WHERE user_id = '$user_id' AND movie_id = '$movie_id'";
                if ($conn->query($sql) === TRUE) {
                    $response['success'] = true;
                    $response['message'] = 'Review updated successfully';
                } else {
                    $response['message'] = 'Error updating review: ' . $conn->error;
                }
            } else {
                // Add new review
                $sql = "INSERT INTO user_reviews (user_id, movie_id, rating, review_text) VALUES ('$user_id', '$movie_id', '$rating', '$review_text')";
                if ($conn->query($sql) === TRUE) {
                    $response['success'] = true;
                    $response['message'] = 'Review added successfully';
                } else {
                    $response['message'] = 'Error adding review: ' . $conn->error;
                }
            }
            break;
            
        default:
            $response['message'] = 'Invalid action';
            break;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>