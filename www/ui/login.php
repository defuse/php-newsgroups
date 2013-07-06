<?php
require_once('ui/view.php');
class LoginView extends View
{
    public $username = "";

    public function show()
    {
?>
    <form action="login.php" method="POST">
    Username: <input type="text" name="username" value="<?php echo htmlentities($this->username, ENT_QUOTES); ?>" /><br />
        Password: <input type="password" name="password" value="" /> <br />
        <input type="submit" name="submit" value="Login" />
    </form>
<?
    }
}
?>
