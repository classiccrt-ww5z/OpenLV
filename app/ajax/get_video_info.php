<?php
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";
header("Content-Type: application/json", true);

//REQUIREMENTS / PERMISSIONS
//- Requires ($_REQUEST["id"])
if (!isset($_REQUEST["id"])) { exit(); }

if($_SERVER["REQUEST_METHOD"] == "POST") {
$Get_Info = $DB->execute("SELECT v.*, v.displayviews as views, u.displayname FROM videos v, users u WHERE v.url = :URL AND u.username = v.uploaded_by AND v.status='2' AND v.privacy = 0 AND v.banned_uploader = 0 LIMIT 1", true, [":URL" => $_REQUEST["id"]]);

if ($DB->RowNum == 1) {
    $Get_Info["uploaded_on"] = date("M d, Y",strtotime($Get_Info["uploaded_on"]));
    $Get_Info["rating"] = return_ratings($Get_Info,17,17);
    echo json_encode($Get_Info);
} else {
    header('HTTP/1.0 404 Not Found');
    exit();
}

} else {
    
    $Get_Info = $DB->execute(
    "SELECT v.*, v.displayviews as views, u.displayname 
     FROM videos v, users u 
     WHERE v.url = :URL 
       AND u.username = v.uploaded_by 
       AND v.status='2' 
       AND v.privacy = 0 
       AND v.banned_uploader = 0 
     LIMIT 1",
    true,
    [":URL" => $_REQUEST["id"]]
);

if ($DB->RowNum == 1) {
    $data = $Get_Info;
    $data["uploaded_on"] = date("M d, Y", strtotime($data["uploaded_on"]));
    $data["rating"] = return_ratings($data, 17, 17);
    $response = [
        "success" => true,
        "data" => $data
    ];

    echo json_encode($response);

} else {
    header('HTTP/1.0 404 Not Found');
    exit();
}

}