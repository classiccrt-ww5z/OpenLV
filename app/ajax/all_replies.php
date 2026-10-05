<?php
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";

//REQUIREMENTS / PERMISSIONS
//- Requires ($_POST["comment_id"])
if (!isset($_POST["comment_id"]))     { exit(); }


function user_avatar3($User,$Width,$Height,$Avatar,$Extra_Class = "") {
    if (strpos($Avatar,"u=") !== false) { $Avatar = str_replace("u=","",$Avatar); $Folder = "avt"; } else { $Upload = false; $Folder = "thmp"; }
    if (empty($Avatar) || !file_exists("../usfi/$Folder/$Avatar.jpg")) {
        $Avatar = "/img/no.png";
    } else {
        if ($Folder == "avt") {
            $Avatar = "/usfi/avt/$Avatar.jpg";
        } else {
            $Avatar = "/usfi/thmp/$Avatar.jpg";
        }
    }
    return '<a href="/user/'.$User.'"><img src="'.$Avatar.'" width="'.$Width.'" height="'.$Height.'" class="avt2 '.$Extra_Class.'" alt="'.$User.'"></a>';
}

$Replies = $DB->execute("SELECT video_comments.*, users.avatar, users.displayname FROM video_comments INNER JOIN users ON video_comments.by_user = users.username WHERE reply_to = :ID ORDER BY date_sent ASC", false, [":ID" => (int)$_POST["comment_id"]]);

unset($Replies[count($Replies) - 1]);
unset($Replies[count($Replies) - 1]);

$Uploaded_By = $DB->execute("SELECT uploaded_by FROM videos WHERE url = :URL", true, [":URL" => $Replies[0]["url"]])["uploaded_by"];

?>
<? foreach ($Replies as $Comment) : ?>
    <?
    if (!empty($Comment["raters"]) && $_USER->logged_in) {
        if (strpos($Comment["raters"],$_USER->username."+") !== false) {
            $Rated = "1";
        } elseif (strpos($Comment["raters"],$_USER->username."-") !== false) {
            $Rated = "-1";
        } else {
            $Rated = 2;
        }
    } else {
        $Rated = 2;
    }
    ?>
    <div class="wt_c_sct wt_r_sct<? if ($Comment["rating"] < -4) : ?> op_c<? endif ?>" id="wt_<?= $Comment["id"] ?>" op="<?= (int)$_POST["comment_id"] ?>" data-op-user="<?= $Comment["by_user"] ?>" data-editing="0">
        <div<? if ($Uploaded_By == $Comment["by_user"]) : ?> style="background:#fffcc2"<? elseif ("LiiVid" == $Comment["by_user"]) : ?> style="background:#d2ebff"<? endif ?>>
            <a href="/user/<?= $Comment["displayname"] ?>"><?= $Comment["displayname"] ?></a> <span>(<?= get_time_ago($Comment["date_sent"]) ?>)</span>
            <div>
                <? if ($_USER->logged_in) : ?><a href="javascript:void(0)" onclick="show_reply(<?= (int)$_POST["comment_id"] ?>,false,'<?= $Comment["displayname"] ?>')">Reply</a><? endif ?><?php if($_USER->logged_in && $Comment["by_user"] == $_USER->username) { ?><a href="javascript:void(0)" onclick="toggleEditComment(<?= $Comment["id"] ?>)" style="padding-left:9px;margin-left:9px;border-left:1px solid #7d7d7d">Edit</a><?php } ?><? if ($_USER->logged_in && ($Uploaded_By == $_USER->username || $Comment["by_user"] == $_USER->username || $_USER->Is_Admin || $_USER->Is_Mod)) : ?><a href="javascript:void(0)" onclick="delete_wtc(<?= $Comment["id"] ?>)" style="padding-left:9px;margin-left:9px;border-left:1px solid #7d7d7d">Delete</a><? endif ?>
            </div>
        </div>
        <div>
            <?= user_avatar3($Comment["displayname"],41,41,$Comment["avatar"],"wp_avt") ?>
            <div>
                <span<? if ($Comment["rating"] < 0) : ?> style="color:red"<? elseif ($Comment["rating"] > 0) : ?> style="color:green"<? endif ?>><?= $Comment["rating"] ?></span>
                <? if (!$_USER->logged_in) : ?><img src="/img/td0.png" onclick="alert('Please sign in to rate this comment')"><img src="/img/tu0.png" onclick="alert('Please sign in to rate this comment')"><? else : ?><img <? if ($Rated == "-1") : ?>src="/img/td1.png" style="opacity:0.75" <? else : ?>src="/img/td0.png" onclick="wr(<?= $Comment["id"] ?>,'0',this)"<? endif ?>><img <? if ($Rated == "1") : ?> src="/img/tu1.png" style="opacity:0.75" <? else : ?>src="/img/tu0.png" onclick="wr(<?= $Comment["id"] ?>,'1',this)"<? endif ?>><? endif ?>
            </div>
            <div id="comment-<?= $Comment["id"] ?>-content" style="width:442px;display: block;">
														<?= showBBcodes(hashtag_search(mention(nl2br($Comment["comment"])))) ?>
													</div>
												<div class="comment-edit-container" id="comment-<?= $Comment["id"] ?>-edit-container" style="display: none;width: calc(100% - 50px)"><textarea id="comment-<?= $Comment["id"] ?>-edit-input" class="comment-textarea comment-edit-input" cols="52" rows="5" maxlength="1000"></textarea><div><button class="search_button save-comment-edit-button" onclick="saveCommentEdit(<?= $Comment["id"] ?>)">Save</button><button class="search_button" onclick="toggleEditComment(<?= $Comment["id"] ?>)">Cancel</button></div></div>
        </div>
    </div>
<? endforeach ?>
