<?php

session_start();

if (empty($_SESSION['sso.service:user_id'])) {
    header('Location: login.php');
    exit;
}

require './db/open.php';

$stmt = $db->prepare('SELECT * FROM `users` WHERE `user_id` = ?');
$stmt->bind_param('s', $_SESSION['sso.service:user_id']);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
if (!isset($user)) {
    header('Location: logout.php');
    exit;
}

require './db/close.php';

?>
<html>
    <head>
        <title>SSO Status</title>
    </head>
    <body>
        <h1>SSO</h1>
        <h3>Currently logged in as:</h3>
        <div style="margin: 0.25rem"><b>User ID:</b> <?php echo $user['user_id']; ?></div>
        <div style="margin: 0.25rem"><b>First Name:</b> <?php echo $user['first_name']; ?></div>
        <div style="margin: 0.25rem"><b>Last Name:</b> <?php echo $user['last_name']; ?></div>
        <div style="margin: 0.25rem"><b>Organization:</b> <?php echo $user['organization']; ?></div>
        <div style="margin: 0.25rem">Actions: <a href="logout.php">Logout</a></div>
    </body>
</html>
