<?php
require_once "_includes/init.php";
// Set page headers
header("Content-Security-Policy: frame-ancestors 'none'");
header("Cache-Control: max-age=0, no-cache, no-store, must-revalidate");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
header("Pragma: no-cache");

// Check if login is enabled in site settings
$Sign_In = $DB->execute("SELECT value FROM settings WHERE name = 'login'", true)["value"];
if(!$Sign_In) {
    notification("Login is currently disabled!", "/", "red");
}


//REQUIREMENTS / PERMISSIONS
//- Requires Being Not Logged In
if ($_USER->logged_in) {
    redirect("/");
    exit();
}

// Fetched failed login count in the past X minutes
// If it exceeds Y, enforce captcha
// TODO When configuration and everything else is setup, make these values configurable
$failed_logins_minutes = 10;
$failed_logins_attempts = 5;

$failed_logins_res = $DB->execute(
    "SELECT COUNT(*) AS wrong_login_count FROM wrong_logins WHERE (ip = :IP AND submit_date > DATE_SUB(NOW(), INTERVAL :MINUTES MINUTE))",
    true,
    [":IP" => user_ip(), ":MINUTES" => $failed_logins_minutes]
);
$failed_logins = (int) $failed_logins_res["wrong_login_count"];
$require_captcha = $failed_logins > $failed_logins_attempts;

if (isset($_POST["submit_login"])) {

    $_GUMP->validation_rules(array(
        "v_username" => "required|max_len,128",
         "password" => "required|max_len,128"
    ));

    $_GUMP->filter_rules(array(
        "v_username" => "trim",
        "password" => "trim"
    ));

    $Validation = $_GUMP->run($_POST);

    


    $continue = true;

    // Check request validation result
    if (!$Validation) {
        $_PAGE->add_error("Validation failed.");
        $continue = false;
    }

    // Check whether the request is coming from a Tor IP
    // if ($continue && isTorRequest()) {
    //     $_PAGE->add_error("You cannot login while using TOR!");
    //     $continue = false;
    // }

 
     if ($continue && $require_captcha && !check_captcha()) {
        $_PAGE->add_error("Please solve the captcha!");
        $continue = false;
     }

 
    if ($continue) {
        $ipRanges = $DB->execute("SELECT ip_range FROM iprange_bans", false);
      if ($DB->RowNum > 0) {
           foreach ($ipRanges as $ipRange) {
               if (strpos(user_ip(), $ipRange["ip_range"]) === 0) {
                    notification("You cannot log in if you've been banned already!", "/login");
                    exit();
                 }
           }
         }
    }

    if ($continue) {
        // Check credentials
        $Username = $Validation["v_username"];
        $Password = $Validation["password"];

        $Query = $DB->execute("SELECT displayname,username, password, banned FROM users WHERE (displayname = :USERNAME or email = :USERNAME2)", true, [":USERNAME" => $Username, ":USERNAME2" => $Username]);

        if ($DB->RowNum == 1) {
            $Username2 = $Query["displayname"];
            $Username = $Query["username"];
            $Hash = $Query["password"];
            $Banned = $Query["banned"];
            if ($Banned == 0 && password_verify($Password, $Hash)) {
                $_USER->username = (string)$Username;
                if($_POST["remember"]) {
                    genuid($_USER->username);
                }
                if ($_USER->login()) {
                    if (!isset($_GET["activate"])) {
                        redirect(previous_page());
                        exit();
                    } else {
                        redirect("/activate?code=" . $_GET["activate"]);
                        exit();
                    }
                }
            } else {
                $_PAGE->add_error("Your credentials are incorrect!");

                $DB->modify("INSERT INTO wrong_logins (ip, submit_date, channel) VALUES (:IP, NOW(), :CHANNEL)", [":IP" => user_ip(), ":CHANNEL" => $Username]);

                $_SESSION["sec_actions"] += 1;
            }
        } else {
            $_PAGE->add_error("This user doesn't exist!");
            unset($Username);
            $_SESSION["sec_actions"] += 1;
        }
    }
}

$_PAGE->set_variables(array(
    "Page_Title" => "Sign In - LiiVid",
    "Page" => "Login",
    "Page_Type" => "Home",
    "Show_Search" => false
));
require_once "_templates/page_structure.php";
