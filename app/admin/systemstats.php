<?php
// Live system stats for the admin sidebar.
//
// This used to bootstrap its own database connection with hardcoded credentials and
// gate itself on a key embedded in the page source ("093434093342"), so anyone who
// could read the repository could also call this endpoint. It now shares the normal
// bootstrap and requires the admin panel gate like every other moderation surface.
require_once $_SERVER["DOCUMENT_ROOT"] . "/_includes/init.php";

require_admin_panel();

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax) {
    header("Content-Type: text/html; charset=UTF-8");
    echo getSystemStats();
    exit;
}

echo getSystemStats();