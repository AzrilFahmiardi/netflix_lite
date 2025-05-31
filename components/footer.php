<?php
// Determine if we are in root directory or pages directory for correct paths
$rootPath = '';
if (strpos($_SERVER['SCRIPT_NAME'], '/pages/') !== false) {
    $rootPath = '../';
}
?>

<footer class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h5 class="gradient-text">StreamFlix</h5>
                <p class="text-light">Your premium streaming destination for unlimited entertainment.</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-white">Company</h6>
                <ul class="list-unstyled text-light">
                    <li><a href="#" class="text-light text-decoration-none">About Us</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Careers</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Press</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-white">Support</h6>
                <ul class="list-unstyled text-light">
                    <li><a href="#" class="text-light text-decoration-none">Help Center</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Contact Us</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Terms of Service</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-white">Follow Us</h6>
                <div class="d-flex gap-3">
                    <a href="#" class="text-light"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-light"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-light"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-light"><i class="fab fa-youtube fa-lg"></i></a>
                </div>
            </div>
        </div>
        
        <hr class="my-4" style="border-color: rgba(108, 92, 231, 0.2);">
        
        <div class="text-center text-light">
            <p>&copy; 2023 StreamFlix. All rights reserved. | Made with <i class="fas fa-heart text-danger"></i> for movie lovers</p>
        </div>
    </div>
</footer>
