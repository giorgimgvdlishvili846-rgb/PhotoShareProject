<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhotoShare - ფოტოების პლატფორმა</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <div class="header-container">
        <a href="index.php" class="logo">📸 PhotoShare</a>
        <nav>
            <ul>
                <li><a href="index.php">მთავარი</a></li>
                <li><a href="gallery.php">გალერეა</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">პროფილი</a></li>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin'): ?>
                        <li><a href="admin.php" class="admin-link">ადმინ პანელი</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="btn-logout">გამოსვლა</a></li>
                <?php else: ?>
                    <li><a href="login.php">შესვლა</a></li>
                    <li><a href="register.php" class="btn-nav-reg">რეგისტრაცია</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<main class="main-content">