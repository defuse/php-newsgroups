<?php
require_once('ui/view.php');
class SettingsView extends View
{
    public function show()
    {
?>
    <div class="contentpadding">
    <div id="othercontentsheader">
        Change Password
    </div>
    <form action="settings.php" method="POST">
        <table class="formalign">
            <tr>
                <td>Current Password:</td>
                <td><input type="password" name="old_password" /></td>
            </tr>
            <tr>
                <td>New Password:</td>
                <td><input type="password" name="new_password" /></td>
            </tr>
            <tr>
                <td>Confirm New Password:&nbsp;&nbsp;&nbsp;</td>
                <td><input type="password" name="confirm_new_password" /></td>
            </tr>
            <tr>
                <td>
                <input type="submit" name="change_password" value="Change Password" />
                </td>
            </tr>
        </table>
    </form>
    </div>
<?
    }
}
?>
