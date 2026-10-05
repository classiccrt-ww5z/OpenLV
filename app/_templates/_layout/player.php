<?php
	if (isset($_COOKIE["player"])) {
		$Player = (int)$_COOKIE["player"];
		if ($Player < 0 || $Player > 4) $Player = 2;
	} else {
		$Player = 2;
	}
	if (isset($_COOKIE["cp"]))
		$custom = explode(",", $_COOKIE["cp"]);

	if (isset($_GET["p"]) && $_GET["p"] > 1) {
	    $Autoplay = 0;
    }
	
	$prev = file_exists($_SERVER['DOCUMENT_ROOT']."/usfi/prvw/$URL.jpg");
?>

<script id="heightAdjust">
	if (!window.videoInfo)
		var videoInfo = {};

	function adjustHeight(n) {
		var height;
		var par = $("#heightAdjust").parent();
		if (par[0].style.height) {
			height = par.height();
			par.height(height+n);
		}
	}
	
	// Easier way of setting cookies
	function setCookie(name, value) {
		var CookieDate = new Date;
		CookieDate.setFullYear(CookieDate.getFullYear() + 10);
		document.cookie = name+'='+value+'; expires=' + CookieDate.toGMTString( ) + '; path=/';
	}

	// Easier way of getting cookies
	function getCookie(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i <ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}
	
	function getTimeHash() {
		var h = 0;
		var st = 0;
		
		if ((h = window.location.href.indexOf("#t=")) >= 0) {
			st = window.location.href.substr(h+3);
			return parseInt(st);
		}
		
		return 0;
	}
	
	var vlpColors = <?= json_encode(isset($_COOKIE["vlpColors"]) ? $_COOKIE["vlpColors"] : "teal,white") ?>;
	vlpColors = vlpColors.split(",");
	
	<? switch($Player) {
		case 3:
			$PlayerSkin = "2012";
			break;
		case 2:
			$PlayerSkin = "2007HD";
			break;
			case 4:
			$PlayerSkin = "2010";
			break;
		default:
			$PlayerSkin = "2009HD";
	} ?>

	<? if ($Status == 2) : ?>
	var viValues = {
		variable: "vlp",
		src: "/usfi/v/<?= $URL ?>.<?= $FILENAME ?>.mp4",
		<?php if($ISHD) { ?>
		hdsrc: "/usfi/v/<?= $URL ?>.<?= $FILENAME ?>.720.mp4",
		<?php } ?>
		img: "/usfi/<?= $prev ? "prvw" : "thmp" ?>/<?= $URL ?>.jpg",
		url: "<?= $URL ?>",
		duration: <?= $Length ?>,
		autoplay: <?= (!empty($Autoplay) && $Autoplay == 1) ? "true" : "false" ?>,
		skin: "<?= $PlayerSkin ?>",
		btcolor: vlpColors[0],
		bgcolor: vlpColors[1],
		adjust: true,
		start: getTimeHash()
	};
	
	for (var i in viValues) {
		if (videoInfo[i] === void(0)) {
			videoInfo[i] = viValues[i];
		}
	}
	<? endif ?>
</script>
<? if ($Status == 2) : ?>
	<? if ($Player != 1) { ?>
<?php if($PlayerSkin == 2010) {
    
    if(empty($Has_Favorited
    )) {
$Has_Favorited = favorited_by2($URL, $_USER->username);
}
?>
<style>

    #vtbl_pl {
        height: calc(<?= $h["n"] ?? "338" ?>px + 30px) !important; 
    }

    #vtbl.expanded #vtbl_pl {
        height: calc(<?= $h["e"] ?? "540" ?>px + 30px) !important; 
    } 
    
.pr_r > div:first-child {
        height: calc(<?= $h["n"] ?? "338" ?>px + 30px) !important;
    }
.pr_tp_pl > div:first-child
{
        height: calc(<?= $h["n"] ?? "360" ?>px + 30px) !important;
    }
    
    .mv_wr >div:first-child {
                height: calc(<?= $h["n"] ?? "241" ?>px + 30px) !important;

    }
</style>

<?php } ?>






<?php if($PlayerSkin == 2010) { ?>
	            <script type="text/javascript">function v_play(){player.ended||player.paused?(player.play(),document.getElementById("left").style.backgroundImage="url('/img/ply0.png')"):(player.pause(),document.getElementById("left").style.backgroundImage="url('/img/ply1.png')")}function v_mute(){player.muted?(document.getElementById("right").style.backgroundImage="url('/img/vol1.png')",player.muted=!1):(document.getElementById("right").style.backgroundImage="url('/img/vol0.png')",player.muted=!0)}</script>
	            <?php } ?>
		<!-- LiiVid HTML5/Flash Player -->
		<div class="vlPlayer">
		     		<script>
		     		<?php if($PlayerSkin == 2010) { ?>
 /* 2010 PLAYER BY BITVIEW (bitview.net) */
		     var liked=	<?php if($Has_Favorited) { echo 'true'; } else { echo "false"; } ?>;
			   var video_url = '<?= $URL ?>';
                            var annotations = false;
                            /* Languages for tooltips */
                            var fullscreen_text = "Full screen";
                            var expand_text = "Expand";
                            var shrink_text = "Shrink";
                            var popout_text = "Pop out";
                            var mute_text = "Mute";
                            var unmute_text = "Unmute";
                            <?php } ?>
                   
				window[videoInfo.variable] = new VLPlayer({
    id: videoInfo.id,
    src: videoInfo.src,
    hdsrc: <?= $ISHD ? "videoInfo.hdsrc" : "null" ?>,
    preview: videoInfo.img,
    videoUrl: "/watch?v="+videoInfo.url,
    duration: videoInfo.duration,
    autoplay: videoInfo.autoplay,
    skin: "/vlPlayer/skins/"+videoInfo.skin,
    adjust: videoInfo.adjust,
    btcolor: videoInfo.btcolor,
    bgcolor: videoInfo.bgcolor,
    start: videoInfo.start,
    expand: videoInfo.expand,
    <?php if($PlayerSkin == 2010) { ?>
    complete: function() {
        if (liked) {
            $(".vlLikeButton").addClass("active");
        }
    },
    <?php
    } else { ?>
        complete: videoInfo.complete,
    <?php } ?>
    ended: videoInfo.ended
});

				
				$(window).on('hashchange', function() {
					var t = getTimeHash();
					vlp.play();
					vlp.seek(t);
					$(window).scrollTop(0);
				});
			

			</script>                       
			<?php if($PlayerSkin == 2010) { ?>
			<script src="/js/annotations.js?22"></script>
                            <script>
function test() {
    window.open("/index", "_blank", "toolbar=yes,scrollbars=no,resizable=yes,top=255,left=255,width=640,height=480");
}
</script>
<?php } ?>
		</div>
	<? } else if ($Player == 1) { ?>
		<!-- JWPlayer HTML5 Player -->
		<div id="vlplayer"></div>
		<script type="text/javaScript">
			var modernPlayer = new ModernPlayer({
				instance: jwplayer("vlplayer"),
				duration: videoInfo.duration,
				videoUrl: "/watch?v="+videoInfo.url,
				src: videoInfo.src,
				hdsrc: <?= $ISHD ? "videoInfo.hdsrc" : "null" ?>,
				startinhd: <?= isset($_COOKIE["vlphd"]) && $_COOKIE["vlphd"] == "1" ? "true" : "false" ?>,
				preview: videoInfo.img,
				autoplay: videoInfo.autoplay,
				start: videoInfo.start,
				ended: videoInfo.ended
			});
			
			$(window).on('hashchange', function() {
				modernPlayer.seek(getTimeHash());
				$(window).scrollTop(0);
			});
		</script>
		
		<!-- Customization -->
		<? if (isset($custom) && $custom) : ?>
			<style>
				.jw-button-color {
					color: <?= $custom[0] ?> !important;
				}
				.jw-progress {
					background: <?= $custom[1] ?> !important;
				}
			</style>
		<? endif ?>
	<? } else { ?>
		<!-- Flash Player -->
		<div id="vlplayer">
			<a href="https://www.adobe.com/go/getflashplayer"><img src="https://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a>
		</div>
		
		<script>
			adjustHeight(25);
			
			var flashvars = {};
			var params = {};
			var attributes = {};
			flashvars.video_id = videoInfo.url;
			flashvars.autoplay = videoInfo.autoplay ? "1" : "0";
			flashvars.thumbnailurl = "liivid.lol"+videoInfo.img;
			params.allowfullscreen = "true";
			attributes.id = "vlplayer";
			swfobject.embedSWF("/player/as.swf", "vlplayer", "100%", "100%", "9.0.0", false, flashvars, params, attributes);
		</script>
	<? } ?>
<? else : ?>
<? if ($Status != 1) : ?>
<div style="display:table;background:black;width:100%;height:100%;color:white;font-size:17px;">
	<div style="display:table-cell;text-align:center;vertical-align:middle;">
		<? if ($Status == 1 || $Status == 0) : ?>
		This video is still converting.....
		<? elseif ($Status == -1) : ?>
		This video is still uploading...
		<? elseif ($Status == -2) : ?>
		This video has failed conversion...
		<? elseif ($Status == -3) : ?>
		This video has been deleted for<br>violating our Community Guidelines.
		<? endif ?>
	</div>
</div>
<? else : ?>
<?php redirect("/404?unavail"); ?>
<? endif ?>
<? endif ?>	