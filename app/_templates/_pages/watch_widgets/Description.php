<?php if ($Has_Banner) { ?>
    <img src="/usfi/wbner/<?= $Uploaded_By ?>.png" style="width:100%;height:50px;display:block;-webkit-user-drag: none">
<?php } ?>

<div class="wt_des" style="<?php 
    if ($_USER->logged_in && $_USER->username == $Uploaded_By) { 
        echo 'margin-bottom:0;border-bottom-left-radius:0;border-bottom-right-radius:0'; 
    } 
    if ($Has_Banner) { 
        echo ';border-top-left-radius:0;border-top-right-radius:0'; 
    } 
?>">
    <div>
        <?= user_avatar2($Uploader["displayname"], 55, 55, $Uploader["avatar"]) ?>

        <div class="wt_person">
            <a href="/user/<?= $Uploader["displayname"] ?>"><?= $Uploader["displayname"] ?></a> <?= ($Uploader["partner"] == 1) ? '<img style="width: 20px;vertical-align: middle;float:unset" title="This user is verified!" src="/img/verified.png">' : null ?><br>
            <?= get_date($_VIDEO->Info["uploaded_on"]) ?><br>
            (<a href="javascript:void(0)" id="show_more">more info</a>)
        </div>

        <div id="subsbtns">
            <?php if (!$Is_Blocked && !$Has_Blocked) { ?>
                <?php if (!$_USER->Owns_Video) { ?>
                    <?= subscribe_button2($Uploaded_By) ?>
                <?php } else { ?>
                    <a href="/my_account" class="yel_btn" style="border-radius:5px !important;margin-right:7.5px">Edit Channel</a>
                <?php } ?>
            <?php } else { ?>
                <?= subscribe_button2($Uploaded_By, true) ?>
            <?php } ?>

            <a href="/user/<?= $Uploader["displayname"] ?>/subscribers" class="yel_btn"<?php if ($_USER->Owns_Video) { echo ' style="display:none"'; } ?>>
                <?= number_format($Uploader["subscribers"]) ?>
            </a>
        </div>
    </div>

    <div class="cl"></div>

    <div id="des_text" style="max-height:84px">
        <?php if (!empty($Description)) { ?>
            <?= DoLinks(nl2br($Description)) ?>
        <?php } else { ?>
            <em>No Description...</em>
        <?php } ?>
    </div>

    <div id="des_info" class="hddn">
        <div>
            <div>Category: </div>
            <div>
                <a href="/videos?c=<?= $Category ?>&o=re&t=2"><?= $Categories[$Category] ?></a>
            </div>
        </div>
        <div class="cl"></div>

        <?php if (!empty($_VIDEO->Info["tags"])) { ?>
            <div>
                <div>Tags:</div>
                <div style="width: 281px">
                    <?php foreach ($Tags as $Tag) { ?>
                        <a href="/results?q=<?= urlencode($Tag) ?>"><?= $Tag ?></a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <div>
        <table>
            <tr>
                <td align="right"><label for="em">Embed</label></td>
                <td>
                    <input type="text" id="em" onclick="$(this).select()" readonly 
                        value='<iframe allowfullscreen src="http://www.liivid.lol/embed?v=<?= $URL ?>&a=0" frameborder="0" width="640" height="360"></iframe>'>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php if ($_USER->logged_in && $_USER->username == $Uploaded_By) { ?>
    <div style="border-bottom-right-radius:4px;border-bottom-left-radius:4px;background:#e9ebff;margin-bottom:9px;border:1px solid #ccc;border-top:0;padding:7px;text-align:center">
        <a href="/edit_video?v=<?= $URL ?>">
            <button class="search_button" type="button" style="padding:2px 8px;margin:0 31px 0 0">Edit Video</button>
        </a>
        <a href="/analytics?page=videos&video=<?= $URL ?>">
            <button type="button" class="search_button" style="padding:2px 8px;margin:0 31px 0 0">Analytics</button>
        </a>
        <a href="/ajax/df/delete?v=<?= $URL ?>" onclick="return confirm('Are you sure you want to delete this video?')">
            <button type="button" class="search_button" style="padding:2px 8px">Delete Video</button>
        </a>
    </div>
<?php } ?>

<?php if ($_USER->Is_Admin || ($_USER->Is_Mod && !$_USER->Owns_Video)) { ?>
    <script>
        function shadowban(user) {
            $.ajax({
                type: "POST",
                url: "/ajax/shadowban",
                data: {user: user},
                success: function (output) {
                    if (output == "d") {
                        $("#shadowban").html("Shadowban Uploader");
                    } else {
                        $("#shadowban").html("Undo Shadowban");
                    }
                }
            });
        }
    </script>

    <div style="border-radius:4px;background:#fffbbc;margin-bottom:5px;border:1px solid #ccc;padding:7px;text-align:center">
        <?php if ($_VIDEO->Info["featured"] == 0) { ?>
            <button class="search_button" style="float:left;width:148px" onclick="feature_video('<?= $URL ?>')" id="feature_video">Feature Video</button>
        <?php } else { ?>
            <button class="search_button" style="float:left;width:148px" onclick="feature_video('<?= $URL ?>')" id="feature_video">Remove Feature</button>
        <?php } ?>

        <button class="search_button" style="float:right;width:148px" id="shadowban" onclick="shadowban('<?=  $Uploaded_By ?>')">
            <?php if ($Uploader["shadowbanned"]) { echo "Undo Shadowban"; } else { echo "Shadowban Uploader"; } ?>
        </button>

        <div style="clear:both"></div>

        <div style="overflow:hidden;margin-top:10px">
            <a href="/admin/videos?v=<?= $URL ?>">
                <button class="search_button" style="width:148px;float:left" type="button">Edit Video</button>
            </a>
            <a href="/analytics?page=videos&video=<?= $URL ?>">
                <button class="search_button" style="width:148px;float:right" type="button">Analytics</button>
            </a>
        </div>
    </div>
<?php } ?>
