<?php
require_once('ui/view.php');
class RegisterView extends View
{
    public function show()
    {
?>
<form action="register.php" method="POST">
    Username: <input type="text" name="username" value="" /> <br />
    Password: <input type="password" name="password" value="" /> <br />
    Confirm Password: <input type="password" name="password_confirm" value="" /> <br />
    <input type="submit" name="submit" value="Create Account" />
</form>
<?
    }
}
?>
