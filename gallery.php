<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'classes/Post.php';

$postManager = new Post();
$error_message = '';
$success_message = '';

// ფოტოს ატვირთვის დამუშავება
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_photo'])) {
    if (!isset($_SESSION['user_id'])) {
        $error_message = "ფოტოს ასატვირთად გთხოვთ გაიაროთ ავტორიზაცია.";
    } else {
        $title = trim($_POST['title']);
        $file = $_FILES['photo'];

        if (!empty($title) && !empty($file['name'])) {
            $result = $postManager->create($_SESSION['user_id'], $title, $file);
            if ($result === true) {
                $success_message = "ფოტო წარმატებით აიტვირთა!";
            } else {
                $error_message = $result;
            }
        } else {
            $error_message = "გთხოვთ შეავსოთ სათაური და აირჩიოთ ფაილი!";
        }
    }
}

// ფოტოს წაშლის დამუშავება
if (isset($_GET['delete_id'])) {
    if (!isset($_SESSION['user_id'])) {
        $error_message = "სურათის წასაშლელად საჭიროა ავტორიზაცია.";
    } else {
        $delete_result = $postManager->delete($_GET['delete_id'], $_SESSION['user_id'], $_SESSION['user_role'] ?? 'User');
        if ($delete_result === true) {
            $success_message = "ფოტო წარმატებით წაიშალა!";
        } else {
            $error_message = $delete_result;
        }
    }
}

// ყველა პოსტის წაკითხვა ბაზიდან გამოჩენისთვის
$posts = $postManager->getAllWithUsers();

include 'includes/header.php';
?>

<section class="gallery-container">
    <h2>ფოტო გალერეა</h2>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="upload-box">
            <h3>ახალი ფოტოს დამატება</h3>
            <form action="gallery.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">ფოტოს სათაური:</label>
                    <input type="text" id="title" name="title" placeholder="შეიყვანეთ სათაური">
                </div>
                <div class="form-group">
                    <label for="photo">აირჩიეთ ფაილი:</label>
                    <input type="file" id="photo" name="photo">
                </div>
                <button type="submit" name="upload_photo" class="btn-submit">ატვირთვა</button>
            </form>
        </div>
    <?php else: ?>
        <p class="info-text">ფოტოების ასატვირთად <a href="login.php">შედით სისტემაში</a>.</p>
    <?php endif; ?>

    <hr>

    <div class="gallery-grid">
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <div class="gallery-item">
                    <img src="uploads/<?php echo $post['image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    <div class="item-info">
                        <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                        <p>ავტორი: <span><?php echo htmlspecialchars($post['email']); ?></span></p>
                        
                        <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $post['user_id'] || $_SESSION['user_role'] === 'Admin')): ?>
                            <a href="gallery.php?delete_id=<?php echo $post['id']; ?>" class="btn-delete" onclick="return confirm('ნამდვილად გსურთ ფოტოს წაშლა?')">წაშლა</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-photos">გალერეა ცარიელია. იყავი პირველი, ვინც ატვირთავს ფოტოს!</p>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>