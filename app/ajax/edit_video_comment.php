<?php
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";

//REQUIREMENTS / PERMISSIONS
//- Requires Login
//- Requires ($_POST["id"])
//- Requires ($_POST["content")
if (!$_USER->logged_in)            { exit(); }
if (!isset($_POST["id"]))       { exit(); }
if (!isset($_POST["content"]))       { exit(); }

$ID    = (int)$_POST["id"];
$Check = $DB->execute("SELECT id, by_user, comment, url, seen, reply_to FROM video_comments WHERE id = $ID", true);

if ($DB->RowNum == 0) { die(); }

$_GUMP->validation_rules(array(
    "content"    => "required|max_len,1005|min_len,1",
    "id"     => "required"
));

$_GUMP->filter_rules(array(
    "content"    => "trim|NoHTML",
    "id"     => "trim|NoHTML"
));
$Validation = $_GUMP->run($_POST);

if ($Check["by_user"] === $_USER->username) {
    
    $Comment = $Validation["content"];
    $DB->modify("UPDATE video_comments SET comment = :comment, edited = 1 WHERE id = $ID",[":comment" => $Comment]);
    
    $Select["comment"]  = showBBcodes(hashtag_search(mention(nl2br($Comment))));
    $Select["markup"] = showBBcodes(hashtag_search(mention(nl2br($Comment))));
    $Select["response"] = "success";
	$Select["by_user"]  = $_USER->displayname;
            if (strpos($Comment, "@") !== false) {
    preg_match_all("/(?<!\S)@([0-9a-zA-Z]+)/", $Comment, $Mentions);
    foreach ($Mentions[1] as $Mention) {

        if (!ctype_alnum($Mention)) {
            continue;
        }

        $Exist = $DB->execute(
            "SELECT username FROM users 
             WHERE displayname = :USER AND can_mention = 1 
             LIMIT 1",
            true,
            [":USER" => $Mention]
        );

        if ($DB->RowNum === 0) {
            continue;
        }

        $Exist = $Exist["username"];

        if (strtolower($_USER->username) === strtolower($Exist)) {
            continue;
        }

        $DB->execute(
            "SELECT 1 FROM users_block 
             WHERE (blocker = :USERNAME AND blocked = :OTHER)
                OR (blocker = :OTHER AND blocked = :USERNAME)",
            false,
            [
                ":USERNAME" => $_USER->username,
                ":OTHER"    => $Exist
            ]
        );

        if ($DB->RowNum > 0) {
            continue;
        }

       
        $DB->execute(
            "SELECT 1 FROM mentions 
             WHERE video = :COMMENT_ID 
               AND username = :USERNAME 
               AND type = 0 
             LIMIT 1",
            false,
            [
                ":COMMENT_ID" => $ID,
                ":USERNAME"   => $Exist
            ]
        );

        if ($DB->RowNum > 0) {
            continue; 
        }

        $DB->modify(
            "INSERT INTO mentions (video, type, date, username)
             VALUES (:COMMENT_ID, 0, NOW(), :USERNAME)",
            [
                ":COMMENT_ID" => $ID,
                ":USERNAME"   => $Exist
            ]
        );
    }
}


  
    exit(json_encode(["success" => true, "data" => $Select]));
}