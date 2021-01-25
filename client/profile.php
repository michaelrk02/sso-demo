<?php

session_start();

if (empty($_SESSION['sso.client:user_id'])) {
    header('Location: login.php');
    exit;
}

?>
<html>
    <head>
        <title>Client Profile</title>
    </head>
    <body>
        <h3>Profile</h3>
        <div style="margin: 0.25rem">Logged in as <b><?php echo $_SESSION['sso.client:user_id']; ?></b></div>
        <div style="margin: 0.25rem">Actions: <a href="logout.php">Logout</a></div>
    </body>
</html>
