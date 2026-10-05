<?php
require_once __DIR__ . "/../../_includes/_cli_only.php";
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";
error_reporting(E_ALL);

$Users = $DB->execute("SELECT username FROM users");

foreach ($Users as $User) {

    $Friends = $DB->execute(
        "SELECT users.displayname, users.banned, users.avatar
         FROM users
         INNER JOIN friends 
           ON (friends.friend_1 = users.username 
               OR friends.friend_2 = users.username)
         WHERE (friends.friend_1 = :USERNAME 
                OR friends.friend_2 = :USERNAME)
           AND users.banned = 0
           AND friends.status = 1
           AND users.username <> :USERNAME",
        false,
        [":USERNAME" => $User["username"]]
    );

    $Friendsc = count($Friends);

    $DB->modify(
        "UPDATE users SET friends = :count WHERE username = :username",
        [
            ":count" => $Friendsc,
            ":username" => $User["username"]
        ]
    );
}

echo "ok";
