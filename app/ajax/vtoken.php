<?php
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";

$origin = $_SERVER['HTTP_REFERER'] ?? '';
$host = parse_url($origin, PHP_URL_HOST);
if ($host !== "liivid.lol" && !str_ends_with($host, ".liivid.lol")) die();

$_POST['u'] = urldecode(trim($_POST['u']));
$vid_id = null;
if (preg_match('/[?&]v=([A-Za-z0-9_-]{11})/', $_POST['u'], $m)) $vid_id = $m[1];

$_VIDEO = new Video($vid_id, $DB);
$_VIDEO->get_info();
$Length = $_VIDEO->Info['length'];

if ($_USER->username === $_VIDEO->Info['uploaded_by']) die("2");

if (!isset($_SESSION['watch_start'])) $_SESSION['watch_start'] = [];
if (!isset($_SESSION['watch_current'])) $_SESSION['watch_current'] = null;

$ip = $_SERVER['REMOTE_ADDR'];

$rows = $DB->execute(
    "SELECT vid, watchtime, ip_list FROM videos_watched WHERE vid = :VID",
    false,
    [":VID" => $vid_id]
);

$total_watchtime = 0;
$ip_watch = [];

foreach ($rows as $row) {
    $total_watchtime += (int)$row['watchtime'];
    $list = $row['ip_list'] ?? '';
    if ($list !== '') {
        $parts = explode('|', $list);
        foreach ($parts as $p) {
            if (strpos($p, ',') === false) continue;
            list($stored_ip, $stored_time) = explode(',', $p);
            $ip_watch[$stored_ip] = max($ip_watch[$stored_ip] ?? 0, (int)$stored_time);
        }
    }
}

$current_ip_watch = $ip_watch[$ip] ?? 0;
if ($current_ip_watch >= $Length) die("1");

if ($_SESSION['watch_current'] !== $vid_id) {
    $_SESSION['watch_current'] = $vid_id;
    $_SESSION['watch_start'] = [$vid_id => time()];
    die("1");
}

$start_time = $_SESSION['watch_start'][$vid_id] ?? time();
$elapsed = time() - $start_time;
$increment = min($elapsed, $Length);

$remaining = $Length - $current_ip_watch;
$increment = min($increment, $remaining);
if ($increment <= 0) die("1");

$DB->modify("UPDATE videos SET watched = watched + :inc WHERE url = :vid_id", [
    ":inc" => $increment,
    ":vid_id" => $vid_id
]);

$DB->modify("DELETE FROM videos_watched WHERE vid = :VID", [":VID" => $vid_id]);

$ip_watch[$ip] = $current_ip_watch + $increment;
$new_ip_list = [];
foreach ($ip_watch as $stored_ip => $t) $new_ip_list[] = $stored_ip . "," . $t;
$new_ip_list_string = implode('|', $new_ip_list);

$DB->modify(
    "INSERT INTO videos_watched (vid, watchtime, submit_date, ip_list)
     VALUES (:vid_id, :inc, NOW(), :ip_list)",
    [
        ":vid_id" => $vid_id,
        ":inc" => $total_watchtime + $increment,
        ":ip_list" => $new_ip_list_string
    ]
);

$_SESSION['watch_start'][$vid_id] = time();

$_VIDEO->view($_USER, 0, $_SERVER['HTTP_REFERER'] ?? '');
$_USER->Viewed_Videos[] = $_VIDEO->URL;

die("1");
