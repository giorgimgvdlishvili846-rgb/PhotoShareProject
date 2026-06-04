<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'classes/User.php';
require_once 'classes/Logger.php';

// RBAC დაცვა: თუ მომხმარებელი არ არის ადმინისტატორი, ვაგდებთ მთავარ გვერდზე
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

$userManager = new User();
$error_message = '';
$success_message = '';

// მომხმარებლის წაშლის დამუშავება
if (isset($_GET['delete_user_id'])) {
    $result = $userManager->deleteUser($_GET['delete_user_id']);
    if ($result === true) {
        $success_message = "მომხმარებელი წარმატებით წაიშალა!";
    } else {
        $error_message = $result;
    }
}

// მომხმარებლების სიის წაკითხვა
$users = $userManager->getAllUsers();

// ლოგების ფაილის წაკითხვა
$logs = Logger::readLogs();

include 'includes/header.php';
?>

<section class="admin-container">
    <h2>🛠️ ადმინისტრატორის პანელი</h2>
    <p class="admin-welcome">მოგესალმებით, <strong><?php echo htmlspecialchars($_SESSION['user_email']); ?></strong>. აქედან შეგიძლიათ მართოთ სისტემა.</p>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="admin-box">
        <h3>👥 დარეგისტრირებული მომხმარებლები</h3>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ელ.ფოსტა</th>
                        <th>როლი</th>
                        <th>რეგისტრაციის თარიღი</th>
                        <th>მოქმედება</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><span class="role-tag <?php echo strtolower($u['role_name']); ?>"><?php echo $u['role_name']; ?></span></td>
                            <td><?php echo $u['created_at']; ?></td>
                            <td>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="admin.php?delete_user_id=<?php echo $u['id']; ?>" class="btn-delete" onclick="return confirm('ნამდვილად გსურთ ამ მომხმარებლის წაშლა?')">წაშლა</a>
                                <?php else: ?>
                                    <span class="disabled-text">თქვენ ხართ</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-box log-box">
        <h3>📜 სისტემური ლოგები (system.log)</h3>
        <p class="info-text-small">ყველა კრიტიკული აქტივობა იწერება ფაილში უსაფრთხოების მიზნით.</p>
        <textarea class="log-textarea" readonly><?php echo htmlspecialchars($logs); ?></textarea>
    </div>
</section>

<?php include 'includes/footer.php'; ?>