<link rel="shortcut icon" href="/img/favicon.png" type="image/png">
<meta charset="utf-8">
<title><? echo $_PAGE->Page_Title ?></title>

<meta http-equiv="X-UA-Compatible" content="IE=edge">


<link rel="apple-touch-icon" href="/img/vl_app.png">

<meta name="description" content="<? echo $_PAGE->Page_Description ?>">
<meta name="keywords" content="<? echo $_PAGE->Page_Tags ?>">
<meta property="og:site_name" content="LiiVid">
<meta property="og:url" content="<?= htmlspecialchars("https://" . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? ''), ENT_QUOTES) ?>">

<? if ($_PAGE->Current_Page == "videos") : ?>
<link rel="canonical" href="/videos">
<? elseif ($_PAGE->Current_Page == "channels") : ?>
<link rel="canonical" href="/channels">
<? endif ?>

<meta name="msapplication-tap-highlight" content="no">

<link rel="stylesheet" type="text/css" href="<?= CSS_FILE ?>">

<? $_THEMES->load_themes() ?>

<script>adblock_installed = false;</script>
<script src='https://unpkg.com/darkreader@4.9.117/darkreader.js'></script>

<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>
<? if (!isset($_SESSION["deto"]) && !isset($_SESSION["beto"])) : ?>
<script>
$.ajax({type: "POST",url: "/ajax/deto"});
</script>
<? endif ?>
<script>
zd = true;
</script>


<? if (($_PAGE->Current_Page == "watch") || $_PAGE->Current_Page == "watchtest") : ?>
    <meta property="datePublished" content="<?= date("Y-m-d",strtotime($_VIDEO->Info["uploaded_on"])) ?>">

    <meta property="og:type" content="video">
    <meta property="og:video" content="https://www.liivid.lol/usfi/v/<?= $URL ?>.<?= $FILENAME ?><?= $ISHD ? ".720" : "" ?>.mp4" />
    <meta property="og:video:type" content="video/mp4" />

    <meta property="video:duration" content="<?= $Length ?>">
   <meta name="description" content="<?php echo html_entity_decode(strip_tags($_PAGE->Page_Description)); ?>" />
<meta property="og:description" content="<?php echo html_entity_decode(strip_tags($_PAGE->Page_Description)); ?>">
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:title" content="<?= $_PAGE->Page_Title ?>" />
    <meta property="twitter:site" content="@LiiVid" />
    <meta property="twitter:description" content="<?= $_PAGE->Page_Description ?>" />
    <meta property="twitter:player" content="/embed?v=<?= $URL ?>&a=0" />
    <meta property="twitter:player:width" content="640" />
    <meta property="twitter:player:height" content="360" />
    <meta property="twitter:image" content="/usfi/prvw/<?= $URL ?>.jpg" />
<? endif ?>

<? if (($_PAGE->Current_Page == "watch" or $_PAGE->Current_Page == "watchtest" or $_PAGE->Current_Page == "profile" or $_PAGE->Current_Page == "community" or $_PAGE->Current_Page == "index" or $_PAGE->Current_Page == "my_playback") && isset($Player)) : ?>
<? if ($Player == 1) : ?>
        <script src="/jwplayer2/jwplayer.js"></script>
        <script>jwplayer.key="pC5h3OO+44j2Ht66GosiwI/yBi8Kp1KC8cZL/g=="</script>
        <? if ($_USER->logged_in && $_USER->Is_Admin && 0) : ?>
            <script src="/js/modern_player.js?<?=rand(0,999999)?>"></script>
        <? else : ?>
            <script src="/js/modern_player.js?<?=$VLPVERSION?>"></script>
        <? endif ?>
    <? else : ?>
		<? if ($_USER->Is_Admin && 0) : ?>
        <script src="/js/vlPlayer.js?v=<?=rand(10000,99999)?>"></script>
		<script>swfobject.registerObject("flPlayer", "9.0.0");</script>
		<script>window.vlpv = <?=$VLPVERSION?>;</script>
		<? else : ?>
        <script src="/vlPlayer/main19.js?<?=$VLPVERSION?>"></script>
		<script>swfobject.registerObject("flPlayer", "9.0.0");</script>
		<script>window.vlpv = <?=$VLPVERSION?>;</script>
		<? endif ?>
	<? endif ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.4/jquery.rateyo.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.4/jquery.rateyo.min.js"></script>
<? endif ?>
