<?php
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";exit();
error_reporting(E_ALL);
$_PAGINATION = new Pagination(10,15);
$Group_Category = array(1 => $LANGS['cat1'], 2 => $LANGS['cat2'], 3 => $LANGS['cat3'], 4 => $LANGS['cat4'], 5 => $LANGS['cat5'], 6 => $LANGS['cat6'], 7 => $LANGS['cat7'], 8 => $LANGS['cat8'], 9 => $LANGS['cat9'], 10 => $LANGS['cat10'], 11 => $LANGS['cat11'], 12 => $LANGS['cat12'], 13 => $LANGS['cat13'], 14 => $LANGS['cat14'], 15 => $LANGS['cat15'], 16 => $LANGS['cat16'], 17 => $LANGS['cat17'], 18 => $LANGS['cat18'], 19 => $LANGS['cat19'], 20 => $LANGS['cat20'], 21 => $LANGS['cat21']);
if (!isset($_GET["b"])) {
    if (!isset($_GET["c"]) || !isset($Group_Category[$_GET["c"]])) {
        $Groups = $DB->execute("SELECT groups.* FROM groups INNER JOIN users ON groups.created_by = users.username WHERE users.banned = 0 ORDER BY groups.creation_date DESC LIMIT $_PAGINATION->From, $_PAGINATION->To");
        $DB->execute("SELECT id FROM groups");
        $_PAGINATION->Total = 150;
    } else {
        $Groups = $DB->execute("SELECT groups.* FROM groups INNER JOIN users ON groups.created_by = users.username WHERE users.banned = 0 AND groups.categories = ".$_GET["c"]." ORDER BY groups.creation_date DESC LIMIT $_PAGINATION->From, $_PAGINATION->To");
        $DB->execute("SELECT id FROM groups INNER JOIN users ON groups.created_by = users.username WHERE users.banned = 0 AND categories = ".$_GET["c"]);
        $_PAGINATION->Total = 150;
    }
} else {
    if ($_GET["b"] == "members") {
        $Groups = $DB->execute("SELECT groups.*, count(groups_members.member) as amount FROM groups INNER JOIN users ON groups.created_by = users.username LEFT JOIN groups_members ON groups.id = groups_members.group_id WHERE users.banned = 0 GROUP BY groups_members.group_id, groups.id ORDER BY amount DESC, groups.creation_date DESC LIMIT $_PAGINATION->From, $_PAGINATION->To");
            $_PAGINATION->Total = 150;
    } elseif ($_GET["b"] == "videos") {
        $Groups = $DB->execute("SELECT groups.*, count(groups_videos.video) as amount FROM groups INNER JOIN users ON groups.created_by = users.username LEFT JOIN groups_videos ON groups.id = groups_videos.group_id WHERE users.banned = 0 GROUP BY groups_videos.group_id, groups.id ORDER BY amount DESC, groups.creation_date DESC LIMIT $_PAGINATION->From, $_PAGINATION->To");
            $_PAGINATION->Total = 150;
    } elseif ($_GET["b"] == "topics") {
        $Groups = $DB->execute("SELECT groups.*, count(groups_topics.id) as amount FROM groups INNER JOIN users ON groups.created_by = users.username LEFT JOIN groups_topics ON groups.id = groups_topics.group_id WHERE users.banned = 0 GROUP BY groups_topics.group_id, groups.id ORDER BY amount DESC, groups.creation_date DESC LIMIT $_PAGINATION->From, $_PAGINATION->To");
        $_PAGINATION->Total = 150;
    } else {
        header("location: /"); exit();
    }
    $DB->execute("SELECT id FROM groups");
}



$_PAGE->set_variables(array(
    "Page_Title" => "Channel Setup - LiiVid",
    "Page" => "groups",
    "Page_Type" => "Community",
    "Show_Search" => false
));
require_once "_templates/page_structure.php";
