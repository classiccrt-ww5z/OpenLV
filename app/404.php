<?php
    require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";

    $Page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    if (ctype_alnum($Page)) {
        $Username = $DB->execute("SELECT displayname FROM users WHERE displayname = :USERNAME", true, [":USERNAME" => substr($Page, 0, 20)]);
        if ($DB->RowNum == 1) {
            $Username = $Username["displayname"];
            redirect("/user/$Username"); exit();
        }
		
        $Username = $DB->execute("SELECT u.displayname FROM users_oldnames r, users u WHERE r.displayname = :USERNAME AND u.username = r.username LIMIT 1", true, [":USERNAME" => substr($Page, 0, 20)]);
        if ($DB->RowNum == 1) {
            $Username = $Username["displayname"];
            redirect("/user/$Username"); exit();
        }
    }

    $Featured_Videos = $DB->execute("SELECT videos.title, videos.length, videos.1_star, videos.2_star, videos.3_star, videos.4_star, videos.5_star, videos.url, videos.views, videos.uploaded_by, users.displayname FROM videos, users WHERE videos.featured = 1 AND users.username = videos.uploaded_by ORDER BY rand() DESC LIMIT 4");

function video_thumbnail2($URL,$LENGTH,$Width,$Height,$Title = NULL) {
    if (!empty($LENGTH) || $LENGTH == "0") { $Length = seconds_to_time((int)$LENGTH); } else { $Length = $LENGTH; }
    if (file_exists($_SERVER['DOCUMENT_ROOT']."/usfi/thmp/$URL.jpg")) { $Thumbnail = "/usfi/thmp/$URL.jpg"; } else { $Thumbnail = "/img/no_th.jpg"; }

    return '<div class="th"><div class="th_t">'.$Length.'</div><a href="/watch?v='.$URL.'"><img class="vid_th" src="'.$Thumbnail.'" style="width:133px !important;" width="'.$Width.'" height="'.$Height.'"></a></div>';
}
?>
<!doctype html>
<html>
<head>
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" type="text/css" href="<?= CSS_FILE ?>">
    <? $_THEMES->load_themes() ?>
     <style>
         .rounded-corner {
         css-border-radius: 5px;
         -webkit-border-radius: 5px;
         -moz-border-radius: 5px;
         }
         .box-title {
         height: 23px;
         padding: 6px 18px 0 10px;
         vertical-align: top;
         background-color: #D2E3FB;
         border: 1px solid #A1B4D9;
         css-border-radius: 5px 5px 0px 0px;
         -webkit-border-top-left-radius: 5px;
         -webkit-border-top-right-radius: 5px;
         -moz-border-radius: 5px 5px 0px 0px;
         font-size: 16px;
         font-weight: bold;
         }
         .promoted-icon {
         background: transparent url(https://web.archive.org/web/20090608023625im_/http://s.ytimg.com/yt/img/iyt_iconset-vfl94059.png) repeat-x scroll -18px 0;
         height:18px;
         width:18px;
         margin-right: 5px;
         }
         .title-text {
         vertical-align: top;
         }
         .box-data {
         padding: 4px 13px 0;
         border-left: 1px solid #A1B4D9;
         border-right: 1px solid #A1B4D9;
         border-bottom: 1px solid #A1B4D9;
         css-border-radius: 0px 0px 5px 5px;
         -webkit-border-bottom-left-radius: 5px;
         -webkit-border-bottom-right-radius: 5px;
         -moz-border-radius: 0px 0px 5px 5px;
         }
         #main-content {
         margin-left: auto;
         margin-right: auto;
         position: relative;
         width: 650px;
         }
         #error-message {
         text-align: center;
         font-size: 12px;
         font-weight: bold;
         color: #666;
         background-color: #FFC;
         margin-top: 50px;
         padding: 12px 0;
         border: 1px solid #FFCC05;
         }
         #search-wrapper {
         margin-top: 10px;
         padding: 15px 0;
         border: 1px solid #CCC;
         }
         #logo {
         float: left;
         padding: 0 20px;
         border-right: 1px solid #CCC;
         }
         #search-box {
         margin: 7px 0 0 20px;
         float: left;
         }
         #search-box .search-term {
         width: 370px;
         }
         #links {
         text-align: center;
         margin-top: 10px;
         }
         #promoted-videos {
         margin-top: 20px;
         }
         #copynotice {
         margin: 20px 0;
         text-align: center;
         font-size: 11px;
         }body,input,textarea{
    font:12px Arial,sans-serif
}
      </style>
</head>
<body>
<div class="wrapper" style="width:650px !important;">
   <div id="error-message" class="rounded-corner">
       <?php if(!isset($_GET["unavail"])) { ?>
		 We're sorry, the page you requested cannot be found.
		 <?php } else { ?>
		 		 We're sorry, the video you requested is currently unavailable.

		 <?php } ?>
         </div>
 <div id="search-wrapper" class="rounded-corner">
            <div id="logo">
               <img height="41" src="/img/LiiVid7.png">
            </div>
            <div id="search-box">
               <form autocomplete="off" class="search-form" action="/results" method="get" name="searchForm">
                  <input id="search-type-search-box" name="search_type" type="hidden" value="">
                  <input id="search-box-search-term" class="search-term" name="q" type="text" onkeyup="top.goog.i18n.bidi.setDirAttribute(event,this)" value="" maxlength="128" onfocus="addClass(this, 'search-term-focus')" onblur="removeClass(this, 'search-term-focus')">
                 <input type="submit" class="search_button" value="Search" style="vertical-align:top;">
               </form>
            </div>
            <div style="clear:both;"></div>
         </div>
<div id="links">
            <a href="l/special_videos?c=0&t=v">Featured Videos</a> |
            <a href="/videos?c=0&o=tr&t=1">Most Popular Videos</a> |
            <a href="/videos?c=0&o=mv&t=1">Most Watched Videos</a>
         </div>
            <div class="wdg" id="ft_widget" style="margin-top:15px;">
                <div style="background:#dae9fe">
                    <img src="/img/ft.png" alt="Featured Videos"><span>Featured Videos</span>
                </div>
                <div>
                    <div class="v_v_bx">
                    <?php
                    $i = 0;
                    foreach($Featured_Videos as $featured) { ?>
                        <div style="margin-right:<?= ($i == 3) ? 0 : 23 ?>px">
                            <?= video_thumbnail2($featured["url"],$featured["length"],133,88) ?>
                            <a href="/watch?v=<?= $featured["url"] ?>" class="ba" style="height:2.4em;line-height:15px"><?= $featured["title"] ?></a>
                            <div class="vw s"><?= number_format($featured["views"]) ?> views</div>
                            <a href="/user/<?= $featured["displayname"] ?>" class="ch_l s"><?= $featured["displayname"] ?></a>
                            <div class="s_r"><?= show_ratings($featured,14,13) ?></div>
                        </div>
                    <?php $i++; } ?>
                    </div>
                </div>
            </div>
</div>
</body>
</html>
