<?php
require_once "_includes/init.php";

//REQUIREMENTS / PERMISSIONS
//- Requires Login
if (!$_USER->logged_in) {
    redirect("/login");
    exit();
}

if (isset($_POST["update_player"])) {
    if (isset($_POST["player"])) {
        switch ($_POST["player"]) {
            case "html5":
                setcookie("player", "1", time() + (86400 * 31), "/");
                break;
            case "flash": // Renaissance
                setcookie("player", "0", time() + (86400 * 31), "/");
                setcookie('cp', null, -1, '/');
                setcookie("vlpColors", $_POST["vlpButton"] . "," . $_POST["vlpBackground"], time() + (86400 * 31), "/");
                break;
            case "penguin": // Penguin
                setcookie("player", "3", time() + (86400 * 31), "/");
                break;
            case "2010": // 2010
                setcookie("player", "4", time() + (86400 * 31), "/");
                setcookie("vlpColors", $_POST["vlpButton"] . "," . $_POST["vlpBackground"], time() + (86400 * 31), "/");
                break;
            default: // Classic
                setcookie("player", "2", time() + (86400 * 31), "/");
                setcookie("vlpColors", $_POST["vlpButton"] . "," . $_POST["vlpBackground"], time() + (86400 * 31), "/");
            
        }
    }
    if ($_POST["autoplay"] != "1" || $_POST["size"] != "0") {
        if ($_POST["autoplay"] == "0") {
            $Player = "0";
        } else {
            $Player = "1";
        }
        if ($_POST["size"] == "0") {
            $Player .= ",0";
        } else {
            $Player .= ",1";
        }
        setcookie("cp2", $Player, time() + (86400 * 31), "/");
    } else {
        setcookie('cp2', null, -1, '/');
    }
    if (($_POST["bcolor"] !== "#FFFFFF" || $_POST["pcolor"] !== "#6E84FF") && $_POST["player"] !== "flash") {
        setcookie("cp", $_POST["bcolor"] . "," . $_POST["pcolor"], time() + (86400 * 31), "/");
    } else {
        setcookie('cp', null, -1, '/');
    }
    if ($_POST["hdplayback"] == 1) {
        setcookie("vlphd", "1", time() + (86400 * 355), "/");
    } else {
        setcookie("vlphd", "0", time() + (86400 * 355), "/");
    }
    if ($_POST["aspect"] == 0) {
        setcookie("aspect", "0", time() + (86400 * 355), "/");
    } elseif($_POST["aspect"] == 1) {
        setcookie("aspect", "1", time() + (86400 * 355), "/");
    } else {
        setcookie("aspect", "2", time() + (86400 * 355), "/");
    }

    notification("Video player has been successfully changed!", "/my_playback", "green");
}

//VALUES
if (isset($_COOKIE["cp"])) {
    $Player1 = explode(",", $_COOKIE["cp"]);
    $Bcolor = $Player1[0];
    $Pcolor = $Player1[1];
} else {
    $Bcolor = "#ffffff";
    $Pcolor = "#6e84ff";
}

if (isset($_COOKIE["cp2"])) {
    $Player1 = explode(",", $_COOKIE["cp2"]);

    $Autoplay = $Player1[0];
    $Size = $Player1[1];
} else {
    $Autoplay = 1;
    $Size = 0;
}

if (isset($_COOKIE["player"])) {
    $Player = (int) $_COOKIE["player"];
    if ($Player < 0 || $Player > 4)
        $Player = 2;
} else {
    $Player = 2;
}


if(isset($_COOKIE["aspect"])){
    $Aspect = (int)$_COOKIE["aspect"];
    if($Aspect!==0 && $Aspect!==1) $Aspect = 2;
}else{
    $Aspect = 2;
}




$_USER->get_profile();

$Channel_Version = $_USER->Info["channel_version"];


$Account_Title = "Playback Setup";


$_PAGE->set_variables(array(
    "Page_Title" => "Playback Setup - LiiVid",
    "Page" => "Playback",
    "Page_Type" => "Home",
    "Show_Search" => false
));

require_once "_templates/settings_structure.php";
