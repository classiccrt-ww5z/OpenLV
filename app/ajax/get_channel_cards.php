<?php
header("Content-Type: application/json", true);
require_once $_SERVER["DOCUMENT_ROOT"] . "/_includes/init.php";
if(!isset($_GET["user"])) {
    exit(json_encode([
        "success" => false,
        "errors" => [[
            "name" => "missing_param",
            "message" => 'Missing parameter "user"',
            "data" => ["param" => "user", "location" => "query"]
        ]]
    ]));
}

// Privacy / shadowban filters may only be lifted for the channel owner or staff.
$Can_View_Restricted = can_view_restricted_videos($_GET["user"]);
$Profile = $DB->execute(
    "SELECT username, displayname, c_playlists FROM users WHERE username = :USERNAME LIMIT 1",
    true,
    [":USERNAME" => $_GET["user"]]
);
if(!$Profile) {
    exit(json_encode([
        "success" => false,
        "errors" => [[
            "name" => "not_found",
            "message" => "User not found",
            "data" => null
        ]]
    ]));
}
$uploadsLimit = isset($_GET["uploadsLimit"]) ? (int)$_GET["uploadsLimit"] : 16;
$favoritesLimit = isset($_GET["favoritesLimit"]) ? (int)$_GET["favoritesLimit"] : 16;
$playlistsLimit = isset($_GET["playlistsLimit"]) ? (int)$_GET["playlistsLimit"] : 0;
$uploadsBefore = !empty($_GET["uploadsBefore"]) ? $_GET["uploadsBefore"] : null;
if ($uploadsBefore) {
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $uploadsBefore);
    if (!$dt || $dt->format('Y-m-d H:i:s') !== $uploadsBefore) {
        $uploadsBefore = null;
    }
}
$response = ["success" => true, "data" => []];
if(!empty($_GET["includeUploads"]) && $_GET["includeUploads"] == "true") {
    $Videos = new Videos($DB, $_USER);
    $Videos->WHERE_P = ["uploaded_by" => $Profile["username"]];
    $Videos->Shadowbanned_Users = $Can_View_Restricted;
    $Videos->WHERE_C = $Videos->WHERE_C ?? "";
    $Videos->LIMIT = $uploadsLimit;
    if(!empty($_GET["uploadsQuery"]) && strlen($_GET["uploadsQuery"]) >= 2) {
        $Filtered_Search_Query = str_replace(['\\','%','_'], ['\\\\','\\%','\\_'], $_GET["uploadsQuery"]);
        $Normal_Search_Query = trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $Filtered_Search_Query));
        $Videos->SELECT .= ", MATCH(videos.title) AGAINST (:SEARCH IN BOOLEAN MODE) as title_rel, MATCH(videos.title, videos.description, videos.tags) AGAINST (:SEARCH IN NATURAL LANGUAGE MODE) as all_rel";
        $Videos->WHERE_C .= " AND (MATCH(videos.title, videos.description, videos.tags) AGAINST (:SEARCH IN BOOLEAN MODE) OR videos.uploaded_by = :SEARCH OR users.displayname = :SEARCH)";
        $Videos->Execute = $Videos->Execute ?? [];
        $Videos->Execute[":SEARCH"] = $Normal_Search_Query;
        $searchMode = true;
    }
    $allowedColumns = ["createdOn" => "uploaded_on", "views" => "displayviews", "comments" => "comments"];
    $allowedDirections = ["ASC", "DESC"];
    $requestedOrder = $_GET["uploadsOrder"] ?? "createdOn DESC";
    $orderParts = explode(" ", $requestedOrder, 2);
    $col = $orderParts[0] ?? "createdOn";
    $dir = strtoupper($orderParts[1] ?? "DESC");
    $column = $allowedColumns[$col] ?? "uploaded_on";
    $direction = in_array($dir, $allowedDirections) ? $dir : "DESC";
    $Videos->ORDER_BY = "$column $direction, videos.uploaded_on $direction";
    if ($column === "uploaded_on") {
        if ($direction === "DESC" && !empty($_GET["uploadsBefore"])) {
            $Videos->WHERE_C .= " AND videos.uploaded_on < :CURSOR_TS";
            $Videos->Execute[":CURSOR_TS"] = $_GET["uploadsBefore"];
        } elseif ($direction === "ASC" && !empty($_GET["uploadsAfter"])) {
            $Videos->WHERE_C .= " AND videos.uploaded_on > :CURSOR_TS";
            $Videos->Execute[":CURSOR_TS"] = $_GET["uploadsAfter"];
        }
    } elseif ($column === "displayviews") {
        if ($direction === "DESC" && isset($_GET["uploadsViewsFewerThan"])) {
            $Videos->WHERE_C .= " AND (videos.displayviews < :CURSOR_VAL OR (videos.displayviews = :CURSOR_VAL AND videos.uploaded_on < :CURSOR_TS))";
            $Videos->Execute[":CURSOR_VAL"] = (int)$_GET["uploadsViewsFewerThan"];
            $Videos->Execute[":CURSOR_TS"] = $_GET["cursorTs"] ?? date('Y-m-d H:i:s');
        } elseif ($direction === "ASC" && isset($_GET["uploadsViewsMoreThan"])) {
            $Videos->WHERE_C .= " AND (videos.displayviews > :CURSOR_VAL OR (videos.displayviews = :CURSOR_VAL AND videos.uploaded_on > :CURSOR_TS))";
            $Videos->Execute[":CURSOR_VAL"] = (int)$_GET["uploadsViewsMoreThan"];
            $Videos->Execute[":CURSOR_TS"] = $_GET["cursorTs"] ?? '1970-01-01 00:00:00';
        }
    } elseif ($column === "comments") {
        if ($direction === "DESC" && isset($_GET["uploadsCommentsFewerThan"])) {
            $Videos->WHERE_C .= " AND (videos.comments < :CURSOR_VAL OR (videos.comments = :CURSOR_VAL AND videos.uploaded_on < :CURSOR_TS))";
            $Videos->Execute[":CURSOR_VAL"] = (int)$_GET["uploadsCommentsFewerThan"];
            $Videos->Execute[":CURSOR_TS"] = $_GET["cursorTs"] ?? date('Y-m-d H:i:s');
        } elseif ($direction === "ASC" && isset($_GET["uploadsCommentsMoreThan"])) {
            $Videos->WHERE_C .= " AND (videos.comments > :CURSOR_VAL OR (videos.comments = :CURSOR_VAL AND videos.uploaded_on > :CURSOR_TS))";
            $Videos->Execute[":CURSOR_VAL"] = (int)$_GET["uploadsCommentsMoreThan"];
            $Videos->Execute[":CURSOR_TS"] = $_GET["cursorTs"] ?? '1970-01-01 00:00:00';
        }
    }
    $Videos->get();
    $uploads = $Videos->fixed();
    if (!empty($searchMode)) {
        $Videos_Count = new Videos($DB, $_USER);
        $Videos_Count->WHERE_P = ["uploaded_by" => $Profile["username"]];
        $Videos_Count->Shadowbanned_Users = $Can_View_Restricted;
        $Videos_Count->Count = true;
        $Videos_Count->WHERE_C = $Videos->WHERE_C;
        $Videos_Count->Execute = $Videos->Execute;
        $videosCOunt = $Videos_Count->get();
    } else {
        $Videos_Amount = new Videos($DB, $_USER);
        $Videos_Amount->WHERE_P = ["uploaded_by" => $Profile["username"]];
        $Videos_Amount->Shadowbanned_Users = $Can_View_Restricted;
        $Videos_Amount->Count = true;
        $videosCOunt = $Videos_Amount->get();
    }
    foreach ($uploads as &$v) {
        if(isset($v["thumbnail2"])) $v["thumbnail"] = $v["thumbnail2"];
        $v["durationFormatted"] = isset($v["length"]) && $v["length"] !== "" ? seconds_to_time($v["length"]) : "0:00";
        $v["createdAgo"] = time_ago($v["uploaded_on"]);
        $v["createdOn"] = ($v["uploaded_on"]);
        $v["uploaderDisplayName"] = $v["displayname"];
        $v["id"] = $v["url"];
        foreach ($v as $key => $val) {
            if (!in_array($key, ['id','title','thumbnail','views','comments','description','durationFormatted','createdAgo','createdOn','uploaderDisplayName'])) unset($v[$key]);
        }
    }
    $response["data"]["uploads"] = ["items"=>$uploads,"total"=>$videosCOunt];
}
if(!empty($_GET["includeFavorites"]) && $_GET["includeFavorites"] == "true") {
    $Favorites = new Videos($DB, $_USER);
    $Favorites->JOIN = "RIGHT JOIN video_favorites ON video_favorites.url = videos.url";
    $Favorites->Shadowbanned_Users = $Can_View_Restricted;
    $Favorites->Banned_Users = $Can_View_Restricted;
    $Favorites->Private_Videos = $Can_View_Restricted;
    $Favorites->Unlisted_Videos = $Can_View_Restricted;
    $Favorites->WHERE_P = ["video_favorites.favorite_by"=>$Profile["username"]];
    $Favorites->ORDER_BY = "video_favorites.date DESC";
    if($favoritesLimit > 0) $Favorites->LIMIT = $favoritesLimit;
    $Favorites->get();
    $favorites = $Favorites->fixed();
    $Favorites_Amount = new Videos($DB, $_USER);
    $Favorites_Amount->JOIN = "RIGHT JOIN video_favorites ON video_favorites.url = videos.url";
    $Favorites_Amount->Shadowbanned_Users = $Can_View_Restricted;
    $Favorites_Amount->Banned_Users = $Can_View_Restricted;
    $Favorites_Amount->Private_Videos = $Can_View_Restricted;
    $Favorites_Amount->Unlisted_Videos = $Can_View_Restricted;
    $Favorites_Amount->WHERE_P = ["video_favorites.favorite_by" => $Profile["username"]];
    $Favorites_Amount->Count = true;
    $Favorites_Amount->Count_Column = "video_favorites.url";
    $Favorites_Amount = $Favorites_Amount->get();
    foreach ($favorites as &$v) {
        if(isset($v["thumbnail2"])) $v["thumbnail"] = $v["thumbnail2"];
        $v["durationFormatted"] = isset($v["length"]) && $v["length"] !== "" ? seconds_to_time($v["length"]) : "0:00";
        $v["createdAgo"] = time_ago($v["uploaded_on"]);
        $v["favoritedOn"] = $v["favorited_on"] ?? null;
        $v["createdOn"] = ($v["uploaded_on"]);
        $v["favoritedAgo"] = isset($v["favorited_on"]) ? time_ago($v["favorited_on"]) : null;
        $v["uploaderDisplayName"] = $v["displayname"];
        $v["id"] = $v["url"];
        foreach ($v as $key => $val) {
            if (!in_array($key, ['id','title','thumbnail','views','comments','description','durationFormatted','createdAgo','createdOn','uploaderDisplayName'])) unset($v[$key]);
        }
    }
    $response["data"]["favorites"] = ["items"=>$favorites,"total"=>$Favorites_Amount];
}
if(!empty($_GET["includePlaylists"]) && $_GET["includePlaylists"] == "true" && $Profile["c_playlists"] == 1) {
    $Playlists = $DB->execute(
        "SELECT p.*, :DISPLAYNAME AS creatorDisplayName FROM playlists p WHERE created_by=:OWNER ORDER BY p.created_on DESC" . ($playlistsLimit>0?" LIMIT :LIMIT":""),
        false,
        [":OWNER"=>$Profile["username"],":DISPLAYNAME"=>$Profile["displayname"],":LIMIT"=>$playlistsLimit>0?$playlistsLimit:null]
    );
    foreach ($Playlists as &$pl) {
        $pl["id"] = $pl["purl"] ?? null;
        $Main_Playlist_Videos = new Videos($DB, $_USER);
        $Main_Playlist_Videos->WHERE_P = ["playlists_videos.purl" =>  $pl["id"]];
        $Main_Playlist_Videos->JOIN = "RIGHT JOIN playlists_videos ON playlists_videos.url = videos.url";
        $Main_Playlist_Videos->ORDER_BY = "playlists_videos.position DESC";
        $Main_Playlist_Videos->Shadowbanned_Users = $Can_View_Restricted;
        $Main_Playlist_Videos->LIMIT = 512;
        $Main_Playlist_Videos->get();
        if ($Main_Playlist_Videos::$Videos) $Main_Playlist_Videos = $Main_Playlist_Videos->fixed();
        if(is_array($Main_Playlist_Videos) && !empty($Main_Playlist_Videos)) {
            $firstVideoUrl = $Main_Playlist_Videos[0];
            $pl["thumbnail2"] = video_thumbnailNoHtml($firstVideoUrl["url"]);
        } else $pl["thumbnail2"] = "/img/no_th.jpg";
        $pl["thumbnail"] = $pl["thumbnail2"];
        $pl["createdAgo"] = time_ago($pl["created_on"]); 
    }
    $response["data"]["playlists"] = ["items"=>$Playlists,"total"=>count($Playlists)];
} else $response["data"]["playlists"] = ["items"=>[],"total"=>0];
echo json_encode($response);
