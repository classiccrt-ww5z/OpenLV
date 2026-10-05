<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/_includes/init.php";
   $Register = $DB->execute("SELECT value FROM settings WHERE name = 'videos'", true)["value"];
if(!$Register) {
    notification("Watch page is currently disabled!", "/", "red");
}

$_VIDEO = new Video($_GET["v"],$DB);

if ($_VIDEO->exists() !== false) {
    $_VIDEO->get_info();


    if ($_VIDEO->Info["privacy"] == 2 && (!$_USER->logged_in || $_USER->username != $_VIDEO->Info["uploaded_by"])) {

        notification("You cannot watch this private video!", "/videos", "red"); exit();

    } elseif ($_VIDEO->Info["privacy"] == 1) {

        notification("This is an unlisted video!", false, "red");

    }


    $_VIDEO->Info["views"] = $_VIDEO->Info["displayviews"];
	

	
	
    $URL            = $_VIDEO->Info["url"];

  	
    $URL            = $_VIDEO->Info["url"];
	$FILENAME       = $_VIDEO->Info["file"];
	$ISHD           = $_VIDEO->Info["hd"] == 1 ? true : false;
    $Title          = htmlspecialchars($_VIDEO->Info["title"]);
    $Description    = htmlspecialchars($_VIDEO->Info["description"]);
    $Tags           = explode(",",htmlspecialchars($_VIDEO->Info["tags"]));
    $Category       = (int)$_VIDEO->Info["category"];
    $Views          = number_format($_VIDEO->Info["views"]);
    $Comments_Num   = number_format($_VIDEO->Info["comments"]);
    $Uploaded_By    = $_VIDEO->Info["uploaded_by"];
    $Uploaded_On    = get_date($_VIDEO->Info["uploaded_on"]).", ".get_time($_VIDEO->Info["uploaded_on"]);
    $Total_Ratings  = $_VIDEO->Info["1_star"] + $_VIDEO->Info["2_star"] + $_VIDEO->Info["3_star"] + $_VIDEO->Info["4_star"] + $_VIDEO->Info["5_star"];
    $Length         = $_VIDEO->Info["length"];
    $Status         = $_VIDEO->Info["status"];
    $Show_Ads       = $_VIDEO->Info["show_ads"];
    $Aspect = $_VIDEO->Info["aspect_ratio"];
    // Whether HD is enabled by query params (for use with noscript player)
    $HD_Enabled = isset($_GET["hd"]) and $_GET["hd"] == "1";
 
 $Featured_Videos             = new Videos($DB, $_USER);
        $Featured_Videos->WHERE_C    = "AND videos.featured = 1";
        $Featured_Videos->ORDER_BY   = "videos.uploaded_on DESC";
        $Featured_Videos->LIMIT      = 8;
        $Featured_Videos->Blocked    = false;
        $Featured_Videos->get();
        $Featured_Videos             = $Featured_Videos->fixed();
$randvid = $Featured_Videos[0];
$search = trim($Title . ' ' . $Description . ' ' . $Tags);

$stmt = $conn->prepare("
SELECT videos.*, 
       (
           (MATCH(videos.title) AGAINST (:SEARCH_TITLE IN NATURAL LANGUAGE MODE) * 3) +
           (MATCH(videos.tags) AGAINST (:SEARCH_TAGS IN NATURAL LANGUAGE MODE) * 2) +
           (MATCH(videos.description) AGAINST (:SEARCH_DESC IN NATURAL LANGUAGE MODE))
       ) AS rel_score,
       users.displayname,
       users.partner,
       users.avatar
FROM videos
LEFT JOIN users ON videos.uploaded_by = users.username
WHERE videos.url <> :URL
  AND videos.s_related = 1
  AND (
        MATCH(videos.title) AGAINST (:WHERE_TITLE IN NATURAL LANGUAGE MODE) OR
        MATCH(videos.tags) AGAINST (:WHERE_TAGS IN NATURAL LANGUAGE MODE) OR
        MATCH(videos.description) AGAINST (:WHERE_DESC IN NATURAL LANGUAGE MODE)
      )
  AND videos.privacy <> 1
  AND videos.privacy <> 2
  AND videos.shadowbanned_uploader = 0
  AND videos.status = 2
  AND videos.banned_uploader = 0
ORDER BY rel_score DESC
LIMIT 4
");

$stmt->execute([
    ':SEARCH_TITLE' => $search,
    ':SEARCH_TAGS'  => $search,
    ':SEARCH_DESC'  => $search,
    ':WHERE_TITLE'  => $search,
    ':WHERE_TAGS'   => $search,
    ':WHERE_DESC'   => $search,
    ':URL'          => $URL
]);

Videos::$Videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$Related_Videos = (new Videos($DB, $_USER))->fixed();

} else {
    notification("This video doesn't exist!","/","red"); exit();
}



?>
<div class="vlUpNext">
    Up next<br>
    <div class="vlUpNextButton" onclick="window.location='watch?v=<?= $randvid["url"] ?>'">
        <div class="vlUpNextThumb">
            <span class="thumbnail">
                <img style="height:100%;width: 100%;" src="/usfi/thmp/<?= $randvid["url"] ?>.jpg" alt="" title="">
                <span class="timestamp_v"><?= $randvid["length"] ?></span>
            </span>
        </div>
        <div class="vlUpNextInfo">
            <div class="vlUpNextInfoCont">
                <div class="vlUpNextInfoTitle"><?= $randvid["title"] ?></div>
                <div class="vlUpNextInfoFrom">From: <?= $randvid["displayname"] ?></div>
                <div class="vlUpNextInfoViews">Views: <?= $randvid["displayviews"] ?></div>
                <div class="vlUpNextInfoFeatured">Featured Video </div>            </div>
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<div style="clear: both;"></div>
<div class="vlSuggestions">
    <?php 
    $i = 0;
    if($Related_Videos) { ?>
    <?php foreach($Related_Videos as $Video) { ?>
        <div class="vlSuggestionsButton" onclick="window.location='watch?v=<?= $Video["url"] ?>'">
        <div class="vlSuggestionsThumb">
            <span class="thumbnail">
                <img style="height:100%;width: 100%;" src="/usfi/thmp/<?= $Video["url"] ?>.jpg" alt="<?= $Video["title"] ?>" title="<?= $Video["title"] ?>">
                <span class="timestamp_v"><?= $Video["length"] ?></span>
            </span>
        </div>
        <div class="vlSuggestionsInfo">
            <div class="vlSuggestionsInfoCont">
                <div class="vlSuggestionsInfoTitle"><?= $Video["title"] ?></div>
            </div>
        </div>
        <div style="clear:both"></div>
    </div>
    <?php $i++; ?>
    <?php } ?>
    <?php } ?>
    </div>
    </div>
