<?php

$sso_app_id = 'sso.client';
$sso_app_url = 'http://localhost/~michael/sso/client';
$sso_server = 'http://localhost/~michael/sso/service';
$sso_secret_key = 'SnbNHaujWORC5ONCSJjkAXzlxOoloTPg2lCITpXbgHNW5zN3IX';

function get_sso_data($token) {
    global $sso_secret_key;

    $token = base64_decode($token);
    $token = explode(':', $token);
    $data = $token[0];
    $signature = $token[1];

    if ($signature === hash_hmac('md5', $data, $sso_secret_key)) {
        $data = base64_decode($data);
        $data = json_decode($data, TRUE);
        if (time() < $data['expiration']) {
            return $data;
        }
    }
    return NULL;
}

function get_sso_login_url($redirect, $param) {
    global $sso_app_id, $sso_app_url, $sso_server;

    return $sso_server.'/login.php?app_id='.urlencode($sso_app_id).'&redirect='.urlencode($sso_app_url.'/'.$redirect).'&param='.urlencode($param);
}

?>
