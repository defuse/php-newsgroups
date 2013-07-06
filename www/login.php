<?php
require_once('inc/account.php');
require_once('ui/layout.php');
require_once('ui/login.php');

$view = new LoginView();
$layout = new Layout($view);

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (Login::TryLogin($username, $password)) {
        header('Location: index.php');
        die();
    } else { 
        $layout->flash = "Incorrect username or password.";
    }
}

if (isset($_POST['username'])) {
    $view->username = $_POST['username'];
}
$layout->show();
?>
