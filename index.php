<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// შემოგვაქვს ჰედერი, სადაც სტილის ლინკია
include 'includes/header.php'; 
?>

<section class="hero-section" style="text-align: center; padding: 5rem 2rem; background: linear-gradient(135deg, #f4f6f8, #e9ecef); border-radius: 12px; margin-top: 2rem; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);">
    <h1 style="font-size: 2.5rem; color: #1e293b; margin-bottom: 1rem;">📸 კეთილი იყოს თქვენი მობრძანება PhotoShare-ზე!</h1>
    <p style="font-size: 1.2rem; color: #555; max-width: 600px; margin: 0 auto 2rem auto;">ეს არის უსაფრთხო და თანამედროვე პლატფორმა, სადაც შეგიძლიათ ატვირთოთ, გააზიაროთ და მართოთ თქვენი საყვარელი ფოტოები.</p>
    
    <?php if(!isset($_SESSION['user_id'])): ?>
        <a href="register.php" class="btn-primary" style="max-width: 200px; display: inline-block;">დაიწყე ახლავე</a>
    <?php else: ?>
        <a href="gallery.php" class="btn-primary" style="max-width: 200px; display: inline-block;">გალერეის ნახვა</a>
    <?php endif; ?>
</section>

<?php 
// შემოგვაქვს ფუტერი
include 'includes/footer.php'; 
?>