<?php
require_once('inc/account.php');
require_once('inc/Newsgroup.php');
require_once('ui/layout.php');
require_once('ui/admin/captcha.php');
require_once('inc/permissions.php');
require_once('inc/settings.php');
Login::RequireAdmin('index.php');

$view = new AdminCaptchaView();
$layout = new Layout($view);

if (isset($_POST['recaptcha_save'])) {
    Settings::SetSetting('recaptcha.public_key', trim($_POST['recaptcha_public']));
    Settings::SetSetting('recaptcha.private_key', trim($_POST['recaptcha_private']));
    if (isset($_POST['recaptcha_register_enable'])) {
        Settings::SetSetting('recaptcha.onregister', "1");
    } else {
        Settings::SetSetting('recaptcha.onregister', "0");
    }
    $layout->flash = "Captcha settings saved.";
}

$layout->show();
?>
