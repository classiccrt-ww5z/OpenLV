<?php
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";

//REQUIREMENTS / PERMISSIONS
//- Requires Login
//- Requires ($_POST["user"])
if (!$_USER->logged_in)            { exit(); }
if (!isset($_POST["user"]))        { exit(); }


$User = $DB->execute("SELECT username FROM users WHERE username = :USERNAME", true, [":USERNAME" => $_POST["user"]]);
if ($DB->RowNum == 1 && $User["username"] !== $_USER->username) {
    $User = $User["username"];

    $Query = $DB->execute("SELECT id, seen FROM friends WHERE (friend_1 = :USERNAME1 AND friend_2 = :FRIEND2) OR (friend_1 = :FRIEND1 AND friend_2 = :USERNAME2) AND status = 0 AND by_user <> :USERNAME3", true,
                         [
                             ":USERNAME1" => $_USER->username,
                             ":FRIEND1"   => $User,
                               ":USERNAME2" => $_USER->username,
                             ":FRIEND2"   => $User,
                                ":USERNAME3" => $_USER->username
                         ]);

    if ($DB->RowNum > 0) {
        $DB->modify("DELETE FROM friends WHERE (friend_1 = :USERNAME2 AND friend_2 = :FRIEND2) OR (friend_1 = :FRIEND1 AND friend_2 = :USERNAME1) AND status = 0 AND by_user <> :USERNAME3",
                   [
                       ":USERNAME1" => $_USER->username,
                             ":FRIEND1"   => $User,
                               ":USERNAME2" => $_USER->username,
                                 ":USERNAME3" => $_USER->username,
                             ":FRIEND2"   => $User
                   ]);
    }
}