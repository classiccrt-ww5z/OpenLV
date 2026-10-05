<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/_includes/_libs/kaptcha_client.php";
try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE,
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false 
        ]
    );
} catch (PDOException $e) { 
    exit("database err");
} 

function decrypt($encryptedData, $dont = true) {
    global $encryption_key;
    $encryptedData = base64_decode($encryptedData);
    $ivSize = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($encryptedData, 0, $ivSize);
    $encrypted = substr($encryptedData, $ivSize);
    $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $encryption_key, OPENSSL_RAW_DATA, $iv);
    if(!$dont) {
    $decrypted = htmlspecialchars($decrypted);
    $decrypted = nl2br($decrypted);
    }
    return $decrypted;
}
function encrypt($data) {
     global $encryption_key;
    if ($encryption_key === '') {
        return '';
    }
    $ivSize = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivSize);
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, OPENSSL_RAW_DATA, $iv);
    $encryptedData = base64_encode($iv . $encrypted);
    return $encryptedData;
}

function decrypt_cookie($data) {
    global $encryption_key;
    if ($encryption_key === '') {
        return false;
    }
    $data = base64_decode($data, true);
    if ($data === false) {
        return false;
    }
    $ivSize = openssl_cipher_iv_length('aes-256-cbc');
    if (strlen($data) <= $ivSize) {
        return false;
    }
    $iv = substr($data, 0, $ivSize);
    $cipher = substr($data, $ivSize);
    $decrypted = openssl_decrypt($cipher, 'aes-256-cbc', $encryption_key, OPENSSL_RAW_DATA, $iv);
    return $decrypted === false ? false : $decrypted;
}

// Signed "remember me" cookie: <username>|<expires>|<hmac-sha256(username|expires)>
// The HMAC means a forged or tampered cookie is rejected even if the ciphertext
// encryption key leaks, and the expiry is baked into the signature so it cannot be
// extended by the client.
function remember_cookie_build($username) {
    global $encryption_key;
    if ($encryption_key === '') {
        return false;
    }
    $expires = time() + (10 * 365 * 24 * 60 * 60);
    $payload = $username . '|' . $expires;
    $signature = hash_hmac('sha256', $payload, $encryption_key);
    return ['value' => $payload . '|' . $signature, 'expires' => $expires];
}

// Returns the username if the cookie is authentic and unexpired, otherwise false.
function remember_cookie_verify($cookie) {
    global $encryption_key;
    if ($encryption_key === '' || !is_string($cookie)) {
        return false;
    }
    $parts = explode('|', $cookie);
    if (count($parts) !== 3) {
        return false;
    }
    list($username, $expires, $signature) = $parts;
    if (!ctype_digit($expires)) {
        return false;
    }
    $expected = hash_hmac('sha256', $username . '|' . $expires, $encryption_key);
    if (!hash_equals($expected, $signature)) {
        return false;
    }
    if ((int)$expires < time()) {
        return false;
    }
    // Usernames are constrained to this character set everywhere they are written.
    if (!preg_match('/^[A-Za-z0-9\-]{1,20}$/', $username)) {
        return false;
    }
    return $username;
}

function remember_cookie_options() {
    $secure = (getenv('SESSION_COOKIE_SECURE') === '1') || !empty($_SERVER['HTTPS']) || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    return [
        'expires'  => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

function genuid($string) {
    if (isset($_COOKIE['remember'])) {
        deluid();
    }
    $cookie = remember_cookie_build($string);
    // No ENCRYPTION_KEY configured: skip issuing the cookie rather than issue a
    // forgeable one. The user simply won't be remembered between visits.
    if ($cookie === false) {
        return false;
    }
    $options = remember_cookie_options();
    $options['expires'] = $cookie['expires'];
    setcookie('remember', $cookie['value'], $options);
    return true;
}

function deluid() {
    $options = remember_cookie_options();
    $options['expires'] = time() - 3600;
    setcookie('remember', '', $options);
    unset($_COOKIE['remember']);
}

function user_ip() {
	$candidates = [
		getenv('HTTP_CF_CONNECTING_IP'),
		getenv('HTTP_CLIENT_IP'),
		getenv('HTTP_X_FORWARDED_FOR'),
		getenv('HTTP_X_FORWARDED'),
		getenv('HTTP_FORWARDED_FOR'),
		getenv('HTTP_FORWARDED'),
		getenv('REMOTE_ADDR')
	];

	// Every one of these headers is attacker controlled, and the raw values used to be
	// stored in users.1st_latest_ip / 2nd_latest_ip and later interpolated into SQL and
	// echoed into HTML. Only ever return something that is a syntactically valid IP.
	foreach ($candidates as $candidate) {
		if (!is_string($candidate) || $candidate === '') {
			continue;
		}
		// X-Forwarded-For may carry a comma separated chain; the client IP is the first entry.
		foreach (explode(',', $candidate) as $ip) {
			$ip = trim($ip);
			if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
				return $ip;
			}
		}
	}

	return '0.0.0.0';
}

function isBotOrNot() {
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
    $botScore = $_SERVER['HTTP_CF_BOT_SCORE'] ?? null;
    $threatScore = $_SERVER['HTTP_CF_THREAT_SCORE'] ?? null;
    $country = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? null;
    $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

    $isBot = false;
    $isVpn = false;
    $isBadUA = false;

    if ($botScore !== null && intval($botScore) < 30) {
        $isBot = true;
    }

    if ($country === "T1") {
        $isVpn = true;
    }

    if ($threatScore !== null && intval($threatScore) > 10) {
        $isVpn = true;
    }

    $badUAList = [
        'python', 'curl', 'wget', 'httpclient', 'java',
        'libwww', 'go-http-client', 'perl', 'php', 'ruby'
    ];

    foreach ($badUAList as $bad) {
        if (strpos($ua, $bad) !== false) {
            $isBadUA = true;
            break;
        }
    }

    return ($isBot || $isVpn || $isBadUA);
}

function in_array_r($needle, $haystack, $strict = false) {
	foreach ($haystack as $item) {
		if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
			return true;
		}
	}

	return false;
}

function showBBcodes($text) {
	$find = array(
		'~\[b\](.*?)\[/b\]~s',
		'~\[i\](.*?)\[/i\]~s',
		'~\[u\](.*?)\[/u\]~s'
	);
	$replace = array(
		'<b>$1</b>',
		'<i>$1</i>',
		'<t style="text-decoration:underline;">$1</t>'
	);
	return preg_replace($find,$replace,$text);
}

function hashtag_search($text) {
	return preg_replace('/(?<!\S)#([0-9a-zA-Z]+)/', '<a href="/results?q=$1">#$1</a>', $text);
}

function process_clickable_length($str) {
	$ex = array_reverse(explode(":", $str[1]));
	$sec = 0;
	
	for($i = 0; $i < count($ex); $i++) {
		$sec += (int)$ex[$i] * pow(60, $i);
	}
	
	return "<a href=\"#t=$sec\" onclick=\"$(window).trigger('hashchange')\">$str[1]</a>";
}

function mention($text) {
	$text = preg_replace_callback('/\b((\d+:){1,2}+\d+)\b/', 'process_clickable_length', $text);
	return preg_replace('/(?<!\S)@([0-9a-zA-Z]+)/', '<a href="/user/$1">@$1</a>', $text);
}

function notification($Message,$Redirect,$Color = "red") {
	$_SESSION["notification"] = $Message;
	// The colour is echoed straight into a style="" attribute by the layout templates,
	// so only allow the literal palette the callers use.
	$_SESSION["n_color"] = in_array($Color, ["red", "green", "blue", "yellow", "orange"], true) ? $Color : "red";

	if ($Redirect != false) {
        redirect($Redirect);
    }
}

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

function isTorRequest() {
	if (isset($_SERVER["HTTP_CF_IPCOUNTRY"]) && $_SERVER["HTTP_CF_IPCOUNTRY"] == "T1") {
		
		return true;
		
	} else {
		
		return false;
		
	}
}

function DoLinks($text){

	return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1">$1</a>', $text);

}

function sql_IN_fix($Array, $useKeys = false) {
    global $conn; 

    $Out = [];

    if (!$useKeys) {
        foreach ($Array as $Value) {
            $Out[] = $conn->quote($Value);
        }
    } else {
        foreach ($Array as $Key => $Value) {
            $Out[] = $conn->quote($Key);
        }
    }

    return implode(',', $Out);
}

function url_parameter($URL,$Parameter) {
	parse_str(parse_url($URL)['query'], $Query);
	return $Query[$Parameter];
}

function get_time_ago($time) {
	$time = time() - strtotime($time);
	$time = ($time < 1)? 1 : $time;
	$tokens = array (
		31536000 => 'year',
		2592000 => 'month',
		604800 => 'week',
		86400 => 'day',
		3600 => 'hour',
		60 => 'minute',
		1 => 'second'
	);

	foreach ($tokens as $unit => $text) {
		if ($time < $unit) continue;
		$numberOfUnits = floor($time / $unit);
		return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s ago':' ago');
	}
	foreach ($tokens as $unit => $text) {
		if ($time < $unit) continue;
		$numberOfUnits = floor($time / $unit);
		return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s ago':' ago');
	}
	foreach ($tokens as $unit => $text) {
		if ($time < $unit) continue;
		$numberOfUnits = floor($time / $unit);
		return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s ago':' ago');
	}
}

function subscribe_button($User,$Is_Subscribed = false,$Blocked = false) {
	global $_USER;

	if ($_USER->logged_in) {
		if ($_USER->username !== $User && !$Blocked) {
			if ($Is_Subscribed) {
				return '<a href="/ajax/df/subscribe?u='.$User.'" class="sub is"><img src="/img/sub_check.png">Subscribed</a>';
			} else {
				return '<a href="/ajax/df/subscribe?u='.$User.'" class="sub"><img src="/img/sub_check.png">Subscribe</a>';
			}
		} else {
			return '<a href="javascript:void(0)" onclick="alert(\'You cannot subscribe to yourself!\')" class="sub"><img src="/img/sub_check.png">Subscribe</a>';
		}
	} elseif (!$Blocked) {
		return '<a href="javascript:void(0)" onclick="alert(\'You must be logged in to subscribe!\')" class="sub"><img src="/img/sub_check.png">Subscribe</a>';
	} else {
		return '<a href="javascript:void(0)" onclick="alert(\'You cannot interact with this user!\')" class="sub"><img src="/img/sub_check.png">Subscribe</a>';
	}
}

function convert_filesize($Bytes,$Format) {
	switch(mb_strtolower($Format)) {
		case "kb" :
			return $Bytes / 1024;
			break;
		case "mb" :
			return $Bytes / 1048576;
			break;
		case "gb" :
			return $Bytes / 1073741824;
			break;
	}
}

function browser_name() {
    $AGENT = $_SERVER['HTTP_USER_AGENT'];

    if (mb_strpos($AGENT,"Chrome") !== false && mb_strpos($AGENT,"OPR") === false) {
        return "Chrome";
    } elseif (mb_strpos($AGENT,"Chrome") !== false && mb_strpos($AGENT, "OPR") !== false) {
        return "Opera";
    } elseif (mb_strpos($AGENT,"Firefox") !== false) {
        return "Firefox";
    } elseif (mb_strpos($AGENT,"Trident") !== false) {
        return "IE";
    } elseif (mb_strpos($AGENT,"Edge") !== false) {
        return "Edge";
    } elseif (mb_strpos($AGENT,"Safari") !== false) {
        return "Safari";
    }
    return "Unknown";
}

function old_show_ratings($Ratings, $width, $height) {
    if (is_array($Ratings)) {
        $Star_1 = $Ratings["1_star"];
        $Star_2 = $Ratings["2_star"];
        $Star_3 = $Ratings["3_star"];
        $Star_4 = $Ratings["4_star"];
        $Star_5 = $Ratings["5_star"];

        $Rating_Num = $Star_1 + $Star_2 + $Star_3 + $Star_4 + $Star_5;

        if ($Rating_Num > 0) {
            $Rating = ($Star_1 + $Star_2 * 2 + $Star_3 * 3 + $Star_4 * 4 + $Star_5 * 5) / $Rating_Num;
        } else {
            $Rating = 0;
        }
    } else {
        $Rating = $Ratings;
    }

    $Full_Stars = floor($Rating);
    $Half_Stars = ($Rating - $Full_Stars) >= 0.5 ? 1 : 0;
    $StarNum = 0;

    for ($x = 0; $x < $Full_Stars; $x++) {
        $StarNum++;
        echo "<img src='/img/full_star.png' width='$width' height='$height'>";
    }

    if ($Half_Stars) {
        $StarNum++;
        echo "<img src='/img/half_star.png' width='$width' height='$height'>";
    }

    while ($StarNum < 5) {
        $StarNum++;
        echo "<img src='/img/no_star.png' width='$width' height='$height'>";
    }
}

function show_ratings($Ratings, $width, $height) {
    if (is_array($Ratings)) {
        $Star_1 = $Ratings["1_star"] / 5;
        $Star_2 = $Ratings["2_star"] / 4;
        $Star_3 = $Ratings["3_star"] / 3;
        $Star_4 = $Ratings["4_star"] / 2;
        $Star_5 = $Ratings["5_star"] / 1;

        $Rating_Num = $Star_1 + $Star_2 + $Star_3 + $Star_4 + $Star_5;

        if ($Rating_Num > 0) {
            $Rating = ($Star_1 + $Star_2 * 2 + $Star_3 * 3 + $Star_4 * 4 + $Star_5 * 5) / $Rating_Num;
        } else {
            $Rating = 0;
        }
    } else {
        $Rating = $Ratings;
    }
     if(empty($_COOKIE["vidlii"]) || ($_COOKIE["vidlii"] != 1)) { 

    if(empty($Rating)) {
        echo "<span class='vw s'><i>no rating</i></span><img style='visibility:hidden;' width='$width' height='$height'>";
        return;
    }
     } else {
         if(empty($Rating)) {
         echo '<img src="/img/no_star.png" width="14" height="13"><img src="/img/no_star.png" width="14" height="13"><img src="/img/no_star.png" width="14" height="13"><img src="/img/no_star.png" width="14" height="13"><img src="/img/no_star.png" width="14" height="13">';
         return;
         }
     }
    $Full_Stars = floor($Rating);
    $Half_Stars = ($Rating - $Full_Stars) >= 0.5 ? 1 : 0;
    $StarNum = 0;

    for ($x = 0; $x < $Full_Stars; $x++) {
        $StarNum++;
        echo "<img src='/img/full_star.png' width='$width' height='$height'>";
    }

    if ($Half_Stars) {
        $StarNum++;
        echo "<img src='/img/half_star.png' width='$width' height='$height'>";
    }

    while ($StarNum < 5) {
        $StarNum++;
        echo "<img src='/img/no_star.png' width='$width' height='$height'>";
    }
}

function return_ratings($Ratings, $width, $height) {
    $Return = "";

    if (is_array($Ratings)) {
        $Star_1 = $Ratings["1_star"];
        $Star_2 = $Ratings["2_star"];
        $Star_3 = $Ratings["3_star"];
        $Star_4 = $Ratings["4_star"];
        $Star_5 = $Ratings["5_star"];

        $Rating_Num = $Star_1 + $Star_2 + $Star_3 + $Star_4 + $Star_5;

        if ($Rating_Num > 0) {
            $Rating = ($Star_1 + $Star_2 * 2 + $Star_3 * 3 + $Star_4 * 4 + $Star_5 * 5) / $Rating_Num;
        } else {
            $Rating = 0;
        }
    } else {
        $Rating = $Ratings;
    }

    $Full_Stars = floor($Rating);
    $Half_Stars = ($Rating - $Full_Stars) >= 0.5 ? 1 : 0;
    $StarNum = 0;

    for ($x = 0; $x < $Full_Stars; $x++) {
        $StarNum++;
        $Return .= "<img src='/img/full_star.png' width='$width' height='$height'>";
    }

    if ($Half_Stars) {
        $StarNum++;
        $Return .= "<img src='/img/half_star.png' width='$width' height='$height'>";
    }

    while ($StarNum < 5) {
        $StarNum++;
        $Return .= "<img src='/img/no_star.png' width='$width' height='$height'>";
    }

    return $Return;
}

include "_libs/Other.php";

function get_time($time) {
	if (!is_numeric($time)) {
		return date("h:i:s A", strtotime($time));
	} else {
		return date("h:i:s A", $time);
	}
}

function random_string($Characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", $Length = 11) {
	$charactersLength = mb_strlen($Characters);
	$randomString = '';
	for ($i = 0; $i < $Length; $i++) {
		$randomString .= $Characters[mt_rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function redirect($url = "") {
	if ($url == "") return;
	
	if(!headers_sent()) {
		header('Location: '.$url);
		exit;
	} else {
		echo "<script>window.location.href='$url'</script>";
		exit;
	}
}

function get_date($date) {
	if (!is_numeric($date)) {
		return date("M d, Y", strtotime($date));
	} else {
		return date("M d, Y", $date);
	}
}

function email_domain($Email) {
	return substr(strrchr($Email, "@"), 1);
}

function video_thumbnail($URL,$LENGTH,$Width,$Height,$Title = NULL) {
	if (!empty($LENGTH) || $LENGTH == "0") { $Length = seconds_to_time((int)$LENGTH); } else { $Length = $LENGTH; }
	if (file_exists("usfi/thmp/$URL.jpg")) { $Thumbnail = "/usfi/thmp/$URL.jpg"; } else { $Thumbnail = "/img/no_th.jpg"; }

	return '<div class="th"><div class="th_t">'.$Length.'</div><a href="/watch?v='.$URL.'"><img class="vid_th" loading="lazy" src="'.$Thumbnail.'" width="'.$Width.'" height="'.$Height.'"></a></div>';
}

function video_thumbnailNoHtml($URL) {
    $path = $_SERVER['DOCUMENT_ROOT'] . "/usfi/thmp/$URL.jpg";
    if (file_exists($path)) {
        return "/usfi/thmp/$URL.jpg";
    } else {
        return "/img/no_th.jpg";
    }
}


function user_avatar($User,$Width,$Height,$Avatar,$Border = "") {
if (!empty($Avatar) && is_string($Avatar) && strpos($Avatar, "u=") !== false) {
    $Avatar = str_replace("u=", "", $Avatar);
    $Folder = "avt";
} else {
    $Upload = false;
    $Folder = "thmp";
}




	if (empty($Avatar) or !file_exists("usfi/$Folder/$Avatar.jpg")) {
		$Avatar = "/img/no.png";
	} else {
		if ($Folder == "avt") {
			$Avatar = "/usfi/avt/$Avatar.jpg";
		} else {
			$Avatar = "/usfi/thmp/$Avatar.jpg";
		}
	}
	return '<a href="/user/'.$User.'"><img src="'.$Avatar.'" width="'.$Width.'" loading="lazy" height="'.$Height.'" class="avt '.$Border.'" alt="'.$User.'"></a>';
}

function user_avatar2($User, $Width, $Height, $Avatar, $Extra_Class = "", $Extra_Attr = "") {
    if (!empty($Avatar) && is_string($Avatar) && strpos($Avatar, "u=") !== false) {
        $Avatar = str_replace("u=", "", $Avatar);
        $Folder = "avt";
    } else {
        $Upload = false;
        $Folder = "thmp";
    }

    if (empty($Avatar) || !file_exists("usfi/$Folder/$Avatar.jpg")) {
        $Avatar = "/img/no.png";
    } else {
        if ($Folder == "avt") {
            $Avatar = "/usfi/avt/$Avatar.jpg";
        } else {
            $Avatar = "/usfi/thmp/$Avatar.jpg";
        }
    }
    return '<a href="/user/'.$User.'"><img src="'.$Avatar.'" width="'.$Width.'" height="'.$Height.'" class="avt2 '.$Extra_Class.'" alt="'.$User.'" '.$Extra_Attr.'></a>';
}


function user_avatar4($Avatar) {
	if (!empty($Avatar) && is_string($Avatar) && strpos($Avatar, "u=") !== false) {
    $Avatar = str_replace("u=", "", $Avatar);
    $Folder = "avt";
} else {
    $Upload = false;
    $Folder = "thmp";
}


	if (empty($Avatar) or !file_exists($_SERVER["DOCUMENT_ROOT"] . "/usfi/$Folder/$Avatar.jpg")) {
		$Avatar = "/img/no.png";
	} else {
		if ($Folder == "avt") {
			$Avatar = "/usfi/avt/$Avatar.jpg";
		} else {
			$Avatar = "/usfi/thmp/$Avatar.jpg";
		}
	}
	return $Avatar;
}


function get_age($Date) {
		return date_diff(date_create($Date), date_create('today'))->y;
	}

	function time_ago($time) {
		$time = time() - strtotime($time);
		$time = ($time < 1)? 1 : $time;
		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second'
		);

		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s ago':' ago');
		}
	}

function colourBrightness($hex, $percent) {
	// Work out if hash given
	$hash = '';
	if (stristr($hex,'#')) {
		$hex = str_replace('#','',$hex);
		$hash = '#';
	}
	/// HEX TO RGB
	$rgb = array(hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2)));
	//// CALCULATE
	for ($i=0; $i<3; $i++) {
		// See if brighter or darker
		if ($percent > 0) {
			// Lighter
			$rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1-$percent));
		} else {
			// Darker
			$positivePercent = $percent - ($percent*2);
			$rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1-$positivePercent));
		}
		// In case rounding up causes us to go to 256
		if ($rgb[$i] > 255) {
			$rgb[$i] = 255;
		}
	}
	//// RBG to Hex
	$hex = '';
	for($i=0; $i < 3; $i++) {
		// Convert the decimal digit to hex
		$hexDigit = dechex($rgb[$i]);
		// Add a leading zero if necessary
		if(strlen($hexDigit) == 1) {
			$hexDigit = "0" . $hexDigit;
		}
		// Append to the hex string
		$hex .= $hexDigit;
	}
	return $hash.$hex;
}


function cut_string($text,$length) {
		if (strlen($text) > $length) {
			return substr($text,0,$length)."...";
		} else {
			return $text;
		}
	}

function subscribe_button2($For,$Blocked = false) {
	global $_USER;
	if ($_USER->logged_in && $Blocked == false) {
		if ($_USER->Is_Activated) {
			if ($_USER->username !== $For) {
				if ($_USER->is_subscribed_to($For)) {
					return '<a href="javascript:void(0)" class="yel_btn sub_button" user="'.$For.'">Unsubscribe</a>';
				} else {
					return '<a href="javascript:void(0)" class="yel_btn sub_button" user="'.$For.'">Subscribe</a>';
				}
			} else {
				return '<a href="javascript:void(0)" class="yel_btn" onclick="alert('."'No need to subscribe to yourself!'".')">Subscribe</a>';
			}
		} else {
			return '<a href="javascript:void(0)" class="yel_btn" onclick="alert('."'Please click the activation link we sent via email to subscribe!'".')">Subscribe</a>';
		}
	} elseif (!$_USER->logged_in) {
		return '<a href="javascript:void(0)" class="yel_btn" onclick="alert('."'You must be logged in to subscribe!'".')">Subscribe</a>';
	} else {
		return '<a href="javascript:void(0)" class="yel_btn" onclick="alert('."'You cannot interact with this user!'".')">Subscribe</a>';
	}
}

function limit_text($text, $length) {
	$length = abs((int)$length);
	if(mb_strlen($text) > $length) {
		$text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1', $text);
	}
	return($text);
}


function check_captcha() {
    $secret = (string)getenv('RECAPTCHA_SECRET');

    if ($secret !== '') {
        $response = $_POST['g-recaptcha-response'] ?? '';
        if (!is_string($response) || $response === '') {
            return false;
        }

        $payload = http_build_query([
            'secret'   => $secret,
            'response' => $response,
            'remoteip' => user_ip(),
        ]);

        $result = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
                'timeout' => 5,
            ],
        ]));

        $decoded = is_string($result) ? json_decode($result, true) : null;
        return is_array($decoded) && !empty($decoded['success']);
    }

    // No reCAPTCHA keys configured. The previous implementation returned true
    // unconditionally, which left the admin panel gate and the login brute force
    // throttle completely open. Enforce a real server side rate limit instead.
    global $DB;
    if (!isset($DB)) {
        return true;
    }

    $window = (int)(getenv('THROTTLE_WINDOW_MINUTES') ?: 30);
    $max    = (int)(getenv('THROTTLE_MAX_FAILURES') ?: 20);

    try {
        $row = $DB->execute(
            "SELECT COUNT(*) AS amount FROM wrong_logins WHERE ip = :IP AND submit_date > DATE_SUB(NOW(), INTERVAL :MINUTES MINUTE)",
            true,
            [":IP" => user_ip(), ":MINUTES" => $window]
        );
    } catch (Exception $e) {
        // If the throttle cannot be evaluated, fail closed rather than wave the request through.
        error_log('check_captcha: throttle lookup failed: ' . $e->getMessage());
        return false;
    }

    return ((int)($row['amount'] ?? 0)) < $max;
}

// Every moderation action must sit behind the same second factor as the admin panel:
// logged in, a moderator, AND having passed the panel password gate. Previously only
// the first two were checked on the AJAX endpoints, so a moderator who had never
// unlocked the panel could still shadowban users, feature videos and dismiss reports.
function require_admin_panel() {
    global $_USER;

    $deny = function ($status) {
        if (!headers_sent()) {
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status, true, $status === 401 ? 401 : 403);
        }
        exit();
    };

    if (!isset($_USER) || !is_object($_USER) || empty($_USER->logged_in)) {
        $deny(401);
    }
    if (empty($_USER->Is_Admin) && empty($_USER->Is_Mod)) {
        $deny(403);
    }
    if (empty($_SESSION['admin_panel'])) {
        $deny(403);
    }
}

function can_view_restricted_videos($target_username = null) {
    global $_USER;
    if (!isset($_USER) || !is_object($_USER) || empty($_USER->logged_in)) {
        return false;
    }
    if (!empty($_USER->Is_Admin) || !empty($_USER->Is_Mod)) {
        return true;
    }
    if ($target_username !== null && $_USER->username === $target_username) {
        return true;
    }
    return false;
}

function previous_page() {
	if (!empty($_SESSION["previous_page"])) {
		return $_SESSION["previous_page"];
	} else {
		return "/";
	}
}

function seconds_to_time($Seconds) {
	$min = intval($Seconds / 60);
	return $min . ':' . str_pad(($Seconds % 60), 2, '0', STR_PAD_LEFT);
}

function return_category($Number) {
	$Categories = array(1 => "Film & Animation", 2 => "Autos & Vehicles", 3 => "Music", 4 => "Pets & Animals", 5 => "Sports", 6 => "Travel & Events", 7 => "Gaming", 8 => "People & Blogs", 9 => "Comedy", 10 => "Entertainment", 11 => "News & Politics", 12 => "Howto & Style", 13 => "Education", 14 => "Science & Technology", 15 => "Nonprofits & Activism");
	return $Categories[$Number];
}

function return_categories() {
	return array(1 => "Film & Animation", 2 => "Autos & Vehicles", 3 => "Music", 4 => "Pets & Animals", 5 => "Sports", 6 => "Travel & Events", 7 => "Gaming", 8 => "People & Blogs", 9 => "Comedy", 10 => "Entertainment", 11 => "News & Politics", 12 => "Howto & Style", 13 => "Education", 14 => "Science & Technology", 15 => "Nonprofits & Activism");
}

function hexToRgb($hex, $alpha = false) {
	$hex      = str_replace('#', '', $hex);
	$length   = strlen($hex);
	$rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
	$rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
	$rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
	if ( $alpha ) {
		$rgb['a'] = $alpha;
	}
	return "rgba(".$rgb["r"].",".$rgb["g"].",".$rgb["b"].",".$rgb["a"].")";
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function getSystemStats() { 
    global $_USER;

    $output = shell_exec("free -b"); 
    $lines = explode("\n", trim($output));
    $totalRAM = $usedRAM = $freeRAM = 0;
    if (isset($lines[1])) {
        $mem = preg_split('/\s+/', $lines[1]);
        $totalRAM = $mem[1];
        $usedRAM = $mem[2];
        $freeRAM = $mem[6];
    }

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $cpuInfo = shell_exec("wmic cpu get name");
        $cpuLoad = shell_exec('wmic cpu get loadpercentage');
        $cpuPerc = $cpuLoad;
    } else {
        $cpuInfo = trim(shell_exec("awk -F': ' '/model name/{print $2; exit}' /proc/cpuinfo"));
        $cpuLoad = shell_exec("top -bn1 | grep 'Cpu(s)'");
        preg_match('/(\d+\.\d+)\s*id/', $cpuLoad, $matches);
        $idle = isset($matches[1]) ? floatval($matches[1]) : 0;
        $cpuPerc = 100 - $idle;
    }

    ob_start();
    ?>
    <tbody>
        <tr valign="top">
            <td>
                <b>System Info</b><br><br>
                <div style="padding-bottom: 5px;"><b>Processor:</b> <?= $cpuInfo ?></div>
                <div style="padding-bottom: 5px;"><b>RAM:</b> <?= formatBytes($totalRAM) ?></div>
                <div style="padding-bottom: 5px;"><b>CPU Usage:</b> <?= $cpuPerc ?>%</div>
                <div style="padding-bottom: 5px;"><b>RAM Usage:</b> <?= formatBytes($usedRAM) ?></div>
                <div style="padding-bottom: 5px;"><b>Available RAM:</b> <?= formatBytes($freeRAM) ?></div>
            </td>
        </tr>
    </tbody>
    <?php
    return ob_get_clean();
}
  function favorited_by2($url, $User): bool {
      global $DB;
            $DB->execute("SELECT url FROM video_favorites WHERE url = :URL AND favorite_by = :USERNAME", false,
                              [
                                  ":URL"       =>$url,
                                  ":USERNAME"  => $User
                              ]);

            if ($DB->RowNum == 1) {
                return true;
            }
            return false;
        }