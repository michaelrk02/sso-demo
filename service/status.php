<?php

$status = NULL;

function status() {
    global $status;
    if (!empty($status)) {
        return '<div>STATUS: '.$status.'</div>';
    }
    return '';
}

?>
