<style>
    <?php
    // The id must be an integer: it was previously concatenated straight into a path,
    // allowing traversal and arbitrary .txt disclosure. The contents are printed inside
    // a <style> block, so strip "<" rather than HTML-escaping (which would mangle ">"
    // child selectors).
    $Group_CSS_Path = $_SERVER["DOCUMENT_ROOT"] . "/u/css/" . (int)$_REQUEST["id"] . ".txt";
    if (file_exists($Group_CSS_Path)) {
        echo str_replace("<", "", file_get_contents($Group_CSS_Path));
    }
    ?>
    .contentBox {
        border-radius: 0;
        padding:6px;
    }
    #groupName {
        font-weight: bold;
        font-size: 16px;
    }
    #groupLinks {
        margin: 6px 0px;
    }
    #groupCommonHeader {
        border-bottom: 1px dashed #999;
        padding-bottom: 12px;
        margin-bottom: 12px;
    }
    #groupOptionsDiv {
        float: right;
        margin-left: 10px;
        line-height: 15px;
    }
    .highlightBox {
        text-align: center;
        margin-top: 3px;
        font-size: 14px;
    }
    #groupFlagAsDiv {
    padding-top: 5px;
    }
    .mainForumTable {
        padding-top: 3px;
        padding-bottom: 3px;
    }
    .grid-view {
    margin-top: 4px;
    }
    #groupRecentVideosDiv {
        padding-top: 2px;
    }
    span[style="font-weight:bold;background-color:#FFF;padding:1px 4px 1px 4px;border:1px solid #999;margin-right:5px"]{
        border:0!important;
    }
    span[style="font-weight:bold;background-color:#CCC;padding:1px 4px 1px 4px;border:1px solid #999;margin-right:5px"]{
        border:0!important;
    }
    .vltitle {
        height: 28px;
        overflow: hidden;
    }
</style>
<script>
function addToQuickList(e) {
    var url = e.parentElement.id.slice(17);
    var msg = e.parentElement.parentElement.parentElement.querySelector("#quicklist-inlist-"+url);
    e.parentElement.parentElement.parentElement.querySelector("#video-actions-"+url).style.display = "none";
    e.parentElement.parentElement.style.display = "none";
    e.parentElement.parentElement.parentElement.querySelector(".video-time").style.display = "none";
    msg.style.display = "block";
    var url = "/a/quicklist?v="+url;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 400) {
            } else {
                showErrorMessage();
            }
        };
        xhr.onerror = function() {
            showErrorMessage();
        };
        xhr.send();
}
function openQueueMenu(e,u) {
    var p = e.parentElement.parentElement.parentElement.parentElement.parentElement;
    if (p.querySelector('#yt-uix-button-menu-'+u).style.display == "none") {
        e.classList.add('yt-uix-button-active');
    }
    else {
        e.classList.remove('yt-uix-button-active');
    }
    p.querySelector('#yt-uix-button-menu-'+u).style.display = p.querySelector('#yt-uix-button-menu-'+u).style.display === 'none' ? '' : 'none';
}
</script>
<div id="mainContent">

        <div class="contentBox">
                <div id="groupCommonHeader">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr valign="top">
                <td width="130">
                    <div id="groupStillDiv">
                        <img src="/u/grp/<?= $Group["id"] ?>.jpg" class="vimg120">
                    </div>
                </td>
                <td>
                    <div id="groupHeaderInfo">
                        <div id="groupName"><?= $Group["title"] ?></div>
                        <div id="groupLinks">
                                <a href="/group?id=<?= $Group["id"] ?>&action=videos"><?= $LANGS['groupvideos'] ?>: <?= $DB->execute("SELECT count(video) as amount FROM groups_videos WHERE group_id = :ID", true, [":ID" => $Group["id"]])["amount"] ?></a> | <a href="/group?id=<?= $Group["id"] ?>&action=members"><?= $LANGS['groupmembers'] ?>: <?= $DB->execute("SELECT count(member) as amount FROM groups_members WHERE group_id = :ID", true, [":ID" => $Group["id"]])["amount"] ?></a> | <a href="/group?id=<?= $Group["id"] ?>"><?= $LANGS['discussions'] ?>: <?= $DB->execute("SELECT count(id) as amount FROM groups_topics WHERE group_id = :ID", true, [":ID" => $Group["id"]])["amount"] ?></a>

                        </div>
                        

                        <div id="groupDesc">
                        
                <span id="BeginvidDesc">
    <?= DoLinks(nl2br($Group["description"])) ?>
    </span>
                        </div>
                    </div>
                </td>
                <td align="right" nowrap>
                    <div id="groupOptionsDiv" class="highlightBox">
                        <? if ($_USER->logged_in && !$Is_Member or $Owns_Group && !$Is_Member) : ?>
        <a href="/a/join_group?group=<?= $Group["id"] ?>"><? if (!$Requested) : ?>
                                                                                        <?= $LANGS['jointhisgroup'] ?>
                                                                                    <? else : ?>
                                                                                        <?= $LANGS['removerequest'] ?>
                                                                                    <? endif ?></a><br>
                        <? elseif ($_USER->logged_in && $Is_Member or $Owns_Group && $Is_Member) : ?>
                        <a href="/a/join_group?group=<?= $Group["id"] ?>"><?= $LANGS['leavegroup'] ?></a><br>
            <? endif ?>
            <? if ($_USER->logged_in && ($Owns_Group || $_USER->Is_Admin || $_USER->Is_Mod)) : ?>
                                <div id="groupFlagAsDiv" class="smallText">
        <a href="/group_moderation?id=<?= (int)$_GET["id"] ?>"><?= $LANGS['moderation'] ?></a>
        </div>
                            <? endif ?>
                                                </div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                </td>
            </tr>
        </table>
    </div>
<? if (!isset($_GET["topic_id"]) && !isset($_GET["action"])) : ?>
                
    <div id="groupRecentVideosDiv">
        <h3><?= $LANGS['recentvideos'] ?>&nbsp;&nbsp;
            <span class="smallText" style="font-weight: normal;">
                <a href="/group?id=<?= (int)$_GET["id"] ?>&action=videos"><?= $LANGS['viewallvideos'] ?></a><? if ($_USER->logged_in && $Is_Member) : ?> | <a href="/submit_group_video?id=<?= (int)$_GET["id"] ?>"> <?= $LANGS['addvideo'] ?></a><? endif ?>
            </span>
        </h3>
<div style="margin-top: 12px;">
                <table width="21" height="121" cellpadding="0" cellspacing="0">
                    <tbody><tr>
                        <td>
                            <table width="680" height="121" cellpadding="0" cellspacing="0">
                                <tbody><tr>
                                    <td style="border-bottom:none;">
                                        <? if ($Videos) : ?>
                                            <? $Count = 0; ?>
                                            <? $Count2 = 1; ?>
                                            <? $Pages = ceil(count($Videos) / 5); ?>
                                            <? foreach ($Videos as $Video) : ?>
                                                <?
                                                if (file_exists($_SERVER["DOCUMENT_ROOT"]."/u/thmp/".$Video["url"].".jpg")) {
                                                    $Thumbnail = "/u/thmp/".$Video["url"].".jpg";
                                                } else {
                                                    $Thumbnail = "/img/nothump.png";
                                                }
                                                ?>
                                                <? if ($Count == 0) : ?><div id="subsl<?= $Count2 ?>"<? if ($Count2 != 1) : ?> style="display:none;"<? endif ?>><? endif ?>
                                                <div style="display:inline-block;width: 124px;word-spacing:0px;vertical-align: top;padding-right: 9px;position: relative;">
                                                    <div class="v120WideEntry" style="margin-bottom: 2px;">
                                                <div class="v120WrapperOuter">
                                                    <div class="v120WrapperInner"><a href="/watch?v=<?= $Video["url"] ?>"><img src="<?= $Thumbnail ?>" class="vimg120"></a><?php if (!in_array($Video["url"], $_USER->QuickList)): ?>
                <span class="video-actions" id="video-actions-<?= $Video['url'] ?>"><button type="button" class="yt-uix-button yt-uix-button-short yt-uix-button-arrowbutton" id="yt-uix-button-arrowbutton-<?= $Video['url'] ?>" onclick="openQueueMenu(this,'<?= $Video['url'] ?>');return false;"> <img class="yt-uix-button-arrow" src="/img/pixel.gif" alt=""></button></span>
                <div id="quicklist-inlist-<?= $Video['url'] ?>" class="hid quicklist-inlist"><a href="/my_quicklist">Added to<br>queue</a></div>
            <?php endif ?><?php if (!in_array($Video["url"], $_USER->QuickList)): ?>
                <span class="video-actions" id="video-actions-<?= $Video['url'] ?>"><button type="button" class="yt-uix-button yt-uix-button-short yt-uix-button-arrowbutton" id="yt-uix-button-arrowbutton-<?= $Video['url'] ?>" onclick="openQueueMenu(this,'<?= $Video['url'] ?>');return false;"> <img class="yt-uix-button-arrow" src="/img/pixel.gif" alt=""></button></span>
                <div id="quicklist-inlist-<?= $Video['url'] ?>" class="hid quicklist-inlist"><a href="/my_quicklist">Added to<br>queue</a></div>
            <?php endif ?><div class="video-time">
                    <span id="video-run-time"><? if ($Video["length"] >= 60): ?><?=ltrim(date('i:s', $Video["length"]), 0)?> <? else: ?><?= gmdate('0:s', $Video["length"]) ?><? endif ?></span>
                    </div>
                                                    </div>
                                                </div>
                                            </div>
            <div class="vltitle">
                <div class="vlshortTitle" style="font-weight:bold">
                    <a href="<?= $Video["link"] ?>" title="<?= $Video["title"] ?>"><?= $Video["title"] ?></a> 
                </div>
            </div>

<div class="vlfacets" style="font-size: 11px;">
    <span id="video-num-views" style="color:#666;margin:0"><?= get_time_ago($Video["uploaded_on"]) ?></span><br>
                <span id="video-num-views" style="color:#666;margin:0"><? if ($LANGS['numberformat'] == 1): ?><?= number_format($Video["views"]) ?><? else: ?><?= ($Video["views"]) ?><? endif ?> <?= $LANGS['videoviews'] ?></span>
                <div><span class="vlfrom">          <a href="/user/<?=$Video["uploaded_by"]?>"><?= displayname($Video["uploaded_by"],0,34) ?></a>
                    <div class="star_rating"><? show_ratings($Video,"12px","12px") ?></div>
</span></div>
                <div class="clearL"></div>
        </div><ul style="display:none;" class="yt-uix-button-menu yt-uix-button-menu-short" id="yt-uix-button-menu-<?= $Video['url'] ?>"><li id="add-to-quicklist-<?= $Video['url'] ?>"><span class=" yt-uix-button-menu-item" onclick="addToQuickList(this);return false;">Add to queue</span></li></ul>
                                                </div>
                                                <? if ($Count == 4 && ($Count2) != $Pages) : ?></div><? $Count2++ ?><? endif ?>
                                                <? $Count++ ?>
                                                <? if ($Count == 5) { $Count = 0; } ?>
                                            <? endforeach ?>
                                            <? if ($Count2 == 4) : ?></div><? endif ?>
                                        <? else : ?>
                                            <div style="text-align:center; font-weight: bold; font-size: 14px;">
                                                <?= $LANGS['novideos'] ?>
                                            </div>
                                        <? endif ?>
                                    </td>
                                </tr>
                                </tbody></table>
                        </td>
                    </tr>
                    </tbody></table>
            </div>
    </div> <!-- end recentVideosDiv -->




    <div id="groupRecentTopicsDiv">
    <h3><?= $LANGS['discussions'] ?>
        &nbsp;<span style="font-size: 12px; font-weight: normal; color: #000;">(<?= $DB->execute("SELECT count(id) as amount FROM groups_topics WHERE group_id = :ID", true, [":ID" => $Group["id"]])["amount"] ?>)
        &nbsp; <span class="smallText label">
            <? if (!$_USER->logged_in) : ?><a href="/login"><?= $LANGS['login'] ?></a> <?= $LANGS['topostatopic'] ?><? elseif (!$Is_Member) : ?><a href="/a/join_group?group=<?= $Group["id"] ?>"><?= $LANGS['jointhisgroup'] ?></a> <?= $LANGS['topostatopic'] ?><? else : ?><a href="/create_discussion?id=<?= $Group["id"] ?>"><?= $LANGS['creatediscussion'] ?><? endif ?></a>
        </span>
    </span>
    </h3>
                    <? if ($Topics) : ?>
        <table cellpadding="3" cellspacing="0" width="100%">
            <tr>
                <td class="grayText smallText" width="50%"><?= $LANGS['topic'] ?></td>
                <td align="center" class="grayText smallText" width="20%"><?= $LANGS['author'] ?></td>
                <td align="center" class="grayText smallText" width="10%"><?= $LANGS['replies'] ?></td>
                <td align="center" class="grayText smallText" width="18%"><?= $LANGS['lastpost'] ?></td>
            </tr>
        <? foreach ($Topics as $Topic) : ?>
            <?
                                            $Posts = $DB->execute("SELECT count(id) as amount FROM groups_messages WHERE topic_id = :ID", true, [":ID" => $Topic["id"]])["amount"];
                                            $Last_Post = $DB->execute("SELECT submit_date FROM groups_messages WHERE topic_id = :ID ORDER BY submit_date DESC LIMIT 1", true, [":ID" => $Topic["id"]])["submit_date"];
                                        ?>
                <tr class="mainForumTable" style="padding-top:3px; padding-bottom:3px;" valign="top" bgcolor="#999999">

                                            <td bgcolor="#FFF" style="padding-left: 3px; padding-top:3px; padding-bottom:3px;">
                                                <a href="/group?id=<?= (int)$_GET["id"] ?>&topic_id=<?= $Topic["id"] ?>"><?= $Topic["title"] ?></a>
                                            </td>
                                            <td align="center" bgcolor="#FFF" style=""><a href="/user/<?= $Topic["created_by"] ?>"><?= displayname($Topic["created_by"]) ?></a></td>
                                            <td align="center" bgcolor="#FFF" style=""><?= $Posts ?></td>
                                            <td align="center" bgcolor="#FFF" style="padding-left: 3px;padding-right: 3px;"><?= get_time_ago($Last_Post) ?></td>
                                        </tr>
                                    <? endforeach ?>
                
            <tr bgcolor="#FFF">
                <td colspan="10">       <div class="pagingDiv">
<?= $_PAGINATION->new_show("id=".$_GET["id"]) ?>

        </div>


                </td>
            </tr>
        <? else: ?>
            <div style="padding-left: 1px;">
                <table width="21" height="121" cellpadding="0" cellspacing="0">
                    <tbody><tr>
                        <td>
                            <table width="680" height="121" cellpadding="0" cellspacing="0">
                                <tbody><tr>
                                    <td style="border-bottom:none;">
                                                                                    <div style="text-align:center; font-weight: bold; font-size: 14px;">
                                                <?= $LANGS['nodiscussions'] ?>
                                            </div>
                                                                            </td>
                                </tr>
                                </tbody></table>
                        </td>
                    </tr>
                    </tbody></table>
            </div>
            <? endif ?>
    
        </table>
        

    
    </div> <!-- end recentTopicsDiv -->

    <div id="groupMembersDiv">
    <h3><?= $LANGS['groupmembers'] ?>
        &nbsp;<span style="font-size: 12px; font-weight: normal; color: #000;">(<?= $Member_Amount ?>)
        &nbsp; <span class="smallText label"><a href="/group?id=<?= (int)$_GET["id"] ?>&action=members" style="font-weight:normal"><?= $LANGS['viewallmembers'] ?></a></span>
    </span>
    </h3>
        <table width="625" height="121" style="background-color: #FFFFFF" cellpadding="0" cellspacing="0">
                                <tbody><tr>
                                    <td style="border-bottom:none;">
                                        <? $Count = 0; ?>
                                        <? $Count2 = 1; ?>
                                        <? $Pages = ceil(count($Members) / 5); ?>
                                        <? foreach ($Members as $Member) : ?>
                                            <? if ($Count == 0) : ?><div id="vil<?= $Count2 ?>"<? if ($Count2 != 1) : ?> style="display:none"<? endif ?>><? endif ?>
                                            <div class="videobarthumbnail_block2" id="div_recent_friends_0" style="position:relative;bottom:6px">
                                                <center>
                                                    <a href="/user/<?= $Member["member"] ?>"><img src="" width="60" height="60" style="border: 3px double #999; margin-bottom: 5px;"></a><br>
                                                    <a href="/user/<?= $Member["member"] ?>" style="font-size:11px"><?= ($Member["member"]) ?></a>
                                            </div>
                                            <? if ($Count == 4 && ($Count2) != $Pages) : ?></div><? $Count2++ ?><? endif ?>
                                            <? $Count++ ?>
                                            <? if ($Count == 5) { $Count = 0; } ?>
                                        <? endforeach ?>
                <? if ($Count2 == 4) : ?></div><? endif ?>
                                    </td>
                                </tr>
                                </tbody></table>

    
    </div> <!-- end membersDiv -->

    <div id="groupAboutGroupDiv">
        <h3><?= $LANGS['aboutgroup'] ?></h3>
        <div class="smallText">
            <span class="grayText"><?= $LANGS['category'] ?>:</span>
        <a href="/groups?c=<?= $Group["categories"] ?>"> <?= $Group_Category[$Group["categories"]] ?></a>


<br>
            <span class="grayText"><?= $LANGS['owner'] ?>:</span>
            <a href="/user/<?= $Group["created_by"] ?>"><?= ($Group["created_by"]) ?></a><br>
            <span class="grayText"><?= $LANGS['groupcreated'] ?>:</span>
            <?php setlocale(LC_TIME, $LANGS['languagecode']);
                    if (isset($_COOKIE['time_machine'])) { echo strftime($LANGS['longtimeformat'], time_machine(strtotime($Group["creation_date"]))); }
                    else {echo strftime($LANGS['longtimeformat'], strtotime($Group["creation_date"])); }  ?>
<br>
<span class="grayText"><?= $LANGS['grouptype'] ?>:</span>
            <? if ($Group["instant_join"] == 1) : ?><?= $LANGS['instantjoin'] ?><? else : ?><?= $LANGS['approvalrequired'] ?><? endif ?>
<br>
            <span class="grayText"><?= $LANGS['groupurl'] ?>:</span>
            <a href="/group?id=<?= $Group["id"] ?>">http://www.bitview.net/group?id=<?= $Group["id"] ?></a>
    
            <br/>
        </div>
    </div> <!-- end aboutGroupDiv -->
<? elseif (isset($_GET["topic_id"])) : ?>
    <div id="groupDiscussionDiv">
    <h3><?= $Topic["title"] ?>
        &nbsp; <span class="smallText label"><? if ($_USER->logged_in && ($_USER->Is_Admin || $_USER->Is_Mod || $Owns_Group)) : ?>
                                    <a href="/a/delete_discussion?id=<?= $_GET["topic_id"] ?>"><?= $LANGS['deletediscussion'] ?></a>
                                    <? endif ?></span>
    </span>
    </h3><div style="margin-left: 10px; margin-right: 10px;background:white;border-top:0;box-sizing:border-box;padding:5px">
                    <? foreach ($Topic_Messages as $Message) : ?>
                        <div style="border-bottom: 1px dashed #999;padding-bottom:5px;margin-bottom:5px;overflow:hidden">
                            <div style="width:10%;float:left;padding-right:5px;text-align:center">
                                <img src="<?= avatar($Message["by_user"]) ?>" width="60" height="60" style="border: 3px double #999; margin-bottom:3px;"><br />
                                <a href="/user/<?= $Message["by_user"] ?>"><?= displayname($Message["by_user"]) ?></a>
                            </div>
                            <div style="padding-left:10px;min-height:75px;position:relative;float: left;width:85%;padding-bottom:0">
                                <?= DoLinks(nl2br($Message["message"])) ?>
                                <div style="color:gray;font-size:10px;position:absolute;left:10px;bottom:0"><?= get_time_ago($Message["submit_date"]) ?><? if ($_USER->logged_in && $First_Message != $Message["id"] && ($Owns_Group || $_USER->Username == $Message["by_user"] || $_USER->Is_Admin || $_USER->Is_Mod)) : ?> | <a href="/a/delete_message?id=<?= $Message["id"] ?>"><?= $LANGS['delete'] ?></a><? endif ?></div>
                            </div>
                        </div>
                    <? endforeach ?>
                    <div>
                        <?= $MESSAGE_PAGINATION->new_show("id=".$_GET["id"]."&topic_id=".$_GET["topic_id"]) ?>
                    </div>
                    <? if ($_USER->logged_in && $Is_Member) : ?>
                    <div style="margin-top:10px">
                        <form action="/a/post_reply" method="POST">
                            <div style="font-weight:bold;margin-bottom:3px"><?= $LANGS['postreply'] ?>:</div>
                            <textarea maxlength="5000" name="reply" style="width:99%" rows="4"></textarea>
                            <input type="hidden" name="topic_id" value="<?= $Topic["id"] ?>">
                            <input type="submit" style="margin-top:3px" name="post_reply" value="<?= $LANGS['post'] ?>">
                        </form>
                    </div>
                    <? endif ?>
                </div>
    
    </div>
<? elseif ($_GET["action"] == "members") : ?>
    <div id="groupMembersDiv">
    <h3><?= $LANGS['groupmembers'] ?>&nbsp;<span style="font-size: 12px; font-weight: normal; color: #000;">(<?= $Member_Amount ?>)
    </span>
    </h3>
        <table width="680" height="121" style="background-color: #FFFFFF" cellpadding="0" cellspacing="0">
                                <tbody><tr>
                                    <td style="border-bottom:none;">
                                        <? $Count = 0; ?>
                                        <? $Count2 = 1; ?>
                                        <? foreach ($Members as $Member) : ?>
                                            <? if ($Count == 0) : ?><div id="vil<?= $Count2 ?>"<? if ($Count2 != 1) : ?> style="display:none"<? endif ?>><? endif ?>
                                            <div class="videobarthumbnail_block2" id="div_recent_friends_0" style="position:relative;bottom:6px">
                                                <center>
                                                    <a href="/user/<?= $Member["member"] ?>"><img src="<?= user_avatar($Member["member"]) ?>" width="60" height="60" style="border: 3px double #999; margin-bottom: 5px;"></a><br>
                                                    <a href="/user/<?= $Member["member"] ?>" style="font-size:12px;white-space: nowrap;"><?= displayname($Member["member"]) ?></a>
                                                    <? if ($_USER->logged_in && $Owns_Group && $Member["accepted"] == 0) : ?><br /><a href="/a/accept_member?user=<?= $Member["member"] ?>&id=<?= (int)$_GET["id"] ?>" style="font-size:11px"><?= $LANGS['accept'] ?></a><br /><a href="/a/decline_member?user=<?= $Member["member"] ?>&id=<?= (int)$_GET["id"] ?>" style="position:relative;top:3px;font-size:11px"><?= $LANGS['decline'] ?></a><? endif ?>
                                            </div>
                                            <? if ($Count == 4 && ($Count2) != $Pages) : ?></div><? $Count2++ ?><? endif ?>
                                            <? $Count++ ?>
                                        <? endforeach ?>
                <? if ($Count2 == 4) : ?></div><? endif ?>
                                    </td>
                                </tr>
                                </tbody></table>

    
    </div>
<? elseif ($_GET["action"] == "videos") : ?>
<div id="groupRecentVideosDiv">
        <h3><?= $LANGS['recentvideos'] ?>&nbsp;&nbsp;<span style="font-size: 12px; font-weight: normal; color: #000;">(<?= $DB->execute("SELECT count(video) as amount FROM groups_videos WHERE group_id = :ID", true, [":ID" => $Group["id"]])["amount"] ?>)</span>&nbsp;&nbsp;
            <span class="smallText" style="font-weight: normal;">
                <? if ($_USER->logged_in && $Is_Member) : ?><a href="/submit_group_video?id=<?= (int)$_GET["id"] ?>"> <?= $LANGS['addvideo'] ?></a><? endif ?>
            </span>
        </h3>
<div style="margin-top: 12px;">
                <table width="21" height="121" cellpadding="0" cellspacing="0">
                    <tbody><tr>
                        <td>
                            <table width="680" height="121" cellpadding="0" cellspacing="0">
                                <tbody><tr>
                                    <td style="border-bottom:none;">
                                        <? if ($Videos) : ?>
                                            <? $Count = 0; ?>
                                            <? $Count2 = 1; ?>
                                            <? foreach ($Videos as $Video) : ?>
                                                <?
                                                if (file_exists($_SERVER["DOCUMENT_ROOT"]."/u/thmp/".$Video["url"].".jpg")) {
                                                    $Thumbnail = "/u/thmp/".$Video["url"].".jpg";
                                                } else {
                                                    $Thumbnail = "/img/nothump.png";
                                                }
                                                ?>
                                                <? if ($Count == 0) : ?><div id="subsl<?= $Count2 ?>"<? if ($Count2 != 1) : ?> style="display:none"<? endif ?>><? endif ?>
                                                <div style="display:inline-block;width: 124px;word-spacing:0px;vertical-align: top;padding-right: 9px;margin-bottom: 12px;position: relative;">
                                                    <div class="v120WideEntry" style="margin-bottom: 2px;">
                                                <div class="v120WrapperOuter">
                                                    <div class="v120WrapperInner"><a href="/watch?v=<?= $Video["url"] ?>"><img src="<?= $Thumbnail ?>" class="vimg120"></a><?php if (!in_array($Video["url"], $_USER->QuickList)): ?>
                <span class="video-actions" id="video-actions-<?= $Video['url'] ?>"><button type="button" class="yt-uix-button yt-uix-button-short yt-uix-button-arrowbutton" id="yt-uix-button-arrowbutton-<?= $Video['url'] ?>" onclick="openQueueMenu(this,'<?= $Video['url'] ?>');return false;"> <img class="yt-uix-button-arrow" src="/img/pixel.gif" alt=""></button></span>
                <div id="quicklist-inlist-<?= $Video['url'] ?>" class="hid quicklist-inlist"><a href="/my_quicklist">Added to<br>queue</a></div>
            <?php endif ?><div class="video-time">
                    <span id="video-run-time"><? if ($Video["length"] >= 60): ?><?=ltrim(date('i:s', $Video["length"]), 0)?> <? else: ?><?= gmdate('0:s', $Video["length"]) ?><? endif ?></span>
                    </div>
                                                    </div>
                                                </div>
                                            </div>
            <div class="vltitle">
                <div class="vlshortTitle" style="font-weight:bold">
                    <a href="<?= $Video["link"] ?>" title="<?= $Video["title"] ?>"><?= $Video["title"] ?></a> 
                </div>
            </div>

<div class="vlfacets" style="font-size: 11px;">
    <span id="video-num-views" style="color:#666;margin:0"><?= get_time_ago($Video["uploaded_on"]) ?></span><br>
                <span id="video-num-views" style="color:#666;margin:0"><? if ($LANGS['numberformat'] == 1): ?><?= number_format($Video["views"]) ?><? else: ?><?= ($Video["views"]) ?><? endif ?> <?= $LANGS['videoviews'] ?></span>
                <div><span class="vlfrom">          <a href="/user/<?=$Video["uploaded_by"]?>"><?= displayname($Video["uploaded_by"],0,34) ?></a>
                    <div class="star_rating"><? show_ratings($Video,"12px","12px") ?></div>
                    <div class="accept"><? if ($_USER->logged_in && $Owns_Group && $Video["accepted"] == 0) : ?><a href="/a/accept_video?url=<?= $Video["url"] ?>&id=<?= (int)$_GET["id"] ?>" style="font-size:11px"><?= $LANGS['accept'] ?></a><? endif ?></div>
</span></div>
                <div class="clearL"></div>
        </div><ul style="display:none;" class="yt-uix-button-menu yt-uix-button-menu-short" id="yt-uix-button-menu-<?= $Video['url'] ?>"><li id="add-to-quicklist-<?= $Video['url'] ?>"><span class=" yt-uix-button-menu-item" onclick="addToQuickList(this);return false;">Add to queue</span></li></ul>
                                                </div>
                                                <? if ($Count == 4 && ($Count2) != $Pages) : ?></div><? $Count2++ ?><? endif ?>
                                                <? $Count++ ?>
                                            <? endforeach ?>
                                            <? if ($Count2 == 4) : ?></div><? endif ?>
                                        <? else : ?>
                                            <div style="text-align:center; font-weight: bold; font-size: 14px;">
                                                <?= $LANGS['novideos'] ?>
                                            </div>
                                        <? endif ?>
                                    </td>
                                </tr>
                                </tbody></table>
                        </td>
                    </tr>
                    </tbody></table>
            </div>
    </div> <!-- end recentVideosDiv -->
<? endif ?>
        </div> <!-- end contentBox -->
    </div> <!-- end mainContent -->
