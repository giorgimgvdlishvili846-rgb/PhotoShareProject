<?php 
// ჩავრთოთ სესია და კლასი
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'classes/User.php';

$error_message = '';
$success_message = '';

// თუ მომხმარებელმა ფორმა გამოაგზავნა (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $user = new User();
        $result = $user->register($email, $password);

        if ($result === true) {
            $success_message = "რეგისტრაცია წარმატებით დასრულდა! შეგიძლიათ შეხვიდეთ სისტემაში.";
        } else {
            $error_message = $result;
        }
    } else {
        $error_message = "გთხოვთ შეავსოთ ყველა ველი!";
    }
}

// ჰედერის შემოტანა (HTML სტრუქტურისთვის)
include 'includes/header.php'; 
?>

<section class="auth-container">
    <h2>მომხმარებლის რეგისტრაცია</h2>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div id="js-error" class="alert alert-danger" style="display: none;"></div>

    <form id="registerForm" action="register.php" method="POST">
        <div class="form-group">
            <label for="email">ელ.ფოსტა:</label>
            <input type="email" id="email" name="email" placeholder="example@domain.com">
        </div>
        
        <div class="form-group">
            <label for="password">პაროლი:</label>
            <input type="password" id="password" name="password" placeholder="მინიმუმ 6 სიმბოლო">
        </div>

        <div class="form-group">
            <label for="confirm_password">გაიმეორეთ პაროლი:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="განმეორებითი პაროლი">
        </div>

        <button type="submit" class="btn-submit">რეგისტრაცია</button>
    </form>
    
    <p class="auth-link">უკვე გაქვთ ანგარიში? <a href="login.php">შესვლა აქედან</a></p>
</section>

<?php 
// ფუტერის შემოტანა
include 'includes/footer.php'; 
?>