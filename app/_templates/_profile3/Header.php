<?php require_once $_SERVER["DOCUMENT_ROOT"] . "/_templates/_layout/header.php"; ?>
<div id="gbg">
    <? if ($Is_OWNER) : ?>
    <div class="cosmic_edit hddn">
        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" enctype="multipart/form-data">
        <div style="border-bottom:1px solid #bcbcbc;position:relative">
            <div style="padding:12px;font-size:18px;color:#555">
                Editing Channel Branding
            </div>
            <div style="position:absolute;right:6px;top:6px">
                <button type="button" class="cosmic_button" onclick="$('.cosmic_edit').toggleClass('hddn')">Cancel</button>
                <input type="submit" class="cosmic_button" value="Save" name="save_channel_branding">
            </div>
        </div>
        <div style="padding:12px;color:#666;overflow:hidden">
            <div style="float:left;width:47%;overflow:hidden;padding-right:1%;margin-right:1%;border-right:1px solid #bcbcbc">
                <div style="font-weight:bold;color:#555;margin-bottom:6px">Channel Background</div>
                <div style="float:left">
                    <div class="cosmic_bg_change">
                        <div style="display:inline-block;vertical-align: middle;color:white;font-size:12px;margin-right:2px">Color:</div>
                    <button class="jscolor {valueElement:null,value:'<?= $Profile["bg"] ?>',onFineChange:'cosmic_bg(this)'}" style="cursor:pointer;width:87px;outline:0;vertical-align:middle;display:inline-block;height:50px;padding:27px;border:0;padding:0;border-radius:0"></button>
                    <input type="hidden" name="bg_color" value="#<?= $Profile["bg"] ?>" id="bg_color">
                    </div>
                </div>
                <div style="float:left;font-size:12px;position:relative;left:25px">
                    <table cellpadding="5">
                        <tr>
                            <td>Background Image</td>
                            <td><input type="file" name="bg_upload" id="bg_upload"<? if ($Has_Background) : ?> style="width:175px;display: none"<? else : ?> style="width:178px"<? endif ?>><button id="bg_delete" onclick="delete_background_new()" type="button"<? if (!$Has_Background) : ?> style="display: none"<? endif ?>>Delete Background</button></td>
                        </tr>
                        <tr>
                            <td>Fixed</td>
                            <td><input name="bg_fixed" type="checkbox"<? if ($Profile["bg_fixed"]) : ?> checked<? endif ?>></td>
                        </tr>
                        <tr>
                            <td>Repeat</td>
                            <td>
                                <select name="bg_repeat" style="padding: 1px 4px;">
                                    <option value="1"<? if ($Profile["bg_repeat"] == 1) : ?> selected<? endif ?>>No Repeat</option>
                                    <option value="2"<? if ($Profile["bg_repeat"] == 2) : ?> selected<? endif ?>>Repeat</option>
                                    <option value="3"<? if ($Profile["bg_repeat"] == 3) : ?> selected<? endif ?>>Repeat X</option>
                                    <option value="4"<? if ($Profile["bg_repeat"] == 4) : ?> selected<? endif ?>>Repeat Y</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Position</td>
                            <td>
                                <select name="bg_position" style="padding: 1px 4px;">
                                    <option value="1"<? if ($Profile["bg_position"] == 1) : ?> selected<? endif ?>>Top</option>
                                    <option value="2"<? if ($Profile["bg_position"] == 2) : ?> selected<? endif ?>>Middle</option>
                                    <option value="3"<? if ($Profile["bg_position"] == 3) : ?> selected<? endif ?>>Bottom</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style="float:left;width:25%;border-right:1px solid #bcbcbc;padding-right: 1%;margin-right:1%;overflow:hidden;height:152px">
                <div style="font-weight:bold;color:#555;margin-bottom:18px">Channel Settings</div>
                <div style="float:left">
                    <div>
                        <div>Feed</div>
                        <table cellpadding="3">
                            <tr>
                                <td><input type="checkbox" name="c_recent" id="csm_edit_act"<? if ($Profile["c_recent"]) : ?> checked<? endif ?>></td>
                                <td><label for="csm_edit_act">Activity</label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="c_comments" id="csm_edit_cmt"<? if ($Profile["c_comments"]) : ?> checked<? endif ?>></td>
                                <td><label for="csm_edit_cmt">Comments</label></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div style="float:left;margin-left:10px">
                    <div>
                        <div>Content</div>
                        <table cellpadding="3">
                            <tr>
                                <td><input type="checkbox" name="c_videos" id="csm_edit_vid"<? if ($Profile["c_videos"]) : ?> checked<? endif ?>></td>
                                <td><label for="csm_edit_vid">Videos</label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="c_favorites" id="csm_edit_fav"<? if ($Profile["c_favorites"]) : ?> checked<? endif ?>></td>
                                <td><label for="csm_edit_fav">Favorites</label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="c_playlists" id="csm_edit_ply"<? if ($Profile["c_playlists"]) : ?> checked<? endif ?>></td>
                                <td><label for="csm_edit_ply">Playlists</label></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div style="float:left;width:23%;overflow:hidden">
                <div style="font-weight:bold;color:#555;margin-bottom:18px">Channel Info</div>
                <div>Channel Title</div>
                <input type="text" style="width:213px" name="ch_title" placeholder="Your Channel Title..." maxlength="80" value="<?= $Profile["channel_title"] ?>">
                <div style="margin-top:31px">Channel Tags</div>
                <input type="text" style="width:213px" name="ch_tags" placeholder="Relevant Channel Tags..." maxlength="256" value="<?= $Profile["channel_tags"] ?>">
            </div>
        </div>
        </form>
    </div>
    <? endif ?>
    <? if ($Banner_Links !== false && $Profile["banned"] == 0) : ?>
        <div style="width:1000px;margin:0 auto"><? require_once $_SERVER['DOCUMENT_ROOT']."/_templates/_layout/banner.php"; ?></div>
    <? endif ?>
<div class="cosmic_top">
    <?= user_avatar2($Profile["displayname"],62,62,$Profile["avatar"]) ?>
    <div>
        <? if (empty($Profile["channel_title"])) { echo $Profile["displayname"]; } else { echo $Profile["channel_title"]; } ?>  <?= ($Profile["partner"] == 1) ? '<img style="width: 22px;vertical-align: middle;" title="This user is verified!" src="/img/verified.png">' : null ?>
    </div>
    <div class="cosmic_sub<? if ($Is_Subscribed) : ?> cosmic_subbed<? endif ?>" onclick="<? if ($_USER->logged_in) : ?><? if (!$Is_OWNER) : ?><? if ($_USER->Is_Activated) : ?>cosmic_subscribe('<?= $Profile["username"] ?>')<? else : ?>alert('Your account must be activated to subscribe to channels!')<? endif ?><? else : ?>alert('You cannot subscribe to your own channel!')<? endif ?><? else : ?>alert('You must be logged in to subscribe to channels!')<? endif ?>"><? if (!$Is_Subscribed) : ?><div id="sub-icon"></div>Subscribe<? else : ?><div id="sub-icon"></div>Unsubscribe<? endif ?></div>
    <div class="cosmic_stats"<? if ($Is_OWNER) : ?>style="padding-right:108px"<? endif ?>>
        <div>
            <span><?= number_format($Profile["subscribers"]) ?></span>
            <span>subscribers</span>
        </div>
        <div>
            <span><?= number_format($Profile["video_views"]) ?></span>
            <span>video views</span>
        </div>
        <? if ($Is_OWNER) : ?>
            <button class="cosmic_button" onclick="$('.cosmic_edit').toggleClass('hddn')" style="position: absolute;right:0;top:11px">Edit Channel</button>
        <? endif ?>
    </div>
</div>
<div class="cosmic_nav">
    <? if (($Profile["c_videos"] && $Profile["videos"] > 0) || ($Profile["c_favorites"] && $Profile["favorites"] > 0) || ($_GET["page"] == "playlist") || ($Profile["c_playlists"]) || isset($Nothing_To_Show)) : ?><a href="/user/<?= $Profile["displayname"] ?>" <? if ($Page_File == "Main" || $Page_File == "Playlist") : ?>id="selectedNav"<? endif ?>>Featured</a><? endif ?><? if ($Profile["c_recent"] || $Profile["c_comments"]) : ?><a <? if ($Page_File == "Feed") : ?>id="selectedNav"<? endif ?> href="/user/<?= $Profile["displayname"] ?>/<? if ($Profile["c_recent"]) : ?>feed<? else : ?>comments<? endif ?>"><? if ($Profile["c_recent"]) : ?>Feed<? else : ?>Channel Comments<? endif ?></a><? endif ?><? if (($Profile["c_videos"] && $Profile["videos"] > 0) || ($Profile["c_favorites"] && $Profile["favorites"] > 0) || ($Profile["c_playlists"]) && $Playlist_Amount > 0) : ?><a href="/user/<?= $Profile["displayname"] ?>/<? if ($Profile["c_videos"] && $Profile["videos"] > 0) : ?>videos<? elseif ($Profile["c_favorites"] && $Profile["favorites"] > 0) : ?>favorites<? elseif ($Profile["c_playlists"] && $Playlist_Amount > 0) : ?>playlists<? endif ?>" <? if ($Page_File == "Videos") : ?>id="selectedNav"<? endif ?>><? if ($Profile["c_videos"] && $Profile["videos"] > 0) : ?>Videos<? elseif ($Profile["c_favorites"] && $Profile["favorites"] > 0) : ?>Favorites<? elseif ($Profile["c_playlists"] && $Playlist_Amount > 0) : ?>Playlists<? endif ?></a><? endif ?>
    <? if ($Profile["c_videos"] && $Profile["videos"] > 0) : ?>
    <form action="/user/<?= $Profile["displayname"] ?>/videos" method="POST">
        <div class="cosmic_search">
            <input type="search" name="q" maxlength="64" placeholder="Search Channel"<? if (isset($_POST["q"])) : ?> value="<?= htmlspecialchars($_POST["q"], ENT_QUOTES) ?>"<? endif ?>><button type="submit"><b>Search</b></button>
        </div>
    </form>
    <? endif ?>
</div>
