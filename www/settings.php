<?php
require_once('inc/account.php');
require_once('ui/settings.php');
require_once('ui/layout.php');

Login::RequireLogin('index.php');
$user = Login::GetLoggedInUser();

$view = new SettingsView();
$layout = new Layout($view);

if (isset($_POST['change_password'])) {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $new_confirm = $_POST['confirm_new_password'];

    if ($new_confirm == $new) {
        if (Account::CheckPassword($user->getUsername(), $old)) {
            $user->setPassword($new);
            $layout->flash = "Password changed.";
        } else {
            $layout->flash = "Current password is incorrect.";
        }
    } else {
        $layout->flash = "Passwords do not match.";
    }
}

$layout->show();
?>
