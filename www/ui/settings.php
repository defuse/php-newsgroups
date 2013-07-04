<?php
require_once('ui/view.php');
class SettingsView extends View
{
    public function show()
    {
?>
    <h1>Change Password</h1>
    <form action="settings.php" method="POST">
        Current Password:
        <input type="password" name="old_password" />
        <br />
        New Password: 
        <input type="password" name="new_password" />
        <br />
        Confirm New Password:
        <input type="password" name="confirm_new_password" />
        <br />
        <input type="submit" name="change_password" value="Change Password" />
    </form>
<?
    }
}
?>
