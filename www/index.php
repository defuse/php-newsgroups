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
    $main->setCurrentGroup($_GET['group']);
} else {
    $main->setCurrentGroup(null);
}

$layout = new Layout($main);
$layout->show();
?>
