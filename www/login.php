<?php
require_once('inc/account.php');
require_once('ui/layout.php');
require_once('ui/login.php');

$failed = FALSE;
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (Login::TryLogin($username, $password)) {
        header('Location: index.php');
        die();
    } else { 
        $failed = TRUE;
    }
}

$view = new LoginView();
$view->setFailed($failed);
if (isset($_POST['username'])) {
    $view->setUsername($_POST['username']);
}
$layout = new Layout($view);
$layout->show();
?>
