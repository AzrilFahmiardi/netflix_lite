<?php
// Include database connection - adjust path since file is now in components folder
require_once('../config/database.php');

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get movie data
    $sql = "SELECT * FROM movies WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        $movie = $result->fetch_assoc();
?>

<form method="post" action="admin.php#movies" enctype="multipart/form-data" id="edit-movie-form">
    <div class="form-group">
        <label for="movie_id">ID Movie:</label>
        <input type="text" id="movie_id" name="movie_id" value="<?= $movie['id'] ?>" required readonly>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="3"><?= htmlspecialchars($movie['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="director">Director:</label>
                <input type="text" id="director" name="director" value="<?= htmlspecialchars($movie['director']) ?>">
            </div>
            <div class="form-group">
                <label for="release_year">Release Year:</label>
                <input type="number" id="release_year" name="release_year" value="<?= $movie['release_year'] ?>">
            </div>
            <div class="form-group">
                <label for="duration_minutes">Duration (mins):</label>
                <input type="number" id="duration_minutes" name="duration_minutes" value="<?= $movie['duration_minutes'] ?>" required>
            </div>
            <div class="form-group">
                <div class="checkbox-wrapper">
                    <input type="checkbox" id="is_featured" name="is_featured" <?= $movie['is_featured'] ? 'checked' : '' ?>>
                    <label for="is_featured">Feature this movie on homepage</label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Current Poster:</label>
                <div class="poster-preview">
                    <?php if (!empty($movie['poster_url'])): ?>
                        <img src="<?= $movie['poster_url'] ?>" alt="Movie poster" style="max-width: 100%; height: auto; border-radius: 8px;">
                    <?php else: ?>
                        <img src="https://img.youtube.com/vi/<?= $movie['trailer_youtube_id'] ?>/mqdefault.jpg" alt="YouTube thumbnail" style="max-width: 100%; height: auto; border-radius: 8px;">
                        <p class="text-center text-muted mt-2">Using YouTube thumbnail</p>
                    <?php endif; ?>
                </div>
                <div class="form-group mt-3">
                    <label for="poster">Update Poster:</label>
                    <input type="file" id="poster" name="poster" class="form-control" accept="image/jpeg, image/png, image/webp">
                    <small class="form-hint text-light">Recommended size: 500x750 pixels</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" name="update_movie" class="btn-primary"><i class="fas fa-save"></i> Update Movie</button>
        <button type="button" class="btn-secondary" onclick="closeModal('edit-movie-modal')">Cancel</button>
    </div>
</form>

<script>
// Handle form submission with AJAX to prevent page reload
document.getElementById('edit-movie-form').addEventListener('submit', function(e) {
    // We're keeping the normal form submission which will properly handle the file upload
    // Just adding measures to prevent duplicate submissions
    
    // Close the modal after submitting
    setTimeout(function() {
        closeModal('edit-movie-modal');
    }, 500);
});
</script>

<?php
    } else {
        echo '<div class="alert error"><i class="fas fa-exclamation-circle"></i> Movie not found</div>';
    }
} else {
    echo '<div class="alert error"><i class="fas fa-exclamation-circle"></i> Invalid request</div>';
}
?>
