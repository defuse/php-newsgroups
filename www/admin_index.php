<?php
require_once('inc/account.php');
require_once('inc/Newsgroup.php');
require_once('ui/layout.php');
require_once('ui/admin/index.php');
require_once('inc/permissions.php');
require_once('inc/settings.php');
Login::RequireAdmin('index.php');

$view = new AdministrationView();
$layout = new Layout($view);

$layout->show();
?>
