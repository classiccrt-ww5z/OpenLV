
<? if (($_USER->logged_in && $Modules["recommended"])) : ?>
<div class="wdg" id="rec_widget">
     <div>
        <img src="/img/rec.png" alt="Recommended Videos"><span>Recommended For You</span>
        <div class="wdg_sel">
            <? if ($_USER->logged_in) : ?>
                <div class="up1" onclick="move_up('rec_widget')"></div><div class="do1" onclick="move_down('rec_widget')"></div>
            <? endif ?>
        </div>
    </div>
    <div>
        <? if (!empty($Recommended_Videos)) : ?>
                <div class="v_v_bx">
                    <? foreach ($Recommended_Videos as $Video) : ?>
                        <div style="margin-bottom:8px">
                            <div class="th">
                                <div class="th_t"><?= $Video["length"] ?></div>
                                <a href="/watch?v=<?= $Video["url"] ?>"><img class="vid_th" <?= $Video["thumbnail"] ?> width="140" height="88"></a>
                            </div>
                            <a href="/watch?v=<?= $Video["url"] ?>" class="ba"><?= $Video["title"] ?></a>
                            <div class="vw s"><?= get_time_ago($Video["uploaded_on"]) ?></div>
                            <div class="vw s"><?= number_format($Video["views"]) ?> views</div>
                            <a href="/user/<?= $Video["displayname"] ?>" class="ch_l s"><?= $Video["displayname"] ?></a>
                            <div class="s_r"><?= show_ratings($Video,14,13) ?></div>
                        </div>
                    <? endforeach ?>
                </div>
        <? else : ?>
          <div style="margin-bottom: 5px;">
                <strong style="display:block;font-size:13px;">Personalized video recommendations</strong>
                <span> Getting personalized recommendations is really easy, all you have to do is watch some <a href="/videos" style="font-weight:bold">videos</a> then come back to this page to see what videos LiiVid has recommended just for you! </span>
            </div>
        <? endif ?>
    </div>
</div>
<? endif ?>