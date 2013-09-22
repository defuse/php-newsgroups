<?php
require_once('inc/Newsgroup.php');
require_once('inc/account.php');
require_once('ui/layout.php');
require_once('ui/main.php');

$user = Login::GetLoggedInUser();
$access = Login::GetEffectiveAccessControl();

$main = new MainView();
$layout = new Layout($main);
$main->user = $user;

if (isset($_GET['group'])) {
    try {
        $group = new Newsgroup($_GET['group']);
        if ($access->canReadGroup($group)) {
            $main->current_group = $group;
            $layout->current_group = $group;
        } else {
            $main->current_group = null;
            $layout->current_group = null;
        }
        if (isset($_GET['page']) && (int)$_GET['page'] > 0) {
            $main->page = (int)$_GET['page'] - 1;
        } else {
            $main->page = 0;
        }
    } catch (GroupDoesNotExistException $e) {
        $main->current_group = null;
        $layout->flash = "Please select a group.";
    }
} else {
    $main->current_group = null;
    $layout->flash = "Please select a group.";
}

$layout->show();
?>
