<?php
require_once('ui/view.php');
require_once('inc/Newsgroup.php');
require_once('inc/permissions.php');
require_once('inc/settings.php');
class AdminCaptchaView extends View
{
    public function show()
    {
?>
    <div class="contentpadding">

    <h1>CAPTCHA</h1>
    <form action="admin_captcha.php" method="POST">
        <table class="formalign">
        <tr>
            <td>Require CAPTCHA to register.&nbsp;&nbsp;&nbsp;</td>
            <td>
                <?php
                    if (Settings::GetSetting("recaptcha.onregister") == "1") {
                        echo '<input type="checkbox" name="recaptcha_register_enable" checked="checked">';
                    } else {
                        echo '<input type="checkbox" name="recaptcha_register_enable">';
                    }
                ?>
            </td> 
        </tr>
        <tr>
            <td>Recaptcha Public Key:</td>
            <td>
                <input name="recaptcha_public" value="<?php
                    $recaptcha_public = Settings::GetSetting("recaptcha.public_key");
                    if ($recaptcha_public !== FALSE) {
                        echo htmlentities($recaptcha_public, ENT_QUOTES);
                    }
                ?>" />
            </td>
        </tr>
        <tr>
            <td>Recaptcha Private Key:</td>
            <td>
            <input name="recaptcha_private" value="<?php
                $recaptcha_private = Settings::GetSetting("recaptcha.private_key");
                if ($recaptcha_private !== FALSE) {
                    echo htmlentities($recaptcha_private, ENT_QUOTES);
                }
            ?>" />
            </td>
        </tr>
        <tr>
            <td><input type="submit" name="recaptcha_save" value="Save" /></td>
        </tr>
        </table>
    </form>
    </div>
<?
    }
}
?>
