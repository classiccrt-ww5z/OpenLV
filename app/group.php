<?php
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";
exit();
error_reporting(E_ALL);
//REQUIRES $_GET["id"]
if (!isset($_GET["id"])) { header("location: /"); exit(); }

$Group = $DB->execute("SELECT groups.* FROM groups WHERE groups.id = :ID", true, [":ID" => $_GET["id"]]);

if ($DB->RowNum == 0) { notification($LANGS['groupdoesnotexist'], "/"); exit(); }

$DB->execute("SELECT member FROM groups_members WHERE group_id = :ID", false, [":ID" => $_GET["id"]]);
$Member_Amount = $DB->RowNum;

$DB->execute("SELECT video FROM groups_videos WHERE group_id = :ID", false, [":ID" => $_GET["id"]]);
$Video_Amount = $DB->RowNum;

$Group_Category = array(1 => $LANGS['cat1'], 2 => $LANGS['cat2'], 3 => $LANGS['cat3'], 4 => $LANGS['cat4'], 5 => $LANGS['cat5'], 6 => $LANGS['cat6'], 7 => $LANGS['cat7'], 8 => $LANGS['cat8'], 9 => $LANGS['cat9'], 10 => $LANGS['cat10'], 11 => $LANGS['cat11'], 12 => $LANGS['cat12'], 13 => $LANGS['cat13'], 14 => $LANGS['cat14'], 15 => $LANGS['cat15'], 16 => $LANGS['cat16'], 17 => $LANGS['cat17'], 18 => $LANGS['cat18'], 19 => $LANGS['cat19'], 20 => $LANGS['cat20'], 21 => $LANGS['cat21']);

if (!isset($_GET["topic_id"])) {
    $_PAGINATION = new Pagination(10, 10);
    $_PAGINATION->Total = ($DB->execute("SELECT count(id) as amount FROM groups_topics WHERE group_id = :ID", true, [":ID" => $_GET["id"]])["amount"]);

    $Topics = $DB->execute("SELECT DISTINCT groups_topics.*, max(groups_messages.submit_date) as submitted FROM groups_topics LEFT JOIN groups_messages ON groups_topics.id = groups_messages.topic_id WHERE group_id = :ID GROUP BY groups_messages.topic_id ORDER BY submitted DESC LIMIT $_PAGINATION->From, $_PAGINATION->To", false, [":ID" => $_GET["id"]]);
}

if (isset($_GET["topic_id"])) {
    $MESSAGE_PAGINATION = new Pagination(10,50);
    $Topic          = $DB->execute("SELECT * FROM groups_topics WHERE id = :ID", true, [":ID" => $_GET["topic_id"]]);
    $DB->execute("SELECT * FROM groups_messages WHERE topic_id = :ID", false, [":ID" => $_GET["topic_id"]]);
    $MESSAGE_PAGINATION->Total = ($DB->RowNum);

    $First_Message  = $DB->execute("SELECT id FROM groups_messages WHERE topic_id = :ID ORDER BY submit_date ASC LIMIT 1", true, [":ID" => $_GET["topic_id"]])["id"];
    $Topic_Messages = $DB->execute("SELECT * FROM groups_messages WHERE topic_id = :ID ORDER BY submit_date ASC LIMIT $MESSAGE_PAGINATION->From, $MESSAGE_PAGINATION->To", false, [":ID" => $_GET["topic_id"]]);

    if ($DB->RowNum == 0) {
        header("location: /group?id=".$_GET["id"]); exit();
    }
}


if ($_USER->logged_in) {
    if ($_USER->username === $Group["created_by"]) {
        $Owns_Group = true;
    } else {
        $Owns_Group = false;
    }

    $Is_Member = $DB->execute("SELECT * FROM groups_members WHERE member = :USERNAME AND group_id = :ID", true,
                             [
                                 ":USERNAME" => $_USER->username,
                                 ":ID"       => $Group["id"]
                             ]);

    if ($DB->RowNum > 0) {
        if ($Is_Member["accepted"] == 1) {
            $Is_Member = true;
            $Requested = true;
        } else {
            $Is_Member = false;
            $Requested = true;
        }
    } else {
        $Is_Member = false;
        $Requested = false;
    }
}


if (!isset($_GET["action"]) || ($_GET["action"] != "members" && $_GET["action"] != "videos")) {
    $Members = $DB->execute("SELECT member FROM groups_members WHERE group_id = :ID AND accepted = 1 ORDER BY submit_date DESC LIMIT 20", false, [":ID" => $_GET["id"]]);
    $Videos  = $DB->execute("SELECT videos.title, videos.url, videos.length, videos.views, videos.uploaded_by, videos.uploaded_on, videos.1_star, videos.2_star, videos.3_star, videos.4_star, videos.5_star FROM videos INNER JOIN groups_videos ON groups_videos.video = videos.url WHERE groups_videos.accepted = 1 AND groups_videos.group_id = :ID AND videos.privacy = 1 AND videos.banned_uploader = 0 ORDER BY groups_videos.submit_date DESC LIMIT 20", false, [":ID" => $_GET["id"]]);
} elseif ($_GET["action"] == "members") {
    if ($_USER->logged_in && $Owns_Group) {
        $Members = $DB->execute("SELECT * FROM groups_members WHERE group_id = :ID ORDER BY submit_date DESC", false, [":ID" => $_GET["id"]]);
    } else {
        $Members = $DB->execute("SELECT * FROM groups_members WHERE group_id = :ID AND accepted = 1 ORDER BY submit_date DESC", false, [":ID" => $_GET["id"]]);
    }
} elseif ($_GET["action"] == "videos") {
    if ($_USER->logged_in && $Owns_Group) {
        $Videos  = $DB->execute("SELECT videos.title, videos.url, videos.length, videos.views, videos.uploaded_by, videos.uploaded_on, videos.1_star, videos.2_star, videos.3_star, videos.4_star, videos.5_star, groups_videos.accepted FROM videos INNER JOIN groups_videos ON groups_videos.video = videos.url WHERE groups_videos.group_id = :ID AND videos.privacy = 1 AND videos.banned_uploader = 0 ORDER BY groups_videos.submit_date DESC", false, [":ID" => $_GET["id"]]);
    } else {
        $Videos  = $DB->execute("SELECT videos.title, videos.url, videos.length, videos.views, videos.uploaded_by, videos.uploaded_on, videos.1_star, videos.2_star, videos.3_star, videos.4_star, videos.5_star, groups_videos.accepted FROM videos INNER JOIN groups_videos ON groups_videos.video = videos.url WHERE groups_videos.accepted = 1 AND groups_videos.group_id = :ID AND videos.privacy = 1 AND videos.banned_uploader = 0 ORDER BY groups_videos.submit_date DESC", false, [":ID" => $_GET["id"]]);
    }
} else {
    header("location: /group?id=".$_GET["id"]); exit();
}

$_PAGE->set_variables(array(
    "Page_Title" => "Group - LiiVid",
    "Page" => "group",
    "Page_Type" => "Community",
    "Show_Search" => false
));
require_once "_templates/page_structure.php";
