<?php
require_once('inc/account.php');

if (isset($_POST['submit'])) {
    if ($_POST['password'] == $_POST['password_confirm']) {
        try {
            Account::CreateAccount($_POST['username'], $_POST['email'], $_POST['password']);
        } catch (Exception $e) {
            echo "<b>Something went wrong. $e</b>";
        }
    } else {
        echo "<b>Passwords do not match.</b>";
    }
}

?>
<html>
<head>
</head>
<body>
    <form action="register.php" method="POST">
        Name: <input type="text" name="username" value="" /> <br />
        Email: <input type="text" name="email" value="" /> <br />
        Password: <input type="password" name="password" value="" /> <br />
        Confirm Password: <input type="password" name="password_confirm" value="" /> <br />
        <input type="submit" name="submit" value="Create Account" />
    </form>
</body>

