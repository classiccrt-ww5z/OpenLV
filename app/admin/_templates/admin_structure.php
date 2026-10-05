<!doctype html>
<html lang="en">
<head>
<script>adblock_installed = false; zd = false; </script>
    <title><?= $Page_Title ?> - LiiVid Admin</title>
    <link rel="stylesheet" type="text/css" href="<?= CSS_FILE ?>">
    <!-- script src='https://www.google.com/recaptcha/api.js'></script -->
    <script src="/js/jquery.js"></script>

    <script>
const url = '/admin/systemstats.php';

const updateSystemStats = async () => {
  const systemStats = document.getElementById('systemStats');

  try {
    const response = await fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });

    if (!response.ok) throw new Error('system stat err');

    const data = await response.text();
    systemStats.innerHTML = data;
  } catch (err) {
    console.error('system stat err', err);
  }
};

updateSystemStats();

setInterval(updateSystemStats, 1000);

</script>
    <style>
        .panel_box {
            padding: 6px;
            border: 1px solid #dddddd;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 20px
        }

        .panel_box > strong:first-of-type {
            display: block;
            margin-bottom: 6px;
            font-size: 15px;
        }

        #user_search {
            padding: 0;
            margin: 0;
        }

        #user_search li {
            padding: 3px;
            margin: 0;
            list-style: none;
        }

        #user_search a {
            text-decoration: none;
            color: black;
        }

        #user_search li:hover {
            background-color: #cccccc;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <? require_once $_SERVER['DOCUMENT_ROOT']."/_templates/_layout/header.php" ?>
    <? if (isset($_SESSION["notification"])) : ?>
        <div style="border: 1.5px solid <?= $_SESSION["n_color"] ?>;padding:6px;text-align:center;margin-bottom:11px;font-size:14px;font-weight:bold"><?= $_SESSION["notification"] ?></div>
        <? unset($_SESSION["notification"]); unset($_SESSION["n_color"]); ?>
    <? endif ?>
    <main class="bottom_wrapper">
    <div style="border-bottom:1px solid #ccc"></div>

       <div class="settings_menu">
    <a href="/admin/dashboard" <? if ($Page == "dashboard") : ?>id="nav_sel"<? endif ?>>Dashboard</a>

    <? if ($_USER->Is_Admin) : ?>
        <a href="/admin/statistics" <? if ($Page == "statistics") : ?>id="nav_sel"<? endif ?>>Charts</a>
    <? endif ?>

    <a href="/admin/users" <? if ($Page == "Users") : ?>id="nav_sel"<? endif ?>>Users</a>

    <a href="/admin/videos" <? if ($Page == "Videos") : ?>id="nav_sel"<? endif ?>>Videos</a>

    <a href="/admin/misc" <? if ($Page == "misc") : ?>id="nav_sel"<? endif ?>>Misc</a>
    <div style="border-bottom:1px solid #ccc;margin-bottom:10px;"></div>

    	<table id="systemStats"  cellspacing="0" cellpadding="0">
<?= getSystemStats() ?></table>
</div>

        <? require_once $_SERVER['DOCUMENT_ROOT']."/admin/_templates/$Page.php" ?>
    </main>
        <? require_once $_SERVER['DOCUMENT_ROOT']."/_templates/_layout/footer.php" ?>
</div>
<script src="<?= MAIN_JS_FILE ?>"></script>
</body>
</html>
