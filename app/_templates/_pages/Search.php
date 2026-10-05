<div class="search_hd">
    <div>
        <strong>Search: </strong>
        <a href="/results?q=<?= urlencode($Normal_Search_Query) ?>"><strong<?php if (!isset($_GET["f"])) { echo ' style="color:black"'; } ?>>All</strong></a>
        <a href="/results?q=<?= urlencode($Normal_Search_Query) ?>&f=1"><strong<?php if (isset($_GET["f"]) && $_GET["f"] == 1) { echo ' style="color:black"'; } ?>>Videos</strong></a>
        <a href="/results?q=<?= urlencode($Normal_Search_Query) ?>&f=2"><strong<?php if (isset($_GET["f"]) && $_GET["f"] == 2) { echo ' style="color:black"'; } ?>>Users</strong></a>
    </div>

    <?php if (!isset($_GET["f"]) || $_GET["f"] != 2) { ?>     
        <form method="GET" id="sort-by-form" style="display: inline-block; margin-left: 10px">
            <?php if (isset($_GET["f"])) { ?>
                <input type="hidden" name="f" value="<?= htmlspecialchars($_GET["f"] ?? '', ENT_QUOTES) ?>"> 
            <?php } ?>
            <input type="hidden" name="q" value="<?= $Normal_Search_Query ?>">

            <?php if ($Sort != "relevance") { ?>
                <input type="hidden" name="sort-type" value="<?= $SortType ?>">   
            <?php } ?>

            <label for="sort" class="small-gray">Sort by</label>
            <select name="sort" id="sort" class="blue-gradient-dropdown" onchange="document.getElementById('sort-by-form').submit()">
                <option value="relevance" <?php if ($Sort == "relevance") { echo 'selected=""'; } ?>>Relevance</option>
                <option value="upload-date" <?php if ($Sort == "upload-date") { echo 'selected=""'; } ?>>Upload date</option>
                <option value="view-count" <?php if ($Sort == "view-count") { echo 'selected=""'; } ?>>View count</option>
                <option value="comment-count" <?php if ($Sort == "comment-count") { echo 'selected=""'; } ?>>Comment count</option>
            </select>                        

            <?php if ($Sort != "relevance") { ?>
                <select name="sort-type" id="sort-type" class="blue-gradient-dropdown" onchange="document.getElementById('sort-by-form').submit()">
                    <option value="asc" <?php if ($SortType == "asc") { echo 'selected=""'; } ?>>Ascending</option>
                    <option value="desc" <?php if ($SortType == "desc") { echo 'selected=""'; } ?>>Descending</option>
                </select>                            
            <?php } ?>
        </form>
    <?php } ?>

    <div>
        About <?= number_format($Total) ?> "<?= htmlspecialchars($Normal_Search_Query) ?>" results in <?= $Search_Time ?>ms
    </div>
</div>

<div>
    <?php if (!isset($_GET["f"]) || $_GET["f"] == 1) { ?>
        <?php if (isset($User) && !isset($_GET["p"])) { ?>
            <div class="result" style="margin-top:20px;width: 500px;">
                <div style="float:left;margin-right:10px">
                    <?= user_avatar2($User["displayname"], 100, 100, $User["avatar"]) ?>
                </div>
                <a href="/user/<?= $User["displayname"] ?>" class="r_title"><?= $User["displayname"] ?></a>
                <?php if (!empty($User["channel_description"])) { echo cut_string($User["channel_description"], 100); } else { echo "<em>No Description</em>"; } ?>
                <div style="margin-top:6px">
                    <?= number_format($User["subscribers"]) ?> subscribers - <?= number_format($User["video_views"]) ?> video views - <?= number_format($User["channel_views"]) ?> channel views
                </div>
            </div>
        <?php } ?>

        <?php foreach ($Videos as $Video) { ?>
            <div class="result">
                <div class="th">
                    <div class="th_t"><?= $Video["length"] ?></div>
                    <a href="/watch?v=<?= $Video["url"] ?>"><img class="vid_th" <?= $Video["thumbnail"] ?> width="186" height="111"></a>
                </div>
                <a href="/watch?v=<?= $Video["url"] ?>" class="r_title"><?= $Video["title"] ?></a>
                By. <a href="/user/<?= $Video["displayname"] ?>"><?= $Video["displayname"] ?></a><br>
                <?= get_time_ago($Video["uploaded_on"]) ?> - <?= number_format($Video["displayviews"]) ?> views
                <div class="r_descr"><?= cut_string($Video["description"], 135) ?></div>
                <?= show_ratings($Video, 15, 14) ?>
            </div>
        <?php } ?>

    <?php } else { ?>
        <?php foreach ($Channels as $User) { ?>
            <div class="result" style="margin-top:20px;width: 500px;">
                <div style="float:left;margin-right:10px">
                    <?= user_avatar2($User["displayname"], 100, 100, $User["avatar"]) ?>
                </div>
                <a href="/user/<?= $User["displayname"] ?>" class="r_title"><?= $User["displayname"] ?></a>
                <?php if (!empty($User["channel_description"])) { echo cut_string($User["channel_description"], 100); } else { echo "<em>No Description</em>"; } ?>
                <div style="margin-top:6px">
                    <?= number_format($User["subscribers"]) ?> subscribers - <?= number_format($User["video_views"]) ?> video views - <?= number_format($User["channel_views"]) ?> channel views
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<div style="padding-top:5px;margin-top:17px;border-top: 1px solid #ccc;font-weight: bold;word-spacing:4px">
    <?php if (!isset($_GET["f"])) { ?>
        <?= $_PAGINATION->new_show($_PAGINATION->Total,"q=".urlencode($_GET["q"])) ?>
    <?php } elseif ($_GET["f"] == 1) { ?>
        <?= $_PAGINATION->new_show($_PAGINATION->Total,"q=".urlencode($_GET["q"])."&f=1") ?>
    <?php } elseif ($_GET["f"] == 2) { ?>
        <?= $_PAGINATION->new_show($_PAGINATION->Total,"q=".urlencode($_GET["q"])."&f=2") ?>
    <?php } ?>
</div>