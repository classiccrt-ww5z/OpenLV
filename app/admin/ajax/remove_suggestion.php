<?php
require_once $_SERVER['DOCUMENT_ROOT']."/_includes/init.php";
//REQUIREMENTS / PERMISSIONS
// Must have passed the admin panel password gate, not merely be a moderator.
require_admin_panel();
$count = $DB->execute("SELECT COUNT(*) as amount FROM feature_suggestions WHERE id = :ID", true, [":ID" => $_POST["id"]])["amount"];
if ($count == 0) {
	die("1");
}
$DB->modify("DELETE FROM feature_suggestions WHERE id = :ID", [":ID" => $_POST["id"]]);
if ($DB->RowNum > 0) {
	die("1");
}
