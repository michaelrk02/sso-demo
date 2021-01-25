<?php

require_once './status.php';

session_start();

if (!empty($_SESSION['sso.service:user_id'])) {
    header('Location: profile.php');
    exit;
}

$user_id = '';
$first_name = '';
$last_name = '';
$organization = '';

if (!empty($_POST['submit'])) {
    require './db/open.php';

    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $organization = $_POST['organization'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password === $password_confirm) {
        $stmt = $db->prepare('SELECT `user_id` FROM `users` WHERE `user_id` = ?');
        $stmt->bind_param('s', $user_id);
        $stmt->execute();

        $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 5]);

        if ($stmt->get_result()->num_rows == 0) {
            $stmt = $db->prepare('INSERT INTO `users` (`user_id`, `password`, `first_name`, `last_name`, `organization`) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $user_id, $password, $first_name, $last_name, $organization);
            $stmt->execute();

            $status = 'An SSO account ('.$user_id.') has been created. Now you can log in using your credentials';
            $user_id = '';
            $first_name = '';
            $last_name = '';
            $organization = '';
        } else {
            $status = 'User ID is already taken';
        }
    } else {
        $status = 'Passwords do not match';
    }

    require './db/close.php';
}

?>
<html>
    <head>
        <title>SSO Register</title>
    </head>
    <body>
        <?php echo status(); ?>
        <form method="post" action="register.php">
            <h3>Register your SSO account</h3>
            <div style="margin: 0.25rem">
                <div><label>User ID</label></div>
                <div><input type="text" style="width: 256px" name="user_id" placeholder="Enter your user ID here" value="<?php echo htmlspecialchars($user_id); ?>"></div>
            </div>
            <div style="margin: 0.25rem">
                <div><label>First Name</label></div>
                <div><input type="text" style="width: 256px" name="first_name" placeholder="Enter your first name here" value="<?php echo htmlspecialchars($first_name); ?>"></div>
            </div>
            <div style="margin: 0.25rem">
                <div><label>Last Name</label></div>
                <div><input type="text" style="width: 256px" name="last_name" placeholder="Enter your last name here" value="<?php echo htmlspecialchars($last_name); ?>"></div>
            </div style="margin: 0.25rem">
            <div style="margin: 0.25rem">
                <div><label>Organization</label></div>
                <div><input type="text" style="width: 256px" name="organization" placeholder="Enter your organization name here" value="<?php echo htmlspecialchars($organization); ?>"></div>
            </div>
            <div style="margin: 0.25rem">
                <div><label>Password</label></div>
                <div><input type="password" style="width: 256px" name="password" placeholder="Enter your password here"></div>
            </div>
            <div style="margin: 0.25rem">
                <div><label>Confirm Password</label></div>
                <div><input type="password" style="width: 256px" name="password_confirm" placeholder="Enter the same password here"></div>
            </div>
            <div style="margin: 0.25rem">Already have an account? <a href="login.php">Login</a></div>
            <div style="margin: 0.25rem"><button type="submit" name="submit" value="1">Register</button></div>
        </form>
    </body>
</html>
