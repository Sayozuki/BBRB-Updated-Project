<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="header_section">
    <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo-brb.png" alt="Barangay Logo" style="max-width:120px; height:auto;">
                    <span>Barangay Blue Ridge B</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="s-1"></span>
                <span class="s-2"></span>
                <span class="s-3"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="announcement.php">Announcements</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                        <!-- Updated Logout Button -->
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-toggle="modal" data-target="#userLogoutModal">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</header>

<!-- Logout Confirmation Popup -->
<div id="userLogoutPopup" class="custom-popup">
    <div class="custom-popup-content">
        <div class="custom-popup-header">
            <h5>Confirm Logout</h5>
            <span class="custom-popup-close" id="closePopupBtn">&times;</span>
        </div>
        <div class="custom-popup-body">
           <p>Are you sure you want to log out?</p>
        </div>
        <div class="custom-popup-footer">
            <button type="button" class="btn btn-secondary" id="cancelPopupBtn">Cancel</button>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

<style>
.header_section, .custom_nav-container, .navbar {
    background-color: #001f4d !important;
}
.header_section .navbar-nav .nav-link,
.header_section .navbar-brand,
.header_section .navbar-brand span {
    color: #fff !important;
}
.header_section .navbar-nav .nav-link:hover,
.header_section .navbar-nav .nav-link.active {
    color: #ffd700 !important;
}

/* Custom Popup Styles */
.custom-popup {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0; top: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.5);
    justify-content: center; align-items: center;
}
.custom-popup-content {
    background: #fff;
    border-radius: 8px;
    width: 350px;
    max-width: 90vw;
    box-shadow: 0 4px 24px rgba(0,0,0,0.2);
    animation: popupFadeIn 0.2s;
}
@keyframes popupFadeIn {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.custom-popup-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 16px 20px 8px 20px;
    border-bottom: 1px solid #eee;
}
.custom-popup-close {
    cursor: pointer;
    font-size: 1.5rem;
    color: #888 !important; /* Gray color */
}
.custom-popup-body {
    padding: 16px 20px;
}
.custom-popup-footer {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 8px 20px 16px 20px;
    border-top: 1px solid #eee;
}
#cancelPopupBtn {
    background-color: #007bff !important; /* Bootstrap blue */
    color: white !important;               /* White text */
    border: none;
}
#cancelPopupBtn:hover, #cancelPopupBtn:focus {
    background-color:rgb(32, 136, 248) !important; /* Darker blue on hover */
    color: #fff !important;               /* White text on hover */
}
.custom-popup-header h5,
.custom-popup-body p {
    color: #000 !important;
}
</style>

<script>
// Show popup when logout link is clicked
document.addEventListener('DOMContentLoaded', function() {
    var logoutLinks = document.querySelectorAll('[data-toggle="modal"][data-target="#userLogoutModal"]');
    var popup = document.getElementById('userLogoutPopup');
    var closeBtn = document.getElementById('closePopupBtn');
    var cancelBtn = document.getElementById('cancelPopupBtn');

    logoutLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            popup.style.display = 'flex';
        });
    });

    [closeBtn, cancelBtn].forEach(function(btn) {
        btn.addEventListener('click', function() {
            popup.style.display = 'none';
        });
    });

    // Optional: close popup when clicking outside content
    popup.addEventListener('click', function(e) {
        if (e.target === popup) popup.style.display = 'none';
    });
});
</script>