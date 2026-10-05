<?php
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";exit();
if (isset($_COOKIE["lang"]) and file_exists($_SERVER['DOCUMENT_ROOT'] . "/lang/".$_COOKIE["lang"].".php")) {
include $_SERVER['DOCUMENT_ROOT'] . "/lang/".$_COOKIE["lang"].".php";
}
elseif (!isset($_COOKIE["lang"]) and file_exists($_SERVER['DOCUMENT_ROOT'] . "/lang/".substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5).".php")) {
include $_SERVER['DOCUMENT_ROOT'] . "/lang/".substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5).".php";
}
elseif (!isset($_COOKIE["lang"]) and file_exists($_SERVER['DOCUMENT_ROOT'] . "/lang/".substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2).".php")) {
include $_SERVER['DOCUMENT_ROOT'] . "/lang/".substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2).".php";
}
else {
include $_SERVER['DOCUMENT_ROOT'] . "/lang/en-US.php";
}
//MUST BE LOGGED IN
//REQUIRES $_GET["id"]
if (!$_USER->Logged_In)         { header("location: /"); exit(); }
if (!isset($_REQUEST["id"]))    { header("location: /"); exit(); }

$Group = $DB->execute("SELECT groups.* FROM groups WHERE groups.id = :ID", true, [":ID" => $_REQUEST["id"]]);

if ($DB->Row_Num == 0) { notification($LANGS['groupdoesnotexist'], "/groups"); exit(); }

if ($Group["created_by"] != $_USER->Username && !$_USER->Is_Admin && !$_USER->Is_Moderator) { header("location: /"); exit(); }


if (isset($_POST["change_info"])) {
    if (mb_strlen($_POST["description"]) > 5000) { header("location: /"); exit(); }

    if (empty($_POST["description"])) { notification($LANGS['groupdescempty'], "/group_moderation?id=".$_REQUEST["id"]); exit(); }

    if ($_POST["instant"] == 1)       { $Instant = 1; } else { $Instant = 0; }
    if ($_POST["instant_video"] == 1) { $Instant_Video = 1; } else { $Instant_Video = 0; }


    $DB->modify("UPDATE groups SET instant_join = $Instant, description = :DESCRIPTION, instant_video = $Instant_Video WHERE id = :ID", [":DESCRIPTION" => $_POST["description"], ":ID" => $_REQUEST["id"]]);

    notification($LANGS['descriptionchanged'], "/group_moderation?id=".$_REQUEST["id"], "cfeeb2"); exit();
}

if (isset($_POST["change_image"])) {
    $Uploader = new upload($_FILES["image"]);
    $Uploader->file_new_name_body = $Group["id"];
    $Uploader->image_resize = true;
    $Uploader->file_overwrite          = true;
    $Uploader->image_x                 = 100;
    $Uploader->image_y                 = 100;
    $Uploader->image_background_color  = '#000000';
    $Uploader->image_convert           = 'jpg';
    $Uploader->image_ratio_fill        = false;
    $Uploader->file_max_size           = 1000000;
    $Uploader->jpeg_quality            = 40;
    $Uploader->allowed                 = array('image/jpeg','image/pjpeg','image/png','image/gif','image/bmp','image/x-windows-bmp');
    $Uploader->process("u/grp/");
    if ($Uploader->processed) {
        notification($LANGS['imageupdated'], "/group_moderation?id=".$_REQUEST["id"], "cfeeb2"); exit();
    } else {
        notification($LANGS['groupimageerror'], false); exit();
    }
}

if (isset($_POST["send_message"])) {
    $Inbox = new Inbox($_USER,$DB);

    if (mb_strlen($_POST["message"]) > 500) { header("location: /"); exit(); }

    if (empty($_POST["message"])) { notification($LANGS['emptymessage'], "/group_moderation?id=".$_REQUEST["id"]); exit(); }

    $Members = $DB->execute("SELECT member FROM groups_members WHERE group_id = :ID AND accepted = 1", false, [":ID" => $_REQUEST["id"]]);

    foreach ($Members as $Member) {
        $Inbox->send_message($_POST["message"], $Member["member"]);
    }
    notification($LANGS['messagesent'], "/group_moderation?id=".$_REQUEST["id"], "cfeeb2"); exit();
}

if (isset($_POST["change_css"])) {
    $CSS = $_POST["css"];

    if (mb_strlen($CSS) > 1000) { header("location: /"); exit(); }

    $Replace = ["<?","<?=","<?php","?>","<script>","<style>","</script>","</style>","<",">"];

    foreach ($Replace as $Word) {
        $CSS = str_replace($Replace,"",$CSS);
    }

    file_put_contents($_SERVER["DOCUMENT_ROOT"]."/u/css/".$_REQUEST["id"].".txt",$CSS);
    notification($LANGS['styleupdated'], "/group_moderation?id=".$_REQUEST["id"], "cfeeb2"); exit();
}

$_PAGE = array(
    "Page" => "group_moderation",
    "Page_Type" => "groups",
    "Show_Search" => false,
    "new" => true
);
require "_templates/_structures/main.php";