<div style="overflow:hidden;width:865px;margin:0 auto">
    <h2 style="margin:0 0 2px"><?= $LANGS["groupmoderation"] ?></h2>
    <form action="/group_moderation" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= (int)$_GET["id"] ?>">
        <table cellpadding="4px" style="position:relative;right:4px">
            <tr>
                <td valign="top"><b><?= $LANGS["desc"] ?>:</b></td>
                <td><textarea name="description" maxlength="5000" cols="50" rows="7"><?= $Group["description"] ?></textarea></td>
            </tr>
            <tr>
                <td><b><?= $LANGS["approvemembers"] ?>:</b></td>
                <td><label><input type="radio" name="instant" value="0" <? if ($Group["instant_join"] == 0) : ?>checked<? endif ?>> <span style="position:relative;bottom:2px"><?= $LANGS["yes"] ?></span></label> <label><input type="radio" name="instant" value="1" <? if ($Group["instant_join"] == 1) : ?>checked<? endif ?>> <span style="position:relative;bottom:2px"><?= $LANGS["no"] ?></span></label></td>
            </tr>
            <tr>
                <td><b><?= $LANGS["approvevideos"] ?>:</b></td>
                <td><label><input type="radio" name="instant_video" value="0" <? if ($Group["instant_video"] == 0) : ?>checked<? endif ?>> <span style="position:relative;bottom:2px"><?= $LANGS["yes"] ?></span></label> <label><input type="radio" name="instant_video" value="1" <? if ($Group["instant_video"] == 1) : ?>checked<? endif ?>> <span style="position:relative;bottom:2px"><?= $LANGS["no"] ?></span></label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" class="yt-button yt-button-primary" name="change_info" value="<?= $LANGS["changeinfo"] ?>"> <a href="/group?id=<?= (int)$_GET["id"] ?>"><button class="yt-menubutton-btn yt-button yt-button-primary" type="button"><?= $LANGS["cancel"] ?></button></a></td>
            </tr>
        </table>
    </form>
    <br /><br />
    <form action="/group_moderation" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= (int)$_GET["id"] ?>">
        <table cellpadding="4px" style="position:relative;right:4px">
            <tr>
                <td><b><?= $LANGS["image"] ?>:</b></td>
                <td><input type="file" name="image"></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" class="yt-button yt-button-primary" name="change_image" value="<?= $LANGS["changeimage"] ?>"> <a href="/group?id=<?= (int)$_GET["id"] ?>"><button class="yt-menubutton-btn yt-button yt-button-primary" type="button"><?= $LANGS["cancel"] ?></button></a></td>
            </tr>
        </table>
    </form>
    <br /><br />
    <form action="/group_moderation" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= (int)$_GET["id"] ?>">
        <table cellpadding="4px" style="position:relative;right:4px">
            <tr>
                <td valign="top"><b><?= $LANGS["styling"] ?>:</b></td>
                <td>
                    <textarea name="css" maxlength="1000" cols="50" rows="7"><? if (file_exists($_SERVER["DOCUMENT_ROOT"]."/u/css/".$_REQUEST["id"].".txt")) : ?><?= file_get_contents($_SERVER["DOCUMENT_ROOT"]."/u/css/".$_REQUEST["id"].".txt") ?><? endif ?></textarea><br />
                    <div><?= $LANGS["cssdesc"] ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" class="yt-button yt-button-primary" name="change_css" value="<?= $LANGS["updatecss"] ?>"> <a href="/group?id=<?= (int)$_GET["id"] ?>"><button class="yt-menubutton-btn yt-button yt-button-primary" type="button"><?= $LANGS["cancel"] ?></button></a></td>
            </tr>
        </table>
    </form>
    <br /><br />
    <form action="/group_moderation" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= (int)$_GET["id"] ?>">
        <table cellpadding="4px" style="position:relative;right:4px">
            <tr>
                <td valign="top"><b><?= $LANGS["groupmessage"] ?>:</b></td>
                <td><textarea name="message" maxlength="500" cols="50" rows="7"></textarea></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" class="yt-button yt-button-primary" name="send_message" value="<?= $LANGS["sendmessagetomembers"] ?>"> <a href="/group?id=<?= (int)$_GET["id"] ?>"><button class="yt-menubutton-btn yt-button yt-button-primary" type="button"><?= $LANGS["cancel"] ?></button></a></td>
            </tr>
        </table>
    </form><br>
    <button class="yt-menubutton-btn yt-button yt-button-primary" onclick="if (confirm('Are you sure you want to delete this group?')) { location.href='/a/delete_group?group=<?= $Group["id"] ?>'; }"><?= $LANGS["delgroup"] ?></button>
</div>
