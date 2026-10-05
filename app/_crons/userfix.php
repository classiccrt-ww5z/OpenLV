<?php
require_once __DIR__ . "/../_includes/_cli_only.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/_includes/init.php";


$Users = $DB->execute("SELECT username FROM users");


foreach ($Users as $User) {
    $Username = $Usernam;
    $Video_Views = $DB->execute("SELECT sum(displayviews) as amount FROM videos WHERE uploaded_by = '$Username'", true)["amount"];

    if (empty($Video_Views)) {
        $Video_Views = 0;
    }
        $Subscriberst = $DB->execute("SELECT users.banned, users.displayname as subscriber, users.avatar FROM subscriptions INNER JOIN users ON subscriptions.subscriber = users.username WHERE subscriptions.subscription = :OWNER AND users.banned = 0", true, [":OWNER" =>$Username]); 
        $Subscriberst = $DB->RowNum;
            $DB->modify("UPDATE users SET subscribers = $Subscriberst WHERE username = '$Username'");
   $Subscriptions = $DB->execute("SELECT users.banned, users.displayname as subscription, users.avatar FROM subscriptions INNER JOIN users ON subscriptions.subscription = users.username WHERE subscriptions.subscriber = :OWNER AND users.banned = 0", true, [":OWNER" => $Username]);
   $Subscriptions = $DB->RowNum;
               $DB->modify("UPDATE users SET subscriptions = $Subscriptions WHERE username = '$Username'");

    $DB->modify("UPDATE users SET video_views = $Video_Views WHERE username = '$Username'");
    
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
        true,
        [":USERNAME" => $Usernam]
    );

   $Friendsc = $DB->RowNum;

    $DB->modify(
        "UPDATE users SET friends = :count WHERE username = :username",
        [
            ":count" => $Friendsc,
            ":username" => $Usernam
        ]
    );
    
}
echo "Done";