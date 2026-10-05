<?php
$current_page = basename($_SERVER['SCRIPT_NAME'], '.php');

// Session cookie hardening. `secure` is enabled automatically on HTTPS and can be
// forced on behind a TLS-terminating proxy with SESSION_COOKIE_SECURE=1.
$__secure_cookie = (getenv('SESSION_COOKIE_SECURE') === '1') || !empty($_SERVER['HTTPS']) || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', $__secure_cookie ? 1 : 0);

// Secret used to sign the "remember me" cookie. MUST be set via the ENCRYPTION_KEY
// environment variable. When it is empty the remember-me feature is disabled
// entirely (fail closed) rather than falling back to a well-known key, which would
// let anyone who has read this source forge a login cookie for any account.
$encryption_key = (string)getenv('ENCRYPTION_KEY');
if ($encryption_key === '') {
    $encryption_key = '';
}
header("Content-Security-Policy: frame-ancestors 'none'");
header("Cache-Control: max-age=0, no-cache, no-store, must-revalidate");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
header("Pragma: no-cache");
date_default_timezone_set('Europe/Berlin');

//CONSTANTS
define("ROOT_FOLDER", $_SERVER["DOCUMENT_ROOT"]);
require_once ROOT_FOLDER . "/vendor/autoload.php";


define("UPLOAD_LIMIT", 1024 * 1024 * 1024 * 5);
define("ALLOWED_FORMATS", ["FLV", "MP4", "WMV", "AVI", "MOV", "M4V", "MPG", "MPEG", "WEBM", "MOV", "MKV", "3GP"]);

define("ADMIN_PASSWORD", getenv('ADMIN_PASSWORD') ?: "adminpass");

define("DB_HOST", getenv('DB_HOST') ?: "localhost");
define("DB_DATABASE", getenv('DB_DATABASE') ?: "db");
define("DB_USER", getenv('DB_USER') ?:"db");
define("DB_PASSWORD", getenv('DB_PASSWORD') ?:"db");
define("DB_CHARSET", getenv('DB_CHARSET') ?: "utf8mb4");
require_once ROOT_FOLDER . "/_includes/PHPfunctions.php";

$maintenance = $conn->query("SELECT name, value FROM settings WHERE name = 'maintenance' AND value = 1")->fetch();$maintenance = true;

//if($maintenance && empty($_COOKIE["key"]) && basename($_SERVER['SCRIPT_NAME']) !== 'converter.php') exit(require_once($_SERVER["DOCUMENT_ROOT"]."/_templates/_errors/maintenance.php"));
define("CSS_FILE", getenv('CSS_FILE') ?: "/css/mNew4.css?33");
define("PROFILE_CSS_FILE", getenv('PROFILE_CSS_FILE') ?: "/css/profile.css?14");
define("PROFILE2_CSS_FILE", getenv('PROFILE2_CSS_FILE') ?: "/css/profile2.css?5");
define("COSMIC_CSS_FILE", getenv('COSMIC_CSS_FILE') ?: "/css/cosmicpanda.css?6");
define("PROFILE_JS_FILE", getenv('PROFILE_JS_FILE') ?: "/js/profileNew.js?22");
define("COSMIC_JS_FILE", getenv('COSMIC_JS_FILE') ?: "/js/cosmicpanda.js?3");
define("MAIN_JS_FILE", getenv('MAIN_JS_FILE') ?: "/js/mainNew.js?74");


//AUTOLOAD CLASSES
spl_autoload_register(function ($class) {
    include ROOT_FOLDER . "/_includes/_classes/" . $class . ".class.php"; });

//ini_set( 'session.cookie_httponly', 1 );
session_start(["cookie_lifetime" => 0, "gc_maxlifetime" => 455800]);

if (!isset($_SESSION["sec_actions"])) {
    $_SESSION["sec_actions"] = 0;
}

if (isset($_COOKIE["css"]) && $_COOKIE["css"] == "deleted") {
    setcookie("css", "", -1);
}

//SETUP CLASSES
$DB = new Database(true);
$_USER = new User(NULL, $DB, true);
$_PAGE = new Page();
$_GUMP = new GUMP();
$_THEMES = new Themes($DB, $_USER);

if ($_USER->logged_in && (!isset($_SERVER["HTTP_X_REQUESTED_WITH"]) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')) {
    require_once ROOT_FOLDER . "/_includes/inbox.php";
}

error_reporting(0);

//LiiVid Player Script/CSS Version
$VLPVERSION = round(mt_rand(0, 10000));$LOGO_VALUE = $DB->execute("SELECT value FROM settings WHERE name = 'logo'", true)["value"] ?? 0;

if ($LOGO_VALUE == "0") {
    $LOGO_VALUE = "img/LiiVid7.png";
} else {
    $LOGO_VALUE = "img/$LOGO_VALUE.png";
}if(empty($_COOKIE["vidlii"]) || ($_COOKIE["vidlii"] != 1)) {

    
} else {
$LOGO_VALUE = "img/Vidlii6.png";
}

if (!isset($_SESSION["secret_id"])) {

    $_SESSION["secret_id"] = random_string("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 14);

}

if(!$_USER->logged_in) {
    // Hash IP address to generate secret
$ip_hash = base64_encode(md5(user_ip()));
// Create secret from a small piece offset by date
$login_secret = substr($ip_hash, (int) date('d'), 3);
}

$_SESSION["ten2020Holiday"] = date("d", time());

//Load for traffic regulation
if ($_SESSION["ten2020Holiday"] > 20) {
    $_SESSION["ten2020Holiday"] = "Today";
}

$Top_text=$DB->execute("SELECT value FROM settings WHERE name='top_text'", true)["value"];

require_once "en.php";