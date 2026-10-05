<?
function captcha($Num, $Left = 0, $Right = 0, $Permanent = false) {
	$Pos = "";
	if ($Left !== 0) { $Pos .= "left:".$Left."px;"; }
	if ($Right !== 0) { $Pos .= "right:".$Right."px;"; }
	if ($Permanent or $_SESSION["sec_actions"] >= $Num) {
		return '       ';

	} else {
		return false;
	}

}


function check_captcha_sp($Num) {
	return true;
}
