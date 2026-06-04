<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// დაცვის მექანიზმი: თუ მომხმარებელი არ არის შესული, გადავისროლოთ ლოგინზე
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';
?>

<section class="profile-container">
    <div class="profile-card">
        <div class="profile-avatar">
            <span>👤</span>
        </div>
        <h2>მოგესალმებით, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</h2>
        <p class="role-badge">სტატუსი: <strong><?php echo htmlspecialchars($_SESSION['user_role']); ?></strong></p>
        
        <hr>
        
        <div class="profile-actions">
            <h3>სწრაფი ნავიგაცია</h3>
            <p>თქვენ წარმატებით გაიარეთ ავტორიზაცია. შეგიძლიათ გადახვიდეთ გალერეაში ფოტოების ასატვირთად და სამართავად.</p>
            <a href="gallery.php" class="btn-primary">გალერეაში გადასვლა</a>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>