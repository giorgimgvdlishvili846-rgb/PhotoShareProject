<?php 
// სესიის დაწყება
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'classes/User.php';

$error_message = '';

// თუ მომხმარებელმა შეავსო შესვლის ფორმა (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $user = new User();
        $result = $user->login($email, $password);

        if ($result === true) {
            // წარმატებული შესვლისას გადავიყვანოთ პროფილის გვერდზე
            header("Location: profile.php");
            exit();
        } else {
            $error_message = $result;
        }
    } else {
        $error_message = "გთხოვთ შეავსოთ ყველა ველი!";
    }
}

// ჰედერის შემოტანა
include 'includes/header.php'; 
?>

<section class="auth-container">
    <h2>სისტემაში შესვლა</h2>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div id="js-login-error" class="alert alert-danger" style="display: none;"></div>

    <form id="loginForm" action="login.php" method="POST">
        <div class="form-group">
            <label for="login_email">ელ.ფოსტა:</label>
            <input type="email" id="login_email" name="email" placeholder="example@domain.com">
        </div>
        
        <div class="form-group">
            <label for="login_password">პაროლი:</label>
            <input type="password" id="login_password" name="password" placeholder="შეიყვანეთ პაროლი">
        </div>

        <button type="submit" class="btn-submit">შესვლა</button>
    </form>
    
    <p class="auth-link">ჯერ არ გაქვთ ანგარიში? <a href="register.php">დარეგისტრირდით აქედან</a></p>
</section>

<?php 
// ფუტერის შემოტანა
include 'includes/footer.php'; 
?>