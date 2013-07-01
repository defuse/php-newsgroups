<?php
require_once('inc/account.php');
require_once('ui/layout.php');
require_once('ui/register.php');

if (isset($_POST['submit'])) {
    if ($_POST['password'] == $_POST['password_confirm']) {
        try {
            Account::CreateAccount($_POST['username'], $_POST['password']);
        } catch (Exception $e) {
            echo "<b>Something went wrong. $e</b>";
        }
    } else {
        echo "<b>Passwords do not match.</b>";
    }
}

$view = new RegisterView();
$layout = new Layout($view);
$layout->show();
?>
