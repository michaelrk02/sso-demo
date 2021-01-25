<?php

require_once './config.php';

session_start();

if (!empty($_SESSION['sso.client:user_id'])) {
    header('Location: profile.php');
    exit;
}

$status = '';

if (!empty($_GET['sso_token'])) {
    $data = get_sso_data($_GET['sso_token']);
    if (isset($data)) {
        $_SESSION['sso.client:user_id'] = $data['user_id'].'@sso';
        header('Location: profile.php');
        exit;
    } else {
        $status = 'Invalid SSO token. Try logging in again';
    }
}

?>
<html>
    <head>
        <title>Client Login</title>
    </head>
    <body>
        <h3>Login To Continue</h3>
        <div style="margin: 0.25rem"><a href="<?php echo get_sso_login_url('login.php', 'sso_token'); ?>">Login using SSO</a></div>
        <div style="margin: 0.25rem"><?php echo $status; ?></div>
    </body>
</html>
