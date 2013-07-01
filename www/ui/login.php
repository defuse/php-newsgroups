<?php
require_once('ui/view.php');
class LoginView extends View
{
    private $failed;
    private $username = "";

    public function setFailed($failed)
    {
        $this->failed = $failed;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function show()
    {
?>
    <?php if ($this->failed) { ?>
    <b>Incorrect username or password.</b>
    <?  } ?>
    <form action="login.php" method="POST">
    Username: <input type="text" name="username" value="<?php echo htmlentities($this->username, ENT_QUOTES); ?>" /><br />
        Password: <input type="password" name="password" value="" /> <br />
        <input type="submit" name="submit" value="Login" />
    </form>
<?
    }
}
?>
