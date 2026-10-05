<?php
ob_start(function($buffer) {
    $protected = [];
    $i = 0;

    $buffer = preg_replace_callback(
        '#<(pre|textarea|script|style|code|meta)[^>]*>.*?</\1>#si',
        function($m) use (&$protected, &$i) {
            $key = "%%PROTECT{$i}%%";
            $protected[$key] = $m[0];
            $i++;
            return $key;
        },
        $buffer
    );

    $buffer = preg_replace('/>\s+</', '><', $buffer);

    $buffer = str_replace(array_keys($protected), $protected, $buffer);

    return $buffer;
});

//$tagline = ["Broadca- wait nvm", "Showcase Yourself.", "Made by copy (not evie luuul)", "Hi! I am sentient please help me.", "meow :33", "rawr >_<", "Like and Subscribe!", "Is shutting down soon (nah jk)", '<img src="https://images.emojiterra.com/twitter/v13.1/512px/1f440.png" height="21" width="200">', "Living one Hell Of A Life.", "GAS STATION TWEAKERS!!!", "Trans rights are human rights.", "Happy pride month even tho its prob not june", "Have a nice day.", "these taglines r pretty heartless", "Vidlii but instead of it being vidlii its liivid", "2020 layout soon trust", "no tagline for u, loser", "Stadium Status", "you can turn this off if u want to btw"];
//$tagline = $tagline[random_int(0, count($tagline) - 1)];
?>
<? if ($_THEMES->Header == 1) : ?>
<?php if($current_page != "profile") { ?>
<header class="n_head">
    <div class="pr_hd_wrapper">
<?php } else { ?>
<header id="pr_hd">
    <div class="pr_hd_wrapper">
<?php } ?>
        <a href="/"><img src="/<?= $LOGO_VALUE ?>?v=<?= $VLPVERSION ?>" alt="LiiVid" title="LiiVid - Showcase Yourself" id="hd_liivid"></a><span style=" position: absolute;top:5px;left:180px;font-size:15px"></span>
        <nav>
           <ul>
    <a href="/"<?php if ($_PAGE->Page_Type == "Home" || !$_PAGE->Page_Type && $current_page != "profile") { ?> id="pr_sel"<?php } ?>><li>Home</li></a>
    <a href="/videos"<?php if ($_PAGE->Page_Type == "Videos") { ?> id="pr_sel"<?php } ?>><li>Videos</li></a>
    <a href="/channels"<?php if ($_PAGE->Page_Type == "Channels" || $current_page == "profile") { ?> id="pr_sel"<?php } ?>>
        <li>Channels</li></a>
        <a href="/community"<?php if ($_PAGE->Page_Type == "Community") { ?> id="pr_sel"<?php } ?>><li>Community</li></a>
            </ul>

        </nav>
        <nav id="sm_nav">
            <? if (!$_USER->logged_in) : ?>
                <a href="/register">Sign Up</a><a href="/help">Help</a><a href="/login">Sign In</a>
                <div id="login_modal">
                    <form action="/login" method="POST">
                        <input type="password" class="search_bar" placeholder="Your Password" style="display:none">
                        <input type="text" name="v_username" class="search_bar" placeholder="Username/E-Mail">
                        <input type="password" name="password" class="search_bar" placeholder="Your Password">
                        <input type="submit" name="submit_login" class="search_button" value="Sign In">
                        <div class="forgot_pass"><a href="/forgot_password">Forgot Password?</a></div>
                    </form>
                </div>
            <? else : ?>
                  <img src="<?= user_avatar4($_USER->avatar) ?>" class="avt2" width="20"  style="vertical-align: middle;">&#32;

                  <a id="hd_name" href="/user/<?= $_USER->displayname ?>"><?= $_USER->displayname ?><img id="n_ar" src="/img/dar.png" style="vertical-align: middle;"></a><a href="/inbox" id="inbox_hd"<? if ($Inbox_Amount > 0) : ?>style="padding-left:34px !important;"<? endif ?>><img src="/img/amsg<? if ($Inbox_Amount == 0) : ?>0<? else : ?>1<? endif ?>.png"<? if ($Inbox_Amount > 0) : ?> style="bottom:2px"<? endif ?>><span>(<?= $Inbox_Amount ?>)</span></a><a href="/help">Help</a><a href="/logout">Log Out</a>
                <div id="name_nav">
                    <div>
                        <? if ($_USER->Is_Admin || $_USER->Is_Mod) : ?><a href="/admin/login">Admin Panel</a><? endif ?>
                        <a href="/my_account">Overview</a>
                        <a href="/my_videos">My Videos</a>
                        <a href="/my_favorites">My Favorites</a>
                        <a href="/analytics">My Analytics</a>
                        <a href="/my_subscriptions">Subscriptions</a>
                        <a href="/friends">Friends</a>
                        <a href="/inbox">Inbox</a>
                    </div>
                </div>
            <? endif ?>
        </nav>
        <div class="pr_hd_bar">
            <form action="/results" method="GET">
           <div class="search_wrapper" style="position:relative; display:inline-block; width:100%;">
  <div style="position:relative; display:inline-block; width:auto;">
    <input type="search" name="q" class="search_bar" id="search_input" maxlength="256" <? if ($_PAGE->Current_Page !== "login" && $_PAGE->Current_Page !== "register" && $_PAGE->Page !== "Send" && !isset($_GET["q"])) : ?><? elseif (isset($_GET["q"])) : ?> value="<?= htmlspecialchars($_GET["q"], ENT_QUOTES) ?>"<? endif ?> onkeyup="autocomplete_search()" autocomplete="off">
    &#32;
    <select name="f" id="filter_select">
        <option>All</option>
        <option <?= (isset($_GET["f"]) && $_GET["f"] == 1 && $current_page == "results") ? 'selected=""' : ""; ?> value="1">Videos</option>
        <option <?= (isset($_GET["f"]) && $_GET["f"] == 2 && $current_page == "results") ? 'selected=""' : ""; ?> value="2">Members</option>
    </select>

    <div id="search_container" style="position:absolute; top:100%; left:0; width:273px; background:white; border:1px solid #ccc; border-top:0; display:none; z-index:1000;text-align:left;">
        <span id="searchbvox" style="list-style:none; margin:0; padding:0;text-align:left;"></span>
    </div>
</div>
  &#32;
<input type="submit" class="search_button" value="Search" style="vertical-align:top;">

            </form>
          
        </div>
          <a href="/upload" class="yel_btn">Upload</a>
    </div>
</header>
<? else : ?>
<?php if($current_page == "profile") { ?><div id="pr_hd"><?php } ?>
<header class="s_head"  <?php if($current_page == "profile") { echo ' style="  padding-top: 6px;
    padding-bottom: 8px;margin:0 auto;width:960px;"'; } ?> >
    <div style="overflow: hidden;">
    <a href="/"><img src="/<?= $LOGO_VALUE ?>" alt="LiiVid" title="LiiVid - Display Yourself."></a>
    <div class="s_search">
         <form action="/results" method="GET">
           <div class="search_wrapper" style="position:relative; display:inline-block; width:100%;">
  <div style="position:relative; display:inline-block; width:auto;">
    <input type="search" name="q"  id="search_input" maxlength="256" <? if ($_PAGE->Current_Page !== "login" && $_PAGE->Current_Page !== "register" && $_PAGE->Page !== "Send" && !isset($_GET["q"])) : ?><? elseif (isset($_GET["q"])) : ?> value="<?= htmlspecialchars($_GET["q"], ENT_QUOTES) ?>"<? endif ?> onkeyup="autocomplete_search()" autocomplete="off">
<style>#search_container {
width: 399px !important;
}
</style>
    <div id="search_container" style="position:absolute; top:100%; left:0; width:399px !important; background:white; border:1px solid #ccc; border-top:0; display:none; z-index:1000;text-align:left;">
        <span id="searchbvox" style="list-style:none; margin:0; padding:0;text-align:left;"></span>
    </div>
</div>
<input type="submit" value="Search"<? if (strpos($_SERVER['HTTP_USER_AGENT'],"PaleMoon") !== false) : ?> style="padding:3px 7px"<? endif ?>>
            </form>
       
      </div>
    </div>
    <a href="javascript:void(0)" class="s_a" onclick="$('#s_toggle2').toggleClass('hddn')">
        Browse
    </a>
    <div id="s_toggle2" class="hddn">
        <a href="/videos">Videos</a>
        <a href="/channels">Channels</a>
        <a href="/community">Community</a>
    </div>
    <a href="/upload" class="s_a">
        Upload
    </a>
    </div>
    <div class="s_center" style="position:absolute;top:<?= ($current_page != "profile" ? 17 : 23) ?>px;right:0px;">
        <? if (!$_USER->logged_in) : ?>
            <a href="/register" style="margin-right:13px;padding-right:13px;border-right: 1px solid #ccc;">
                Create Account
            </a>
            <a href="/login" class="sign_out">
                Sign In
            </a>
        <? else : ?>
            <div id="s_username" onclick="$('#s_toggle').toggleClass('hddn'); $('#s_username').toggleClass('s_username_clicked')">
                <?= $_USER->displayname ?>
            </div>
            <span id="s_toggle" class="hddn">
                <div>
                <a href="/user/<?= $_USER->displayname ?>">My Channel</a>
                <a href="/inbox">Inbox</a>
                </div>
                <div>
                <a href="/my_account">Account</a>
                <a href="/my_subscriptions">Subscriptions</a>
                </div>
                <div>
                <a href="/my_videos">Videos</a>
                <a href="/friends">Friends</a>
                </div>
                <? if ($_USER->Is_Admin || $_USER->Is_Mod) : ?>
                <div style="width:100%">
                    <a style="width:91%;text-align:center" href="/admin/login">Admin Panel</a>
                </div>
                <? endif ?>
            </span>
            <a href="/logout" class="sign_out">
               Sign Out
            </a>
        <? endif ?>
    </div>
</header>
<?php if($current_page == "profile") { ?></div><?php } ?>

<? endif ?>
<?php ob_end_flush() ?>