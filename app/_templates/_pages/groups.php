<div class="vc_l">
    <div class="vc_cats">
        <div>Categories</div>
        <ul>
            <li><a href="/groups"<?php if (!isset($_GET["b"])) : ?> class="category-selected"<?php endif ?>><?= $LANGS['recentgroups'] ?></a></li>
            <li><a href="/groups?b=members"<?php if (isset($_GET["b"]) && $_GET["b"] == "members") : ?> class="category-selected"<?php endif ?>><?= $LANGS['mostmembers'] ?></a></li>
            <li><a href="/groups?b=videos"<?php if (isset($_GET["b"]) && $_GET["b"] == "videos") : ?> class="category-selected"<?php endif ?>><?= $LANGS['mostvideos'] ?></a></li>
            <li><a href="/groups?b=topics"<?php if (isset($_GET["b"]) && $_GET["b"] == "topics") : ?> class="category-selected"<?php endif ?>><?= $LANGS['groupmostdiscussed'] ?></a></li>
        </ul>
    </div>
    <div style="padding-top: 12px;">
        <input type="submit" class="yt-button yt-button-primary" name="creategroup" value="<?= $LANGS['createagroup'] ?>" onclick="window.location.href='/groups_create'">
    </div>
</div>

<div class="vc_r">
    <div class="vc_hd">
        <ul>
            <li id="vc_selec"><a href="groups">Groups</a></li>
            <li style="padding:0 48px"></li>
            <li><a href="groups">My Groups</a></li>
        </ul>
    </div>
    <div style="background:white;z-index:10;height:20px;width:600px;position:absolute"></div>
    <div class="vc_nav"></div>

    <div class="vc_channels">
        <?php if ($Groups) : ?>
            <?php foreach ($Groups as $Group) : ?>     
                <div>
                    <a href="/group?id=<?= $Group["id"] ?>"><?= $Group["title"] ?></a>
                    <a href="/group?id=<?= $Group["id"] ?>">
                        <img src="/u/grp/<?= $Group["id"] ?>.jpg" width="80" height="80" class="avt2" alt="<?= $Group["title"] ?>">
                    </a>
                    <div>
                         <?= $DB->execute("SELECT count(video) as amount FROM groups_videos WHERE group_id = :ID", true, [":ID" => $Group["id"]])["amount"] ?><br>Videos<br><br>
                   <?= $DB->execute("SELECT count(id) as amount FROM groups_topics WHERE group_id = :ID", true, [":ID" => $Group["id"]])["amount"] ?><br>Topics<br>
                   
                    
                    
                    </div>
                  
                </div>
            <?php endforeach ?>
        <?php else : ?>
            <div style="font-size:14px;text-align:center;padding:22px 0 16px"><?= $LANGS['nogroups'] ?></div>
        <?php endif ?>
    </div>
<div class="cl"></div>
    <?php if ($Groups) : ?>
        <div class="vc_pagination">
            <?php 
                if (!empty($_GET["c"]) && $_GET["c"] != 0) { 
                    $_PAGINATION->new_show('c='.$_GET["c"].''); 
                } else { 
                    $_PAGINATION->new_show(); 
                } 
            ?>
        </div>
    <?php endif ?>
</div>



<div class="clear"></div>
