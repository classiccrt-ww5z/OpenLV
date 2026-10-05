<?php
require_once "_includes/init.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["q"]) && strlen(trim($_POST["q"])) > 0) {
    header('Content-Type: application/json');
    $w = trim($_POST["q"]);
    $fq = str_replace(['\\','%','_'], ['\\\\','\\%','\\_'], $w);
    $R = [];

    $f = $_POST["f"] ?? "all";
    $f = strtolower($f);

    if ($f === "all" || $f == 1) {
        $V = $DB->execute(
            "SELECT title FROM videos WHERE title LIKE :T ESCAPE '\\\\' AND shadowbanned_uploader = 0 AND banned_uploader = 0 AND status = 2 LIMIT 10",
            false,
            [":T" => "%$fq%"]
        );
        if ($V) {
            foreach ($V as $v) $R[] = ['type'=>'v','value'=>$v['title']];
        }
    }

    if ($f === "all" || $f == 2) {
        $U = $DB->execute(
            "SELECT displayname FROM users WHERE displayname LIKE :U ESCAPE '\\\\' AND shadowbanned=0 AND banned=0 LIMIT 10",
            false,
            [":U" => "%$fq%"]
        );
        if ($U) {
            foreach ($U as $u) $R[] = ['type'=>'u','value'=>$u['displayname']];
        }
    }

    $R = array_slice($R, 0, 10);

    if (!empty($R)) {
        echo json_encode([
            "success" => true,
            "data" => $R
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => "No results found"
        ]);
    }
}
 else {
$Search_Start = microtime(true);
if(isset($_GET["f"]) && $_GET["f"] > 2) {
    $_GET["f"] = null;
}
if (isset($_GET["q"]) && strlen($_GET["q"]) >= 2) {
    $_PAGINATION = new Pagination(15, 10);
    $Filtered_Search_Query = str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $_GET["q"]);
    $Normal_Search_Query = trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $Filtered_Search_Query));

    if (isset($_GET["f"])) {

        if ($_GET["f"] == 1) {

            $Page_Type = "Videos";
$allowed_sorts = ["upload-date","view-count","comment-count"];
$Sort = $_GET["sort"] ?? "relevance";
if (!in_array($Sort, $allowed_sorts)) $Sort = "relevance";
$SortType = strtolower($_GET["sort-type"] ?? "asc");
if (!in_array($SortType, ["asc","desc"])) $SortType = "asc";
            if ($Sort == "upload-date") $Order = "videos.uploaded_on $SortType";
            elseif ($Sort == "view-count") $Order = "videos.displayviews $SortType";
            elseif ($Sort == "comment-count") $Order = "videos.comments $SortType";
            else $Order = "(0 + (title_rel * 1) + (videos.views / 150) + (`videos`.`5_star` / 50) + (videos.watched / 60 / 60 / 2) - (`videos`.`1_star`)) + (CASE WHEN YEARWEEK(videos.uploaded_on) = YEARWEEK(NOW()) THEN 1 else 0 end) DESC";

            $Videos = new Videos($DB, $_USER);
            $Videos->SELECT .= ", MATCH(videos.title) AGAINST (:SEARCH IN BOOLEAN MODE) as title_rel,
                                        MATCH(videos.title, videos.description, videos.tags) AGAINST (:SEARCH IN NATURAL LANGUAGE MODE) as all_rel";
            $Videos->WHERE_C = " AND (MATCH (videos.title, videos.description, videos.tags) AGAINST (:SEARCH IN BOOLEAN MODE) OR videos.uploaded_by = :SEARCH OR users.displayname = :SEARCH)";
            $Videos->Blocked = false;
            $Videos->ORDER_BY = $Order;
            $Videos->LIMIT = $_PAGINATION;
            $Videos->Execute = [":SEARCH" => $Normal_Search_Query];
            $Videos->get();

            if ($Videos::$Videos) {

                $Videos = $Videos->fixed();

                $Total = new Videos($DB, $_USER);
                $Total->Count = true;
                $Total->Blocked = false;
                $Total->WHERE_C = " AND (MATCH (videos.title, videos.description, videos.tags) AGAINST (:SEARCH IN BOOLEAN MODE) OR videos.uploaded_by = :SEARCH OR users.displayname = :SEARCH)";
                $Total->Execute = [":SEARCH" => $Normal_Search_Query];
                $Total = $Total->get();

                $_PAGINATION->Total = $Total;

            } else {

                redirect("/results?q=" . urlencode($Normal_Search_Query) . "&f=2");
                exit();

            }

        } elseif ($_GET["f"] == 2) {

            $Page_Type = "Channels";
            $from = (int)$_PAGINATION->From;
            $to = (int)$_PAGINATION->To;

            $Channels = $DB->execute(
                "SELECT username, displayname, subscribers, video_views, avatar, channel_views, channel_description
                 FROM users
                 WHERE displayname LIKE :USERNAME ESCAPE '\\\\'
                   AND shadowbanned = 0
                 LIMIT $from, $to",
                false,
                [
                    ":USERNAME" => "%$Filtered_Search_Query%"
                ]
            );

            $Total = $DB->execute(
                "SELECT COUNT(username) AS amount
                 FROM users
                 WHERE shadowbanned = 0
                   AND displayname LIKE :USERNAME ESCAPE '\\\\'",
                true,
                [
                    ":USERNAME" => "%$Filtered_Search_Query%"
                ]
            )["amount"];

            if ($Total == 0) {
                notification("No Results!", "/");
                exit();
            }

            $_PAGINATION->Total = $Total;

        } else {
            redirect("/");
        }

    } else {

        $Page_Type = "Videos";

        $allowed_sorts = ["upload-date","view-count","comment-count"];
$Sort = $_GET["sort"] ?? "relevance";
if (!in_array($Sort, $allowed_sorts)) $Sort = "relevance";
$SortType = strtolower($_GET["sort-type"] ?? "asc");
if (!in_array($SortType, ["asc","desc"])) $SortType = "asc";
        if ($Sort == "upload-date") $Order = "videos.uploaded_on $SortType";
        elseif ($Sort == "view-count") $Order = "videos.displayviews $SortType";
        elseif ($Sort == "comment-count") $Order = "videos.comments $SortType";
        else $Order = "(0 + (title_rel * 1) + (videos.views / 150) + (`videos`.`5_star` / 50) + (videos.watched / 60 / 60 / 2) - (`videos`.`1_star`)) + (CASE WHEN YEARWEEK(videos.uploaded_on) = YEARWEEK(NOW()) THEN 1 else 0 end) DESC";

        $Videos = new Videos($DB, $_USER);
        $Videos->SELECT .= ", MATCH(videos.title) AGAINST (:SEARCH IN BOOLEAN MODE) as title_rel,
                                     MATCH(videos.title, videos.description, videos.tags) AGAINST (:SEARCH IN NATURAL LANGUAGE MODE) as all_rel";
        $Videos->WHERE_C = " AND (MATCH (videos.title, videos.description, videos.tags) AGAINST (:SEARCH IN BOOLEAN MODE) OR videos.uploaded_by = :SEARCH OR users.displayname = :SEARCH)";
        $Videos->Blocked = false;
        $Videos->ORDER_BY = $Order;
        $Videos->LIMIT = $_PAGINATION;
        $Videos->Execute = [":SEARCH" => $Normal_Search_Query];
        $Videos->get();

        if ($Videos::$Videos) {

            $Videos = $Videos->fixed();

            $Total = new Videos($DB, $_USER);
            $Total->Count = true;
            $Total->Blocked = false;
            $Total->WHERE_C = " AND (MATCH (videos.title, videos.description, videos.tags) AGAINST (:SEARCH IN BOOLEAN MODE) OR videos.uploaded_by = :SEARCH OR users.displayname = :SEARCH)";
            $Total->Execute = [":SEARCH" => $Normal_Search_Query];
            $Total = $Total->get();

            $_PAGINATION->Total = $Total;

        } else {

            redirect("/results?q=" . urlencode($Normal_Search_Query) . "&f=2");
            exit();

        }

        if (!$_USER->logged_in) {

            $User = $DB->execute("SELECT username, displayname, subscribers, video_views, avatar, channel_views, channel_description FROM users WHERE displayname = :USERNAME AND shadowbanned = 0", true, [":USERNAME" => $Normal_Search_Query]);

        } else {

            $User = $DB->execute("SELECT username, displayname, subscribers, video_views, avatar, channel_views, channel_description FROM users LEFT JOIN users_block ON (('$_USER->username' = users_block.blocker AND users.username = users_block.blocked) OR ('$_USER->username' = users_block.blocked AND users.username = users_block.blocker)) WHERE displayname = :USERNAME AND users_block.blocker IS NULL AND shadowbanned = 0", true, [":USERNAME" => $Normal_Search_Query]);

        }

        if ($DB->RowNum == 0) unset($User);

    }
$Search_Time = (int)( (microtime(true) - $Search_Start) * 1000 );

} else {
    notification("You must enter a valid search term!", "/");
    exit();
}





$_PAGE->set_variables(array(
    "Page_Title" => "$Normal_Search_Query - LiiVid",
    "Page" => "Search",
    "Page_Type" => $Page_Type
));
require_once "_templates/page_structure.php";
}