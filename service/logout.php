<?php

session_start();

if (!empty($_SESSION['sso.service:user_id'])) {
    unset($_SESSION['sso.service:user_id']);
}
header('Location: login.php');

?>
