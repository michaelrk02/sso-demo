<?php

require_once './status.php';

session_start();

$parameters = [];
array_walk($_GET, function($value, $key) use (&$parameters) {
    $parameters[] = urlencode($key).'='.urlencode($value);
});
if (count($parameters) > 0) {
    $parameters = '?'.implode('&', $parameters);
} else {
    $parameters = '';
}

function redirect() {
    global $db, $stmt, $parameters;

    if (!empty($_GET['app_id']) && !empty($_GET['redirect']) && !empty($_GET['param'])) {
        $app_id = $_GET['app_id'];

        $stmt = $db->prepare('SELECT `secret_key` FROM `apps` WHERE `app_id` = ?');
        $stmt->bind_param('s', $app_id);
        $stmt->execute();

        $app = $stmt->get_result()->fetch_assoc();
        if (isset($app)) {
            $secret_key = $app['secret_key'];
            $data = ['user_id' => $_SESSION['sso.service:user_id'], 'expiration' => time() + 5];
            $data = json_encode($data);
            $data = base64_encode($data);
            $signature = hash_hmac('md5', $data, $secret_key);
            $token = implode(':', [$data, $signature]);
            $token = base64_encode($token);

            header('Location: '.$_GET['redirect'].'?'.$_GET['param'].'='.urlencode($token));
            exit;
        } else {
            die('App not found: '.$app_id);
        }
    } else {
        header('Location: profile.php');
        exit;
    }
}

if (!empty($_SESSION['sso.service:user_id'])) {
    require './db/open.php';
    redirect();
    require './db/close.php';
}

if (!empty($_POST['submit'])) {
    require './db/open.php';

    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    $stmt = $db->prepare('SELECT `password` FROM `users` WHERE `user_id` = ?');
    $stmt->bind_param('s', $user_id);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();
    if (isset($user)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['sso.service:user_id'] = $user_id;
            redirect();
        } else {
            $status = 'Invalid user ID or password';
        }
    } else {
        $status = 'Invalid user ID or password';
    }

    require './db/close.php';
}

?>
<html>
    <head>
        <title>SSO Login</title>
    </head>
    <body>
        <?php echo status(); ?>
        <form method="post" action="login.php<?php echo $parameters; ?>">
            <h3>Login using your SSO details</h3>
            <?php if (!empty($_GET['app_id'])): ?>
                <div style="margin: 0.25rem">You're about to authenticate <b><?php echo $_GET['app_id']; ?></b> using this SSO service</div>
            <?php endif; ?>
            <div style="margin: 0.25rem">
                <div><label>User ID</label></div>
                <div><input type="text" style="width: 256px" name="user_id" placeholder="Enter your user ID here"></div>
            </div>
            <div style="margin: 0.25rem">
                <div><label>Password</label></div>
                <div><input type="password" style="width: 256px" name="password" placeholder="Enter your password here"></div>
            </div>
            <div style="margin: 0.25rem">Don't have an account? <a href="register.php">Register</a></div>
            <div style="margin: 0.25rem"><button type="submit" name="submit" value="1">Login</button></div>
        </form>
    </body>
</html>

