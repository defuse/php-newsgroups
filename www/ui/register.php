<?php
require_once('ui/view.php');
require_once('inc/settings.php');
require_once('inc/captcha.php');
class RegisterView extends View
{
    public function show()
    {
?>
<div class="contentpadding">
<form action="register.php" method="POST">
    <table class="formalign">
        <tr>
            <td>Username:</td>
            <td><input type="text" name="username" value="" /></td>
        </tr>
        <tr>
            <td>Password:</td>
            <td><input type="password" name="password" value="" /></td>
        </tr>
        <tr>
            <td>Confirm Password:&nbsp;&nbsp;&nbsp;</td>
            <td><input type="password" name="password_confirm" value="" /></td>
        </tr>
    <?php
        if (Settings::GetSetting('recaptcha.onregister') == "1") {
            echo '<tr><td></td><td>';
            Captcha::ShowCaptcha();
            echo '</td></tr>';
        }
    ?>
        <tr>
            <td><input type="submit" name="submit" value="Create Account" /></td>
        </tr>
    </table>
</form>
</div>
<?
    }
}
?>
