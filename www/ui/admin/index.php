<?php
require_once('ui/view.php');
require_once('inc/Newsgroup.php');
require_once('inc/permissions.php');
require_once('inc/settings.php');
class AdministrationView extends View
{
    public function show()
    {
?>
    <div class="contentpadding">
    <ul>
        <li><a href="admin_users.php">Manage users</a></li>
        <li><a href="admin_newsgroups.php">Manage newsgroups</a></li>
        <li><a href="admin_captcha.php">Manage CAPTCHA</a></li>
    </ul>

    </div>
<?
    }
}
?>
