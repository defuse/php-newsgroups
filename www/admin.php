<?php
require_once('inc/account.php');
require_once('ui/layout.php');
require_once('ui/administration.php');
Login::RequireAdmin('index.php');
$view = new AdministrationView();
$layout = new Layout($view);
$layout->show();
?>
