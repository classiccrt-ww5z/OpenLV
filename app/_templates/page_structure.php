<?php if(empty($_COOKIE["vidlii"]) || ($_COOKIE["vidlii"] != 1)) { ?>
<!doctype html>
<html lang="en">
<head>
<?php require_once $_SERVER['DOCUMENT_ROOT']."/_templates/_head/main_head.php"; ?><meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body>
<?php
$Has_Adblock=false;
$Check=$DB->execute("SELECT submit_date FROM badboys WHERE ip = :IP AND submit_date > NOW() - INTERVAL 1 MINUTE", true, [":IP"=>user_ip()]);
if($Check||isTorRequest()) {
    $Has_Adblock=false;
    if($Check) $Adblock_Time=strtotime($Check["submit_date"]);
    else $Adblock_Time=time()-rand(0,20);
}
?>
<?php if(!empty($Top_text)): ?><div style="background:#f0f0f0;border-bottom:1px solid #989898;text-align:center;padding:4px 0;"><?= $Top_text ?></div><?php endif ?>
<div class="wrapper">
<?php require_once $_SERVER['DOCUMENT_ROOT']."/_templates/_layout/header.php"; ?>
<?php if($_USER->logged_in && !$_USER->Is_Activated): ?><div style="border:1.5px solid red;padding:6px;text-align:center;margin-bottom:11px;font-size:14px;font-weight:bold">Click the activation link we sent to your email to use LiiVid to its fullest! (Check the spam folder!)</div><?php endif ?>
<?php if(isset($_SESSION["notification"])): ?><div style="border:1.5px solid <?= $_SESSION["n_color"] ?>;padding:6px;text-align:center;margin-bottom:11px;font-size:14px;font-weight:bold;margin:0 auto 11px;width:946px;border-radius:3px"><?= $_SESSION["notification"] ?></div><?php unset($_SESSION["notification"]); unset($_SESSION["n_color"]); endif ?>
<main class="bottom_wrapper"><?php require_once $_SERVER['DOCUMENT_ROOT']."/_templates/_pages/$_PAGE->Page.php"; ?></main>
<?php require_once $_SERVER['DOCUMENT_ROOT']."/_templates/_layout/footer.php"; ?>
</div>
<?php require_once $_SERVER['DOCUMENT_ROOT']."/_templates/_layout/scripts.php"; ?>

</body>
</html>
<?php } else { ?>
<?php require_once $_SERVER['DOCUMENT_ROOT']."/_templates_vidlii/page_structure.php"; ?>

<?php } ?>