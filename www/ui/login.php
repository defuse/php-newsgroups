<?php
require_once('ui/view.php');
class LoginView extends View
{
    public $username = "";

    public function show()
    {
?>
    <div class="contentpadding">
    <form action="login.php" method="POST">
        <table class="formalign">
            <tr>
                <td>Username:&nbsp;&nbsp;&nbsp;</td>
                <td><input type="text" name="username" value="<?php echo htmlentities($this->username, ENT_QUOTES); ?>" /></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="password" value="" /></td>
            </tr>
            <tr>
                <td><input type="submit" name="submit" value="Login" /></td>
            </tr>
        </table>
    </form>
    </div>
<?
    }
}
?>
