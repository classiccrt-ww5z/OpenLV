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
?>
<div class="cl"></div>
<footer style="font-size:11px !important">
    <div style="font-size:11px !important">
        <div>
            <strong style="font-size:11px !important">About LiiVid</strong>
            <div>
                <a href="/blog">Blog</a><a href="/about">About</a>
            </div>
            <div>
                <a href="/terms">Terms of Use</a><a href="/privacy">Privacy Policy</a>
            </div>
            <div>
                <a href="/themes">Themes</a><a href="/testlii">Testlii</a>
            </div>
        </div>
        <div>
            <strong style="font-size:11px !important">Help & Info</strong>
            <div>
                <a href="/help">Help Center</a><a href="/partners">Partnership</a>
            </div>
            <div>
                <a href="/copyright">Copyright</a><a href="/guidelines">Community Guidelines</a>
            </div>
        </div>
        <div>
            <strong style="font-size:11px !important">Your Account</strong>
            <div>
                <a href="/my_videos">My Videos</a><a href="/my_favorites">My Favorites</a>
            </div>
            <div>
                <a href="/my_subscriptions">My Subscriptions</a><a href="/my_account">My Account</a>
            </div>
        </div>
        
    </div>
</footer>
  <form style="text-align:center;margin-bottom:10px;" action="/results" method="GET">
        <input type="search" name="q" class="search_bar" maxlength="256" <? if ($_PAGE->Current_Page !== "login" && $_PAGE->Current_Page !== "register" && $_PAGE->Page !== "Send" && !isset($_GET["q"])) : ?><? elseif (isset($_GET["q"])) : ?> value="<?= htmlspecialchars($_GET["q"], ENT_QUOTES) ?>"<? endif ?>> &#32; <select name="f">
					<option>All</option>
					<option <?= (isset($_GET["f"]) && $_GET["f"] == 1 && $current_page == "results") ? 'selected=""' : ""; ?> value="1">Videos</option>
					<option <?= (isset($_GET["f"]) && $_GET["f"] == 2 && $current_page == "results") ? 'selected=""' : ""; ?> value="2">Members</option>
				</select>&#32; <input type="submit" value="Search" class="search_button">
    </form> 
    <div style="text-align:center;margin:0 !important;margin-bottom:10px !important;font-size:12px !important;">
<b>Copyright © 2008 - <?= date('Y') ?> ww5z</b>
</div>
<?php ob_end_flush() ?>
