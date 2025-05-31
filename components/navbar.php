<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $isLoggedIn ? $_SESSION['name'] : '';
$userInitial = $isLoggedIn ? substr($_SESSION['name'], 0, 1) : '';

// Determine current page for active menu state
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Fix path relativity issue - determine if we are in pages directory
$isInPagesDir = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
$rootPath = $isInPagesDir ? '../' : '';
?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="<?= $rootPath ?>index.php">
            <i class="fas fa-play-circle"></i> StreamFlix
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            
            <?php if($isLoggedIn): ?>
                <!-- Main navigation links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $isInPagesDir ? '' : 'pages/' ?>browse.php">
                            Browse
                        </a>
                    </li>
                </ul>
                
                <div class="ms-auto d-flex align-items-center">
                    <!-- Search form -->
                    <form class="search-form me-3" action="<?= $isInPagesDir ? '' : 'pages/' ?>search.php" method="get">
                        <div class="search-container">
                            <input class="search-input" type="search" name="q" placeholder="Search movies..." aria-label="Search">
                            <button class="search-btn" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- User dropdown menu -->
                    <div class="dropdown user-dropdown">
                        <a href="#" class="user-profile-btn dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                <?= $userInitial ?>
                            </div>
                            <span class="user-name d-none d-sm-inline"><?= $userName ?></span>
                        </a>
                        <ul class="dropdown-menu user-dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?= $isInPagesDir ? '' : 'pages/' ?>profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= $isInPagesDir ? '' : 'pages/' ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a></li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <div class="ms-auto d-flex align-items-center">
                    <a href="<?= $isInPagesDir ? '' : 'pages/' ?>login.php" class="signin-link me-3">Sign In</a>
                    <a href="<?= $isInPagesDir ? '' : 'pages/' ?>register.php" class="btn btn-primary-gradient btn-sm">Sign Up</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
