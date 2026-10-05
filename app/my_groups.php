<?php
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";
exit();
if (!$_USER->logged_in) { header("location: /login"); exit(); }
error_reporting(E_ALL);
$_PAGINATION = new Pagination(10,10);

$DB->execute("SELECT groups.id FROM groups INNER JOIN groups_members ON groups.id = groups_members.group_id WHERE groups_members.member = :USERNAME", false, [":USERNAME" => $_USER->username]);
$_PAGINATION->Total = $DB->RowNum;


$Groups = $DB->execute("SELECT groups.* FROM groups INNER JOIN groups_members ON groups.id = groups_members.group_id WHERE groups_members.member = :USERNAME ORDER BY groups.creation_date DESC LIMIT $_PAGINATION->From, $_PAGINATION->To", false, [":USERNAME" => $_USER->username]);


$_PAGE->set_variables(array(
    "Page_Title" => "My Groups - LiiVid",
    "Page" => "my_groups",
    "Page_Type" => "Community",
    "Show_Search" => false
));
require_once "_templates/page_structure.php";
