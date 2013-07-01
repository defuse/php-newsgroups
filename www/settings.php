<?php
require_once('inc/account.php');
require_once('ui/settings.php');
require_once('ui/layout.php');

Login::RequireLogin('index.php');

$view = new SettingsView();
$layout = new Layout($view);
$layout->show();
?>
