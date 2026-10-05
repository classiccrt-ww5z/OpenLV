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

         $Featured_Videos             = new Videos($DB, $_USER);
        $Featured_Videos->WHERE_C    = "AND videos.featured = 1";
        $Featured_Videos->ORDER_BY   = "RAND()";
        $Featured_Videos->LIMIT      = 8;
        $Featured_Videos->Blocked    = false;
        $Featured_Videos->get();
        $Featured_Videos             = $Featured_Videos->fixed();
function video_thumbnail2($URL,$LENGTH,$Width,$Height,$Title = NULL) {
    if (!empty($LENGTH) || $LENGTH == "0") { $Length = seconds_to_time((int)$LENGTH); } else { $Length = $LENGTH; }
    if (file_exists($_SERVER['DOCUMENT_ROOT']."/usfi/thmp/$URL.jpg")) { $Thumbnail = "/usfi/thmp/$URL.jpg"; } else { $Thumbnail = "/img/no_th.jpg"; }

    return '<div class="th"><div class="th_t">'.$Length.'</div><a href="/watch?v='.$URL.'"><img class="vid_th" src="'.$Thumbnail.'" width="'.$Width.'" height="'.$Height.'"></a></div>';
}
?>
<!doctype html>
<html>
<head>
    <title>404: Page Not Found</title>
    <link rel="stylesheet" type="text/css" href="<?= CSS_FILE ?>">
    <? $_THEMES->load_themes() ?>
</head>
<body>
<div class="wrapper">
    <? require_once $_SERVER['DOCUMENT_ROOT']."/_templates/_layout/header.php"; ?>
 <main class="bottom_wrapper">
  <div style="margin:37px auto 0">
        <div style="width:33%;float:left">
            <img src="/img/404.png" style="width:310px;position:relative;top:9px;">
        </div>
        <div style="float:left;width:67%;">
            <div style="font-size:33px;font-weight:bold;text-align:center;color:#404040">404 - Page Not Found</div>
            <div style="font-size:23px;font-weight:bold;text-align:center;color:#606060">Sorry about that. :(</div>

   
        </div><div style="float:left;margin-top:15px">
            
            

   
        <div class="wdg" id="ft_widget" style="width:665px">
    <div style="background:#dae9fe">
        <a href="/special_videos?c=0&amp;t=v"><img src="/img/ft.png" alt="Featured Videos"><span>Featured Videos</span></a>
        <div class="wdg_sel">
        </div>
    </div>
    <div>
        <div class="v_v_bx">
                             <? foreach ($Featured_Videos as $Video) : ?>
                <div style="margin-bottom: 8px">
                    <div class="th">
                        <div class="th_t"><?= $Video["length"] ?></div>
                        <a href="/watch?v=<?= $Video["url"] ?>"><img class="vid_th" <?= $Video["thumbnail"] ?> width="140" height="88"></a>
                    </div>
                    <a href="/watch?v=<?= $Video["url"] ?>" class="ba"><?= $Video["title"] ?></a>
                    <div class="vw s"><?= number_format($Video["views"]) ?> views</div>
                    <a href="/user/<?= $Video["displayname"] ?>" class="ch_l s"><?= $Video["displayname"] ?></a>
                    <div class="s_r"><?= show_ratings($Video,14,13) ?></div>
                </div>
            <? endforeach ?>
               
                    </div>
    </div>
</div></div>
    </div>
    </main>
    <? require_once $_SERVER['DOCUMENT_ROOT']."/_templates/_layout/footer.php"; ?>
</div>
</body>
</html>
