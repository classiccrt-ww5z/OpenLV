<script>
   function getCookie(name) {
   	var value = "; " + document.cookie;
   	var parts = value.split("; " + name + "=");
   	if (parts.length == 2) return parts.pop().split(";").shift();
   }
   
   function expandPlayer() {
   	var cook = getCookie("cp2");
   	cook = (cook == "" ? ["1","0"] : cook.split(","));
   	
   	$("#vtbl").toggleClass("expanded");
   	cook[1] = $("#vtbl").hasClass("expanded") ? "1" : "0";
   	
   	var CookieDate = new Date;
   	CookieDate.setFullYear(CookieDate.getFullYear() + 10);
   	document.cookie = 'cp2='+cook.join(",")+'; expires=' + CookieDate.toGMTString( ) + ';';
   	$(".vlPlayer").toggleClass("expanded");
   }
   
   var videoInfo = {
   	expand: expandPlayer,
   	complete: function() {
   		expandPlayer();
   		expandPlayer();
   	},
   	ended: function() {
   		<? if (isset($NextVideo) && isset($Playlist_Videos[$NextVideo])) : ?>
   			window.location = '/watch?v=<?= $Playlist_Videos[$NextVideo]["url"] ?>&pl=<?= urlencode($_GET["pl"]) ?>';
   		<? endif ?>
   	}
   };
</script>
<?php
   $c = isset($_COOKIE["aspect"])?(int)$_COOKIE["aspect"]:2;
   $A = $Aspect ?? "4:3";
   list($w,$hgt)=explode(":",$A);
   $r=$w/$hgt;
   
   if($c===0){
       $u=false;
   }elseif($c===1){
       $u=true;
   }else{
       $u = $r >= (16/9*0.95);
   }
   
   $h = $u?["n"=>338,"e"=>540]:["n"=>450,"e"=>720];
   ?>
<style>
   #vtbl_pl,
   #vtbl_actions,
   #vtbl_desc {
   display:inline-block;
   vertical-align:top;
   }
   #vtbl_pl,
   #vtbl_actions {
   width:640px;
   margin-right:20px;
   overflow:hidden;
   }
   #vtbl_pl {
   height:<?= $h["n"] ?>px;
   }
   #vtbl_desc {
   width:340px;
   float:right;
   }
   #vtbl.expanded #vtbl_pl {
   width:960px !important;
   height:<?= $h["e"] ?>px !important;
   margin-right:0;
   }
   #vtbl.expanded #vtbl_desc {
   margin-top:11px;
   }
</style>
<? if ($Status == 2) : ?>
<div class="w_title">
   <h1<? if ($Size == 1) : ?> style="width:auto"<? endif ?>><?= $Title ?></h1>
   <div class="wt_person">
      <?php if ($Has_Banner) { ?>
      <a style="font-weight:bold" href="/user/<?= $Uploader["displayname"] ?>">
      <img src="/usfi/wbner/<?= $Uploaded_By ?>.png" style="width:170px;height:auto;display:block;-webkit-user-drag:none;position:relative;top:8px;margin-top:-10px"></a>
      <?php } else { ?>
      <?= user_avatar2($Uploader["displayname"], 19, 19, $Uploader["avatar"], "", 'style="float:left;margin-right:5px;margin-bottom:5px"'); ?>
      <a style="font-weight:bold;position:relative;top:0px;" href="/user/<?= $Uploader["displayname"] ?>"><?= $Uploader["displayname"] ?></a>
      <?= ($Uploader["partner"] == 1) ? '<img style="width: 20px;vertical-align: middle;float:unset" title="This user is verified!" src="/img/verified.png">' : null ?>
      <?php } ?>
      <div style="clear:both;"></div>
   </div>
   <style>
      .yel_btn.sub_button {
      position: relative;
      left: 7px
      }
      <?php if(!$_USER->logged_in) { ?>
      #submeow .yel_btn:first-of-type {
      position: relative;
      left: 7px;
      }
      <?php } ?>
   </style>
   <span id="submeow" style="display:inline-block">
   <?php if (!$Is_Blocked && !$Has_Blocked) { ?>
   <?php if (!$_USER->Owns_Video) { ?>
   <?= subscribe_button2($Uploaded_By) ?>
   <?php } else { ?>
   <a href="/my_account" class="yel_btn sub_button" style="border-radius:5px !important">Edit Channel</a>
   <?php } ?>
   <?php } else { ?>
   <?= subscribe_button2($Uploaded_By, true) ?>
   <?php } ?>
   <a style=" padding: 4px 5px;
      border-bottom-left-radius: 0;
      border-top-left-radius: 0;
      background: #fff;
      border-color: #d3d3d3 #d3d3d3 #d3d3d3 #f4c800;
      color: gray!important;
      position: relative;
      z-index: 10" href="/user/<?= $Uploader["displayname"] ?>/subscribers" class="yel_btn"<?php if ($_USER->Owns_Video) { echo ' style="display:none"'; } ?>>
   <?= number_format($Uploader["subscribers"]) ?>
   </a></span>
   <? if ($Other_Videos) : ?>
   <div class="u_sct" style="margin:0 0 10px; display: inline-block;"><img src="/img/clp00.png" style="width:10px;height:10px;left:5px;">
      <span class="u_sct_hd" style="position:relative;left:5px;"><?= $Uploader["videos"] ?> Videos</span>
   </div>
   <div class="w_videos" style="margin-top:5px;display:none; overflow-x:auto; overflow-y:hidden; white-space:nowrap;">
      <div style="padding:7px 7px 5px">
         <? foreach ($Other_Videos as $Video) : ?>
         <div  id ="userv" style="display:inline-block; vertical-align:top; width:125px;margin-right:8px;">
            <div class="th">
               <div class="th_t"><?= $Video["length"] ?></div>
               <a href="/watch?v=<?= $Video["url"] ?>">
               <img class="vid_th" <?= $Video["thumbnail"] ?> width="121" height="78">
               </a>
            </div>
            <div style="width:120px;white-space:normal; word-break:break-word;">
               <a href="/watch?v=<?= $Video["url"] ?>" class="ln2"><?= $Video["title"] ?></a>
               <span class="vw s"><?= number_format($Video["views"]) ?> views</span><br>
               <? show_ratings($Video,14,14) ?>
            </div>
         </div>
         <? endforeach ?>
      </div>
   </div>
   <? endif ?>
</div>
<? endif ?>
<div id="vtbl">
   <noscript>
      <div id="noscript-player">
         <video id="noscript-player-video" src="/usfi/v/<?= $URL ?>.<?= $FILENAME ?><?= ($ISHD and $HD_Enabled) ? ".720" : "" ?>.mp4" controls autoplay></video>
         <? if($ISHD): ?>
         <form action="" method="GET" id="noscript-player-hd">
            <input type="hidden" name="v" value="<?= htmlspecialchars($_GET["v"] ?? '', ENT_QUOTES) ?>">
            <input type="hidden" name="hd" value="<?= (int) (!$HD_Enabled) ?>">
            <? if($HD_Enabled): ?>
            <input type="submit" value="Switch to SD" id="noscript-player-hd-button">
            <? else: ?>
            <input type="submit" value="Switch to HD" id="noscript-player-hd-button">
            <? endif; ?>
         </form>
         <? endif; ?>
      </div>
   </noscript>
   <div id="vtbl_pl" style="display: none">
      <? require_once $_SERVER['DOCUMENT_ROOT']."/_templates/_layout/player.php"; ?>
   </div>
   <? if ($Status == 2) : ?>
   <div id="vtbl_desc">
      <? if ($Size == 1) : ?><script>expandPlayer();</script><? endif ?>
      <? require_once("watch_widgets/Playlist.php"); ?>
      <? if ($Related_Videos && $_VIDEO->Info["s_related"] == 1) : ?>
      <h4 style=" font-size: 1.0833em;font-weight: normal;color: #666;margin-top: 0;margin-bottom: 5px;padding-bottom: 4px;border-bottom: 1px solid #ccc;">Suggestions</h4>
      <div class="w_videos" style="display:block;border:0;padding:0;">
         <div>
            <? foreach ($Related_Videos as $Video) : ?>
            <div>
               <div class="th">
                  <div class="th_t"><?= $Video["length"] ?></div>
                  <a href="/watch?v=<?= $Video["url"] ?>"><img class="vid_th" <?= $Video["thumbnail"] ?> width="121" height="78"></a>
               </div>
               <div style="margin-bottom:5px;">
                  <a href="/watch?v=<?= $Video["url"] ?>" class="ln2"><?= $Video["title"] ?></a>
                  <span class="vw s"><?= number_format($Video["views"]) ?> views</span><br>
                  <span class="s"><a style="font-weight:normal !important;" href="/user/<?= $Video["displayname"] ?>"><?= $Video["displayname"] ?></a></span><br>
                  <? show_ratings($Video,14,14) ?>
               </div>
            </div>
            <? endforeach ?>
         </div>
      </div>
      <? endif ?>
   </div>
   <div id="vtbl_actions">
      <script> vitag.videoConfig = { width: 640, height: 480, loadingText: "Loading advertisement..", complete: function () { }, error: function () { }, hidden: function () { } }; function loadAd() { (vitag.Init = window.vitag.Init || []).push(function () { viAPItag.startPreRoll("vi_2050787313"); }); } </script>
      <? if (isset($Single_Response)) : ?>
      <div style="    background: #ffc;border: 1px solid #fc3;font-weight: bold;padding: 5px; margin-top: 11px;">This video is a response to: <a href="/watch?v=<?= $Single_Response["url"] ?>"><?= $Single_Response["title"] ?></a></div>
      <? endif ?>
      <div class="w_actions">
         <span style="float:left;font-size:16px;font-weight:bold;padding:0;padding-left:6.5px" class="w_views" style="font-size:16px;font-weight:bold;margin-right:4px">Rate:</span>	
         <div id="rateYo"><span style="font-size:16px;font-weight:bold;margin-right:4px">Rate:</span></div>
         <div id="ratings"><? if ($_VIDEO->Info["s_ratings"] != 0 ) : ?><?= number_format($Total_Ratings) ?> rating<? if ($Total_Ratings !== 1) : ?>s<? endif ?><? else : ?>Disabled<? endif ?></div>
         <div class="w_views" style="font-size:16px;font-weight:normal"> <span style="font-size:16px;font-weight:bold;margin-right:4px">Views:</span><?= number_format($_VIDEO->Info["views"]) ?></div>
         <script>
            <? if ($_VIDEO->Info["s_ratings"] != 0) : ?>
            $(function () {
                $("#rateYo").rateYo({
                    ratedFill: "#<? if ($_VIDEO->Info["featured"] == 0) : ?>E74C3C<? else : ?>E3BF00<? endif ?>",
                    normalFill: "#c7c7c7",
                    fullStar: true,
                    starWidth: "19px"<? if (!$_USER->logged_in || $Is_Blocked || $Has_Blocked) : ?>,
                    <? if ($Total_Ratings > 0) : ?>
                    rating: <?= ($_VIDEO->Info["1_star"] + $_VIDEO->Info["2_star"] * 2 + $_VIDEO->Info["3_star"] * 3 + $_VIDEO->Info["4_star"] * 4 + $_VIDEO->Info["5_star"] * 5) / ($Total_Ratings); ?>,
                    <? else : ?>
                    rating: 0,
                    <? endif ?>
                    readOnly: true
                    <? elseif ($_USER->logged_in) : ?>,
                    <? if ($Total_Ratings > 0) : ?>
                    rating: <?= ($_VIDEO->Info["1_star"] + $_VIDEO->Info["2_star"] * 2 + $_VIDEO->Info["3_star"] * 3 + $_VIDEO->Info["4_star"] * 4 + $_VIDEO->Info["5_star"] * 5) / ($Total_Ratings); ?>,
                    onChange: function (rating, rateYoInstance) {
                        if (rating == 1) {
                            $("#ratings").html("Terrible...")
                        } else if (rating == 2) {
                            $("#ratings").html("Bad..")
                        } else if (rating == 3) {
                            $("#ratings").html("OK.")
                        } else if (rating == 4) {
                            $("#ratings").html("Good!")
                        } else if (rating == 5) {
                            $("#ratings").html("Very Good!")
                        }
                    }
                    <? else : ?>
                    rating: 0,
                    onChange: function (rating, rateYoInstance) {
                        if (rating == 1) {
                            $("#ratings").html("Terrible...")
                        } else if (rating == 2) {
                            $("#ratings").html("Bad..")
                        } else if (rating == 3) {
                            $("#ratings").html("OK.")
                        } else if (rating == 4) {
                            $("#ratings").html("Good!")
                        } else if (rating == 5) {
                            $("#ratings").html("Very Good!")
                        }
                    }
                    <? endif ?>
                    <? elseif ($_USER->logged_in && $Has_Rated_Video !== false) : ?>,
                    <? if ($Total_Ratings > 0) : ?>
                    rating: <?= ($_VIDEO->Info["1_star"] + $_VIDEO->Info["2_star"] * 2 + $_VIDEO->Info["3_star"] * 3 + $_VIDEO->Info["4_star"] * 4 + $_VIDEO->Info["5_star"] * 5) / ($Total_Ratings); ?>,
                    <? else : ?>
                    rating: 0,
                    <? endif ?>
                    readOnly: true
                    <? endif ?>
                });
            
                <? if ($_USER->logged_in && ($Has_Blocked || $Is_Blocked)) : ?>
                $("#rateYo").click(function() {
                    alert("You cannot interact with <?= $_VIDEO->Info["uploaded_by"] ?>!");
                });
                <? endif ?>
            
                <? if ($_USER->logged_in) : ?>
                $("#rateYo").rateYo()
                    .on("rateyo.set", function (e, data) {
                        var rating = data.rating;
                        rate_video("<?= $URL ?>",rating);
                        rated = true;
                    });
                rated = false;
            
                $("#rateYo").mouseleave(function () {
                    if (rated == false) {
                        $("#ratings").html("<?= number_format($Total_Ratings) ?> rating<? if ($Total_Ratings !== 1) : ?>s<? endif ?>");
                    }
                });
                <? elseif (!$_USER->logged_in) : ?>
                $("#rateYo").click(function() {
                    alert("You must be logged in to rate videos!");
                });
                <? elseif ($_USER->logged_in && $Has_Rated_Video !== false) : ?>
                $("#rateYo").mouseenter(function() {
                    $("#rateYo").rateYo("option", "rating", <?= $Has_Rated_Video ?>);
                });
                $("#rateYo").mouseleave(function() {
                    $("#rateYo").rateYo("option", "rating", <?= ($_VIDEO->Info["1_star"] + $_VIDEO->Info["2_star"] * 2 + $_VIDEO->Info["3_star"] * 3 + $_VIDEO->Info["4_star"] * 4 + $_VIDEO->Info["5_star"] * 5) / ($Total_Ratings); ?>);
                });
                <? endif ?>
            });
            <? else : ?>
            $(function () {
                $("#rateYo").rateYo({
                    ratedFill: "#E74C3C",
                    normalFill: "#c7c7c7",
                    fullStar: true,
                    starWidth: "19px",
                    rating: 0,
                    readOnly: true
                });
            });
            <? endif ?>
         </script>
      </div>
      <div class="w_actions">
         <div class="w_lnks">
            <a class="active" href="javascript:void(0)" onmouseenter="wn('w_sh')" onmouseleave="wl('w_sh')" onclick="wc('w_sh')"><img src="/img/shhd1.png" id="w_sh"><span style="top:2px">Share</span></a><a href="javascript:void(0)" onmouseenter="wn('w_fv')" onmouseleave="wl('w_fv')" onclick="wc('w_fv')"><img src="/img/hehd0.png" id="w_fv"><span>Favorite</span></a><a href="javascript:void(0)" onmouseenter="wn('w_pl')" onmouseleave="wl('w_pl')" onclick="wc('w_pl')"><img src="/img/plhd0.png" id="w_pl"><span>Playlists</span></a><a href="javascript:void(0)" onmouseenter="wn('w_fl')" onmouseleave="wl('w_fl')" onclick="wc('w_fl')"><img src="/img/flhd0.png" id="w_fl"><span>Flag</span></a>
         </div>
         <div id="w_l_cnts">
            <div id="w_sh_cnt">
                   <span><a href="https://www.facebook.com/sharer/sharer.php?u=https%3A//liivid.lol/watch?v=<?= $URL ?>" target="_blank" onclick="playerInstance.pause(true)"> <img src="https://facebook.com/favicon.ico" height="15" style="position:relative;top:2px;margin-right:5px;">Facebook</a></span><span><a href="https://x.com/home?status=I%20just%20watched%20this%20awesome%20video%3A%20https%3A//liivid.lol/watch?v=<?= $URL ?>" target="_blank" onclick="playerInstance.pause(true)"><img src="https://twitter.com/favicon.ico" height="15" style="position:relative;top:2px;margin-right:5px;">X/Twitter</a></span><span><a href="https://bsky.app/intent/compose?text=I%20just%20watched%20this%20awesome%20video%3A%20https%3A//liivid.lol/watch?v=<?= $URL ?>" target="_blank" onclick="playerInstance.pause(true)"><img src="https://web-cdn.bsky.app/static/favicon-16x16.png" height="15" style="position:relative;top:2px;margin-right:5px;">Bluesky</a></span><span><a href="https://www.reddit.com/submit?url=https://www.liivid.lol/watch?v=<?= $URL ?>&title=<?= $Title ?>" target="_blank" onclick="playerInstance.pause(true)"><img src="https://reddit.com/favicon.ico" height="15" style="position:relative;top:2px;margin-right:5px;">Reddit</a></span>
            </div>
            <div id="w_fv_cnt" class="hddn">
               <? if (!$_USER->logged_in) : ?>
               <?php require($_SERVER["DOCUMENT_ROOT"] . "/_templates/_pages/watch_widgets/Login.php"); ?>
               <? elseif (!$Has_Favorited) : ?>
               <div style="text-align:center" id="w_ff">
                  Are you sure you want to <strong>add</strong> this video to your favorites? <a href="javascript:void(0)" class="a_btn" onclick="add_video_favorite('<?= $URL ?>')">Yes!</a>
               </div>
               <? else : ?>
               <div style="text-align:center" id="w_ff">
                  Are you sure you want to <strong>remove</strong> this video from your favorites? <a href="javascript:void(0)" class="a_btn" onclick="add_video_favorite('<?= $URL ?>')">Yes!</a>
               </div>
               <? endif ?>
            </div>
            <div id="w_pl_cnt" class="hddn">
               <? if (!$_USER->logged_in) : ?>
                <?php require($_SERVER["DOCUMENT_ROOT"] . "/_templates/_pages/watch_widgets/Login.php"); ?>
               <? elseif (count($Playlists) > 0) : ?>
               <div style="text-align:center" id="wnn">
                  <select style="width:230px;margin:4px 0 0 0" id="watch_playlist">
                     <? foreach ($Playlists as $Playlist) : ?>
                     <option value="<?= $Playlist["purl"] ?>"><?= $Playlist["title"] ?></option>
                     <? endforeach ?>
                  </select>
                  <button type="button" class="search_button" onclick="add_to_playlist('<?= $URL ?>')" style="height:24px;position:relative;bottom:0.5px">Add to Playlist</button>
               </div>
               <? else : ?>
               <div style="text-align:center;line-height:17px">
                  <div style="font-weight:bold">You don't seem to have any Playlists.</div>
                  Do you want to <strong><a href="/my_playlists">create one</a>?</strong>
               </div>
               <? endif ?>
            </div>
            <div id="w_fl_cnt" class="hddn">
               <? if (!$_USER->logged_in) : ?>
                 <?php require($_SERVER["DOCUMENT_ROOT"] . "/_templates/_pages/watch_widgets/Login.php"); ?>
               <? else : ?>
               <div style="text-align:center" id="w_flag">
                  <select style="width:230px;margin:4px 0 0 0" id="flag_select">
                     <option value="1">Sexual Content</option>
                     <option value="2">Spam or Misleading</option>
                  </select>
                  <button type="button" class="search_button" style="height:24px;position:relative;bottom:0.5px" onclick="flag_video('<?= $URL ?>')">Flag this Video</button>
               </div>
               <? endif ?>
               <script async>setTimeout(function(){$.ajax({type:"POST",url:"/ajax/aw",data:{u:'<?= $_VIDEO->Info["url"] ?>',a:<? if (isset($_SERVER["HTTP_REFERER"])) : ?>'<?= $_SERVER["HTTP_REFERER"] ?>'<? else : ?>''<? endif ?>}})},1750);</script>
            </div>
         </div>
      </div>
      <div class="wt_des" style="<?php 
         if ($_USER->logged_in && $_USER->username == $Uploaded_By) { 
             echo 'margin-bottom:0;border-bottom-left-radius:0;border-bottom-right-radius:0'; 
         } 
         if ($Has_Banner) { 
             echo ';border-top-left-radius:0;border-top-right-radius:0'; 
         } 
         ?>">
         <div>
            <div class="wt_person">
               <?= get_date($_VIDEO->Info["uploaded_on"]) ?>
               (<a href="javascript:void(0)" id="show_more">more info</a>)
            </div>
            <div id="subsbtns">
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
                     <input type="text" id="em" onclick="$(this).select()" readonly value='<iframe allowfullscreen src="http://www.liivid.lol/embed?v=<?= $URL ?>&a=0" frameborder="0" width="640" height="360"></iframe>'>
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
         <button class="search_button" style="float:left;width:48%" onclick="feature_video('<?= $URL ?>')" id="feature_video">Feature Video</button>
         <?php } else { ?>
         <button class="search_button" style="float:left;width:48%" onclick="feature_video('<?= $URL ?>')" id="feature_video">Remove Feature</button>
         <?php } ?>
         <button class="search_button" style="float:right;width:48%" id="shadowban" onclick="shadowban('<?= $Uploaded_By ?>')">
         <?php if ($Uploader["shadowbanned"]) { echo "Undo Shadowban"; } else { echo "Shadowban Uploader"; } ?>
         </button>
         <div style="clear:both"></div>
         <div style="overflow:hidden;margin-top:10px">
            <a href="/admin/videos?v=<?= $URL ?>">
            <button class="search_button" style="width:48%;float:left" type="button">Edit Video</button>
            </a>
            <a href="/analytics?page=videos&video=<?= $URL ?>">
            <button class="search_button" style="width:48%;float:right" type="button">Analytics</button>
            </a>
         </div>
      </div>
      <?php } ?>
      <script> vitag.outStreamConfig = { type: "slider", position: "right" }; </script>
      <div class="w_btm">
         <div style="display:table;width:100%">
            <div class="w_big_btn big_sel" id="w_com">
               <a href="javascript:void(0)">Commentary</a>
            </div>
            <div class="w_big_btn" id="w_stats">
               <a href="javascript:void(0)">Statistics</a>
            </div>
         </div>
         <div class="cl"></div>
         <div id="w_com_sct">
            <div class="u_sct">
               <? if ($_VIDEO->Info["responses"] > 0) : ?>
               <img src="/img/clp11.png">
               <span class="u_sct_hd">Video Responses <span>(<span><?= number_format($_VIDEO->Info["responses"]) ?></span>)</span></span>
               <? else : ?>
               <img src="/img/clp00.png">
               <span class="u_sct_hd">Video Responses <span>(<span>0</span>)</span></span>
               <? endif ?>
               <? if ($_VIDEO->Info["s_responses"] != 0) : ?>
               <? if (!$_USER->logged_in) : ?><a href="/login" onclick="event.stopPropagation()">Sign in to make a video response</a><? else : ?><a href="/post_response?v=<?= $URL ?>" onclick="event.stopPropagation();">Submit a video response</a><? endif ?>
               <? endif ?>
            </div>
            <div style="<? if ($_VIDEO->Info["responses"] > 0) : ?>display:block;margin-bottom:8px<? else : ?>display:none;margin-bottom:20px<? endif ?>">
               <? if ($_VIDEO->Info["responses"] > 0) : ?>
               <? foreach ($Responses as $Response) : ?>
               <div class="v_resp">
                  <?= video_thumbnail($Response["url_response"],$Response["length"],111,75) ?>
                  <a href="/watch?v=<?= $Response["url_response"] ?>"><?= limit_text($Response["title"],50) ?></a>
               </div>
               <? endforeach ?>
               <div class="cl"></div>
               <? else : ?>
               <div style="text-align:center;margin-top:22px">This video doesn't have any video responses!</div>
               <? endif ?>
            </div>
            <div class="u_sct">
               <img src="/img/clp11.png">
               <span class="u_sct_hd">Text Comments <span>(<span id="cmt_num"><?= $Comments_Num ?></span>)</span></span>
               <? if (!$_USER->logged_in) : ?><a href="/login" onclick="event.stopPropagation()">Sign in to post a comment</a><? else : ?><a href="javascript:void(0)" onclick="$('#comment_textarea').focus();event.stopPropagation();">Write Comment</a><? endif ?>
            </div>
            <div style="display:block; word-wrap: break-word">
               <? if ($Top_Comments !== false && (!isset($_GET["p"]) || $_GET["p"] == 1)) : ?>
               <div id="top_comments">
                  <? foreach ($Top_Comments as $Comment) : ?>
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
                  <div class="wt_c_sct" id="wt_<?= $Comment["id"] ?>" data-editing="0">
                     <div style="background:#cfffc4">
                        <a href="/user/<?= $Comment["displayname"] ?>"><?= $Comment["displayname"] ?></a> <span>(<?= get_time_ago($Comment["date_sent"]) ?>)</span>
                     </div>
                     <div>
                        <?= user_avatar2($Comment["displayname"],41,41,$Comment["avatar"],"wp_avt") ?>
                        <div>
                           <span<? if ($Comment["rating"] < 0) : ?> style="color:red"<? elseif ($Comment["rating"] > 0) : ?> style="color:green"<? endif ?>><?= $Comment["rating"] ?></span>
                           <? if (!$_USER->logged_in) : ?><img src="/img/td0.png" onclick="alert('Please sign in to rate this comment')"><img src="/img/tu0.png" onclick="alert('Please sign in to rate this comment')"><? else : ?><img <? if ($Rated == "-1") : ?>src="/img/td1.png" style="opacity:0.75" <? else : ?>src="/img/td0.png" onclick="wr(<?= $Comment["id"] ?>,'0',this)"<? endif ?>><img <? if ($Rated == "1") : ?> src="/img/tu1.png" style="opacity:0.75" <? else : ?>src="/img/tu0.png" onclick="wr(<?= $Comment["id"] ?>,'1',this)"<? endif ?>><? endif ?>
                        </div>
                        <div>
                           <?= showBBcodes(hashtag_search(mention(nl2br($Comment["comment"])))) ?>
                        </div>
                     </div>
                  </div>
                  <? endforeach ?>
               </div>
               <? endif ?>
               <div id="video_comments_section">
                  <? if ($_VIDEO->Info["comments"] > 0) : ?>
                  <? foreach ($Comments_Array as $Comment) : ?>
                  <? $OP_ID = $Comment["id"] ?>
                  <? $OP_USER = $Comment["by_user"] ?>
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
                  <div class="wt_c_sct<? if ($Comment["rating"] < -4) : ?> op_c<? endif ?>" id="wt_<?= $Comment["id"] ?>" data-editing="0">
                     <div <? if ($Uploaded_By == $Comment["by_user"]) : ?> style="background:#fffcc2"<? elseif ("copy2123" == $Comment["by_user"] || "LiiVid" == $Comment["by_user"]) : ?> style="background:#d2ebff"<? endif ?>>
                        <a href="/user/<?= $Comment["displayname"] ?>"><?= $Comment["displayname"] ?></a> <span>(<?= get_time_ago($Comment["date_sent"]) ?>)</span>
                        <div>
                           <? if ($_USER->logged_in && !$Has_Blocked && !$Is_Blocked) : ?><a href="javascript:void(0)" onclick="show_reply(<?= $Comment["id"] ?>,<? if ($_USER->logged_in && $Uploaded_By == $_USER->username) : ?>true<? else : ?>false<? endif ?>,'<?=$Comment["displayname"]?>')">Reply</a><? endif ?><?php if($_USER->logged_in && $Comment["by_user"] == $_USER->username) { ?><a href="javascript:void(0)" onclick="toggleEditComment(<?= $Comment["id"] ?>)" style="padding-left:9px;margin-left:9px;border-left:1px solid #7d7d7d">Edit</a><?php } ?><? if ($_USER->logged_in && ($Uploaded_By == $_USER->username || $Comment["by_user"] == $_USER->username || $_USER->Is_Admin || $_USER->Is_Mod)) : ?><a href="javascript:void(0)" onclick="delete_wtc(<?= $Comment["id"] ?>)" style="padding-left:9px;margin-left:9px;border-left:1px solid #7d7d7d">Delete</a><? endif ?>
                        </div>
                     </div>
                     <div>
                        <?= user_avatar2($Comment["displayname"],41,41,$Comment["avatar"],"wp_avt") ?>
                        <div>
                           <span<? if ($Comment["rating"] < 0) : ?> style="color:red"<? elseif ($Comment["rating"] > 0) : ?> style="color:green"<? endif ?>><?= $Comment["rating"] ?></span>
                           <? if (!$_USER->logged_in) : ?><img src="/img/td0.png" onclick="alert('Please sign in to rate this comment')"><img src="/img/tu0.png" onclick="alert('Please sign in to rate this comment')"><? else : ?><img <? if ($Rated == "-1") : ?>src="/img/td1.png" style="opacity:0.75" <? else : ?>src="/img/td0.png" onclick="wr(<?= $Comment["id"] ?>,'0',this)"<? endif ?>><img <? if ($Rated == "1") : ?> src="/img/tu1.png" style="opacity:0.75" <? else : ?>src="/img/tu0.png" onclick="wr(<?= $Comment["id"] ?>,'1',this)"<? endif ?>><? endif ?>
                        </div>
                        <div id="comment-<?= $Comment["id"] ?>-content" style="display: block;">
                           <?= showBBcodes(hashtag_search(mention(nl2br($Comment["comment"])))) ?>
                        </div>
                        <div class="comment-edit-container" id="comment-<?= $Comment["id"] ?>-edit-container" style="display: none;">
                           <textarea id="comment-<?= $Comment["id"] ?>-edit-input" class="comment-textarea comment-edit-input" cols="52" rows="5" maxlength="1000"></textarea>
                           <div><button class="search_button save-comment-edit-button" onclick="saveCommentEdit(<?= $Comment["id"] ?>)">Save</button><button class="search_button" onclick="toggleEditComment(<?= $Comment["id"] ?>)">Cancel</button></div>
                        </div>
                     </div>
                  </div>
                  <?
                     if ($Comment["has_replies"]) {
                     	$Replies = $DB->execute("SELECT video_comments.*, users.avatar, users.displayname FROM video_comments INNER JOIN users ON video_comments.by_user = users.username WHERE video_comments.reply_to = :ID ORDER BY date_sent DESC LIMIT 2", false, [":ID" => $Comment["id"]]);
                     	if ($DB->RowNum > 0) {
                     		if ($DB->RowNum == 2) {
                     			$Show_More = $DB->execute("SELECT id FROM video_comments WHERE reply_to = ".$Comment["id"]);
                     			$Show_More = $DB->RowNum;
                     		} else {
                     			$Show_More = false;
                     		}
                     		$Replies = array_reverse($Replies);
                     	} else {
                     		$Replies = false;
                     	}
                     } else {
                     	$Replies = false;
                     }
                     ?>
                  <? if ($Replies !== false) : ?>
                  <? if ($Show_More !== false && $Show_More > 2) : ?>
                  <a href="javascript:void(0)" class="show_more" onclick="show_all_replies(<?= $OP_ID ?>)" id="sa_<?= $OP_ID ?>">Show all <?= $Show_More ?> replies</a>
                  <? endif ?>
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
                  <div class="wt_c_sct wt_r_sct<? if ($Comment["rating"] < -4) : ?> op_c<? endif ?>" id="wt_<?= $Comment["id"] ?>" op="<?= $OP_ID ?>" data-op-user="<?= $Comment["by_user"] ?>" data-editing="0">
                     <div <? if ($Uploaded_By == $Comment["by_user"]) : ?> style="background:#fffcc2"<? elseif ("copy2123" == $Comment["by_user"] || "LiiVid" == $Comment["by_user"]) : ?> style="background:#d2ebff"<? endif ?>>
                        <a href="/user/<?= $Comment["displayname"] ?>"><?= $Comment["displayname"] ?></a> <span>(<?= get_time_ago($Comment["date_sent"]) ?>)</span>
                        <div>
                           <? if ($_USER->logged_in && !$Has_Blocked && !$Is_Blocked) : ?><a href="javascript:void(0)" onclick="show_reply(<?= $OP_ID ?>,<? if ($_USER->logged_in && $Uploaded_By == $_USER->username) : ?>true<? else : ?>false<? endif ?>,'<?=$Comment["displayname"]?>')">Reply</a><? endif ?><?php if($_USER->logged_in && $Comment["by_user"] == $_USER->username) { ?><a href="javascript:void(0)" onclick="toggleEditComment(<?= $Comment["id"] ?>)" style="padding-left:9px;margin-left:9px;border-left:1px solid #7d7d7d">Edit</a><?php } ?><? if ($_USER->logged_in && ($Uploaded_By == $_USER->username || $Comment["by_user"] == $_USER->username || $_USER->Is_Admin || $_USER->Is_Mod)) : ?><a href="javascript:void(0)" onclick="delete_wtc(<?= $Comment["id"] ?>)" style="padding-left:9px;margin-left:9px;border-left:1px solid #7d7d7d">Delete</a><? endif ?>
                        </div>
                     </div>
                     <div>
                        <?= user_avatar2($Comment["displayname"],41,41,$Comment["avatar"],"wp_avt") ?>
                        <div>
                           <span<? if ($Comment["rating"] < 0) : ?> style="color:red"<? elseif ($Comment["rating"] > 0) : ?> style="color:green"<? endif ?>><?= $Comment["rating"] ?></span>
                           <? if (!$_USER->logged_in) : ?><img src="/img/td0.png" onclick="alert('Please sign in to rate this comment')"><img src="/img/tu0.png" onclick="alert('Please sign in to rate this comment')"><? else : ?><img <? if ($Rated == "-1") : ?>src="/img/td1.png" style="opacity:0.75" <? else : ?>src="/img/td0.png" onclick="wr(<?= $Comment["id"] ?>,'0',this)"<? endif ?>><img <? if ($Rated == "1") : ?> src="/img/tu1.png" style="opacity:0.75" <? else : ?>src="/img/tu0.png" onclick="wr(<?= $Comment["id"] ?>,'1',this)"<? endif ?>><? endif ?>
                        </div>
                        <div id="comment-<?= $Comment["id"] ?>-content" style="width:442px;display: block;">
                           <?= showBBcodes(hashtag_search(mention(nl2br($Comment["comment"])))) ?>
                        </div>
                        <div class="comment-edit-container" id="comment-<?= $Comment["id"] ?>-edit-container" style="display: none;width: calc(100% - 50px)">
                           <textarea id="comment-<?= $Comment["id"] ?>-edit-input" class="comment-textarea comment-edit-input" cols="52" rows="5" maxlength="1000"></textarea>
                           <div><button class="search_button save-comment-edit-button" onclick="saveCommentEdit(<?= $Comment["id"] ?>)">Save</button><button class="search_button" onclick="toggleEditComment(<?= $Comment["id"] ?>)">Cancel</button></div>
                        </div>
                     </div>
                  </div>
                  <? endforeach ?>
                  <? endif ?>
                  <? if ($_USER->logged_in) : ?>
                  <div id="r_cmt_<?= $OP_ID ?>"></div>
                  <? endif ?>
                  <? endforeach ?>
                  <? else : ?>
                  <div id="no_video_comments">This video has no comments yet!</div>
                  <? endif ?>
               </div>
               <? if ($_VIDEO->Info["comments"] > 15) : ?>
               <div class="w_pag">
                  <?= $_PAGINATION->new_show($_VIDEO->Info["comments"],"v=$URL") ?>
               </div>
               <? endif ?>
               <? if ($_USER->logged_in && $_USER->Is_Activated && !$Is_Blocked && !$Has_Blocked && ($_VIDEO->Info["s_comments"] == 1 || ($_VIDEO->Info["s_comments"] == 2 && $_USER->logged_in && $Is_Friends) || ($_USER->logged_in && $_USER->username == $_VIDEO->Info["uploaded_by"]))) : ?>
               <? if (!isset($_GET["p"]) || $_GET["p"] == 1) : ?>
               <div style="margin: 13px 0 5px">
                  <div style="font-weight:bold;font-size:16px;margin:0 0 4px">Post a Comment:</div>
                  <textarea id="comment_textarea" style="height:80px" onkeyup="textCounter(this,'counter',1000);" rows="5" maxlength="1000" placeholder="Your Comment..."></textarea>
                  <div>
                     <button type="button" class="search_button" id="video_button" onclick="post_video_comment('<?= $URL ?>'<? if ($Uploaded_By == $_USER->username) : ?>,true<? else : ?>,false<? endif ?>)">Post Comment</button><span class="tiny_text"> <span id="counter">0</span> / 1000</span>
                  </div>
               </div>
               <? endif ?>
               <? elseif (!$_USER->logged_in) : ?>
               <div style="text-align:center;margin: 13px 0 5px">
                  <a href="/register">Sign up</a> for a free account, or <a href="/login">sign in</a> to post a comment.
               </div>
               <? elseif (!$_USER->Is_Activated) : ?>
               <div style="text-align:center;margin: 13px 0 5px">
                  You must activate your account before you can post a comment.
               </div>
               <? elseif ($Is_Blocked || $Has_Blocked) : ?>
               <div style="text-align:center;margin: 13px 0 5px">
                  You cannot interact with this user.
               </div>
               <? else : ?>
               <div style="text-align:center;margin: 13px 0 5px">
                  <? if ($_VIDEO->Info["s_comments"] == 2) : ?>
                  You must be friends with the uploader to post a comment.
                  <? else : ?>
                  Comments under this video have been disabled.
                  <? endif ?>
               </div>
               <? endif ?>
            </div>
         </div>
         <div id="w_stats_sct" class="hddn">
            <table style="width:88%;margin:0 0 0 61px" cellpadding="5">
               <tr>
                  <td>
                     Date: 
                     <strong>
                        <date><?= date("M d, Y",strtotime($_VIDEO->Info["uploaded_on"])) ?></date>
                     </strong>
                  </td>
                  <td>Views: <strong><?= $Views ?></strong></td>
                  <td>Ratings: <strong><?= number_format($Total_Ratings) ?></strong></td>
               </tr>
               <tr>
                  <td>Time: <strong><time><?= date("h:i A",strtotime($_VIDEO->Info["uploaded_on"])) ?></time></strong></td>
                  <td>Comments: <strong><?= $Comments_Num ?></strong></td>
                  <td>Favorites: <strong><?= $_VIDEO->Info["favorites"] ?></strong></td>
               </tr>
            </table>
         </div>
      </div>
      <div style="width:468px;margin:8px auto">
      </div>
   </div>
   <? endif ?>
</div>