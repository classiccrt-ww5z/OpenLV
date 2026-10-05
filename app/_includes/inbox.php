<?php  
error_reporting(0);

$Invites = $DB->execute("
    SELECT COUNT(f.id) AS amount
    FROM friends f
    INNER JOIN users u1 ON u1.username = f.friend_1
    INNER JOIN users u2 ON u2.username = f.friend_2
    WHERE (f.friend_1 = :U1 OR f.friend_2 = :U2)
      AND f.status = 0
      AND f.seen = 0
      AND f.by_user <> :U3
      AND u1.banned = 0
      AND u2.banned = 0
", true, [
    ":U1" => $_USER->username,
    ":U2" => $_USER->username,
    ":U3" => $_USER->username,
]);

$Messages = $DB->execute("
    SELECT COUNT(pm.id) AS amount
    FROM private_messages pm
    INNER JOIN users u ON u.username = pm.from_user
    WHERE pm.to_user = :M1
      AND pm.seen = 0
      AND u.banned = 0
", true, [
    ":M1" => $_USER->username,
]);


$Responses = $DB->execute("
    SELECT COUNT(vr.id) AS amount
    FROM video_responses vr
    INNER JOIN videos v ON vr.url_response = v.url
    INNER JOIN users u ON u.username = v.uploaded_by
    WHERE vr.accepted = 0
      AND vr.response_user = :R1
      AND vr.seen = 0
      AND u.banned = 0
", true, [
    ":R1" => $_USER->username,
]);


$Comments = $DB->execute("
    SELECT COUNT(*) AS amount FROM (
        -- Video comments on user's videos
        SELECT vc.id
        FROM video_comments vc
        INNER JOIN videos v ON vc.url = v.url
        INNER JOIN users u ON v.uploaded_by = u.username
        WHERE v.uploaded_by = :C1
          AND vc.by_user <> :C2
          AND vc.seen = 0
          AND vc.reply_to = 0
          AND u.banned = 0

        UNION ALL

        -- Mentions in video comments
        SELECT m.type
        FROM mentions m
        INNER JOIN video_comments vc ON vc.id = m.video
        INNER JOIN videos v ON v.url = vc.url
        INNER JOIN users u ON m.username = u.username
        WHERE m.username = :C3
          AND m.seen = 0
          AND u.banned = 0

        UNION ALL

        -- Mentions in channel comments
        SELECT m.type
        FROM mentions m
        INNER JOIN channel_comments cc ON cc.id = m.channel
        INNER JOIN users u ON m.username = u.username
        WHERE m.username = :C4
          AND m.seen = 0
          AND u.banned = 0

        UNION ALL

        -- Replies to user
        SELECT r.id
        FROM replies r
        INNER JOIN video_comments vc ON vc.id = r.id
        INNER JOIN videos v ON v.url = vc.url
        INNER JOIN users u ON r.for_user = u.username
        WHERE r.for_user = :C5
          AND r.seen = 0
          AND u.banned = 0

        UNION ALL

        -- Comments on user's channel
        SELECT cc.id
        FROM channel_comments cc
        INNER JOIN users u ON cc.on_channel = u.username
        WHERE cc.on_channel = :C6
          AND cc.by_user <> :C7
          AND cc.seen = 0
          AND u.banned = 0
    ) AS combined
", true, [
    ":C1" => $_USER->username,
    ":C2" => $_USER->username,
    ":C3" => $_USER->username,
    ":C4" => $_USER->username,
    ":C5" => $_USER->username,
    ":C6" => $_USER->username,
    ":C7" => $_USER->username,
]);

$Inbox_Amounts["messages"]  = $Messages["amount"];
$Inbox_Amounts["comments"]  = $Comments["amount"];
$Inbox_Amounts["invites"]   = $Invites["amount"];
$Inbox_Amounts["responses"] = $Responses["amount"];

$Inbox_Amount = 
    $Inbox_Amounts["messages"] +
    $Inbox_Amounts["comments"] +
    $Inbox_Amounts["invites"] +
    $Inbox_Amounts["responses"];

unset($Invites, $Comments, $Messages, $Responses);
