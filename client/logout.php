<?php

session_start();

if (isset($_SESSION['sso.client:user_id'])) {
    unset($_SESSION['sso.client:user_id']);
}
header('Location: login.php');

?>
