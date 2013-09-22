<?php
require_once('ui/view.php');
require_once('inc/account.php');
class HeaderView extends View
{
    public function show()
    {
        $current_user = Login::GetLoggedInUser();
        if ($current_user === FALSE) {
            ?>
                <a href="login.php">Log In</a> | 
                <a href="register.php">Register</a>
            <?
            } else {
            ?>
                <a href="settings.php">Settings</a>
                <?php if ($current_user->isAdmin()) { ?>
                    | <a href="admin_index.php">Administration</a>
                <? } ?>
                <div style="float: right;">
                You are logged in as <b><?php echo $current_user->getUsername(); ?></b>.
                <a href="logout.php">Log out</a>.
                </div>
            <?
        }
    }
}
?>
