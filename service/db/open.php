<?php

$db = NULL;
$stmt = NULL;

(function($username, $password, $dbname = 'sso_service') {
    global $db;

    $db = new \mysqli('localhost', $username, $password, $dbname);
    if ($db->connect_errno != 0) {
        die('Connection error: '.$db->connect_error);
    }
})('root', 'root');

?>
