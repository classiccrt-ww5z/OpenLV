<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/_includes/_libs/klabs.php');
define('BAPTCHA_API_URL', 'https://');
define('VIP_API_KEY', 'magno');

function kaptcha_validate($key) {
	if (isset($_REQUEST["_BAPTCHA_NOJS"])) {
		$ip = getremoteaddr();
		$check = file_get_contents('https://https:///baptcha/login/vip.php?key='.VIP_API_KEY.'&addr='.$ip);
		if ($ip == $check && (isset($_GET["nojs"]) || isset($_GET["nojscheck"]) || isset($_GET["_BAPTCHA_NOJS"]))) {
    		return true;
		}

		$k = $_REQUEST["_BAPTCHA_KEY"]??false;
	if (!$k) return;
		return stristr(trim(file_get_contents(BAPTCHA_API_URL."?nojscheck&key=&_BAPTCHA=".$k)), "CHECK correct") ? 1 : 0;
	}

	$k = $_REQUEST["_BAPTCHA"]??false;
	if (!$k) return;
	return stristr(trim(file_get_contents(BAPTCHA_API_URL."?_BAPTCHA=".$k."&key=".$key)), "CHECK correct") ? 1 : 0;
}


?>
