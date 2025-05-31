<?php
// Include session check
require_once('../components/session_check.php');

// Require user to be logged in
requireLogin();

// Include database connection
require_once('../config/database.php');

// Initialize response array
$response = [
    'success' => false,
    'message' => 'An error occurred'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from session
    $user_id = getCurrentUserId();
    
    // Get form data
    $movie_id = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';
    
    // Validate form data
    if ($movie_id <= 0) {
        $response['message'] = 'Invalid movie ID.';
    } elseif ($rating <= 0 || $rating > 5) {
        $response['message'] = 'Rating must be between 1 and 5.';
    } elseif (empty($review_text)) {
        $response['message'] = 'Review text cannot be empty.';
    } else {
        // Check if user has already reviewed this movie
        $sql = "SELECT * FROM user_reviews WHERE user_id = $user_id AND movie_id = $movie_id";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            // Update existing review
            $sql = "UPDATE user_reviews SET rating = $rating, review_text = '$review_text', updated_at = NOW() WHERE user_id = $user_id AND movie_id = $movie_id";
            
            if ($conn->query($sql) === TRUE) {
                $response['success'] = true;
                $response['message'] = 'Review updated successfully.';
                
                // Update movie's average rating
                updateMovieRating($conn, $movie_id);
            } else {
                $response['message'] = 'Error updating review: ' . $conn->error;
            }
        } else {
            // Insert new review
            $sql = "INSERT INTO user_reviews (user_id, movie_id, rating, review_text, created_at) VALUES ($user_id, $movie_id, $rating, '$review_text', NOW())";
            
            if ($conn->query($sql) === TRUE) {
                $response['success'] = true;
                $response['message'] = 'Review added successfully.';
                
                // Update movie's average rating
                updateMovieRating($conn, $movie_id);
            } else {
                $response['message'] = 'Error adding review: ' . $conn->error;
            }
        }
    }
}

// Function to update movie's average rating
function updateMovieRating($conn, $movie_id) {
    $sql = "SELECT AVG(rating) as average_rating FROM user_reviews WHERE movie_id = $movie_id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $average_rating = $result->fetch_assoc()['average_rating'];
        
        // Update movie's rating
        $sql = "UPDATE movies SET rating = $average_rating WHERE id = $movie_id";
        $conn->query($sql);
    }
}

// If AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If regular form submission
if (isset($_POST['redirect_url'])) {
    $redirect_url = $_POST['redirect_url'];
} else {
    $redirect_url = "movie.php?id=" . $movie_id;
}

// Add success/error message to session for display after redirect
if ($response['success']) {
    $_SESSION['review_message'] = $response['message'];
    $_SESSION['review_status'] = 'success';
} else {
    $_SESSION['review_message'] = $response['message'];
    $_SESSION['review_status'] = 'error';
}

// Redirect back to movie page
header("Location: ../pages/" . $redirect_url);
exit;
?>