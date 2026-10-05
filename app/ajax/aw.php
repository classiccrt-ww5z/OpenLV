<?php
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";

if (!isset($_POST["u"])) { exit(); }
if (!isset($_POST["a"])) { exit(); }
if(isBotOrNot()) { exit("true"); }

$Source = $_POST["a"];
$video_id = $_POST["u"];

if (mb_strpos($Source, "/watch?v=") !== false || mb_strpos($Source, "https://www.liivid.lol/watch?v=") !== false) {
    $parts = parse_url($Source);
    parse_str($parts['query'], $query);
    if (isset($query['v'])) {
        $video_id = $query['v'];
        if ($video_id != $_POST["u"]) {
            $Source = "!".$video_id;
        }
    }
} elseif (mb_strpos($Source, "/results?q=") !== false) {
    $parts = parse_url($Source);
    parse_str($parts['query'], $query);
    $video_id = $query['q'] ?? '';
    $Source = ")".urldecode($video_id);
} elseif (mb_strpos($Source, "/videos") !== false) {
    $Source = "v";
} elseif ($Source == "/") {
    $Source = "h";
} elseif (mb_strpos($Source, "/community") !== false) {
    $Source = "c";
} elseif (mb_strpos($Source, "/user/") !== false) {
    $user_path = str_replace("/user/", "", $Source);
    $Username = $DB->execute("SELECT username FROM users WHERE displayname = :SOURCE", true, [":SOURCE" => $user_path]);
    $Source = ($DB->RowNum == 0) ? "" : "?".$Username["username"];
} elseif (filter_var($Source, FILTER_VALIDATE_URL)) {
    $Source = $Source;
} else {
    $Source = "";
}

$_VIDEO = new Video($video_id, $DB);

if ($_VIDEO->exists()) {
    $view = $_VIDEO->view($_USER, 1, $Source);
    if($view) {
    echo "true";
    } else {
        echo "false";
    }
    exit();
}

exit("error: video not found");
