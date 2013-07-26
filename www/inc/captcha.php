<?php

require_once('libs/recaptchalib.php');
require_once('inc/settings.php');

class Captcha
{
    public static function ShowCaptcha()
    {
        $public_key = Settings::GetSetting('recaptcha.public_key');
        echo recaptcha_get_html($public_key, null, true);
    }

    public static function CheckCaptcha()
    {
        $private_key = Settings::GetSetting('recaptcha.private_key');
        $resp = recaptcha_check_answer(
            $private_key,
            $_SERVER['REMOTE_ADDR'],
            $_POST['recaptcha_challenge_field'],
            $_POST['recaptcha_response_field']
        );
        if ($resp->is_valid) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

?>
