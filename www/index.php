<?php
require_once('inc/Newsgroup.php');
require_once('inc/account.php');
require_once('ui/layout.php');
require_once('ui/main.php');

$user = Login::GetLoggedInUser();
$user_class = $user ? $user->getUserClass() : UserClass::Anonymous();

$main = new MainView();

$main->sidebar_groups = $user_class->getVisibleGroups();
if (isset($_GET['group'])) {
    try {
        $group = new Newsgroup($_GET['group']);
        if ($user_class->canReadGroup($group)) {
            $main->setCurrentGroup($group);
        } else {
            $main->setCurrentGroup(null);
        }
        if (isset($_GET['page']) && (int)$_GET['page'] > 0) {
            $main->page = (int)$_GET['page'] - 1;
        } else {
            $main->page = 0;
        }
    } catch (GroupDoesNotExistException $e) {
        $main->setCurrentGroup(null);
    }
} else {
    $main->setCurrentGroup(null);
}

$layout = new Layout($main);
$layout->show();
?>
