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
        $user = Login::GetLoggedInUser();
        if (!$user->isDisabled()) {
            header('Location: index.php');
            die();
        } else {
            Login::LogOut();
            $layout->flash = "Account is disabled.";
        }
    } else { 
        $layout->flash = "Incorrect username or password.";
    }
}

if (isset($_POST['username'])) {
    $view->username = $_POST['username'];
}
$layout->show();
?>
