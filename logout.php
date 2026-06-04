<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'classes/Logger.php';

// თუ მომხმარებელი შესული იყო, ჩავწეროთ ლოგებში მისი გამოსვლა
if (isset($_SU_SESSION['user_email'])) {
    Logger::log("გამოვიდა სისტემიდან", $_SU_SESSION['user_email']);
}

// სესიის მონაცემების გასუფთავება და წაშლა
$_SU_SESSION = array();
session_destroy();

// გადამისამართება მთავარ გვერდზე
header("Location: index.php");
exit();