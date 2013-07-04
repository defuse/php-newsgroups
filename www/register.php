<?php
require_once('inc/account.php');
require_once('inc/settings.php');
require_once('inc/captcha.php');
require_once('ui/layout.php');
require_once('ui/register.php');

$view = new RegisterView();
$layout = new Layout($view);

if (isset($_POST['submit'])) {
    $captcha_good = true;
    if (Settings::GetSetting('recaptcha.onregister') == "1") {
        if (!Captcha::CheckCaptcha()) {
            $layout->flash = "Incorrect captcha.";
            $captcha_good = false;
        }
    }
    if ($captcha_good) {
        if ($_POST['password'] == $_POST['password_confirm']) {
            try {
                Account::CreateAccount($_POST['username'], $_POST['password']);
                $layout->flash = "Account was successfully created. You may now log in.";
            } catch (Exception $e) {
                $layout->flash = "Something went wrong.";
            }
        } else {
            $layout->flash = "The two passwords you provided do not match.";
        }
    }
}

$layout->show();
?>
