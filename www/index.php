<?php
require_once('inc/Newsgroup.php');
require_once('ui/layout.php');
require_once('ui/main.php');

$main = new MainView();

$main->setGroupNames(Newsgroup::GetGroupNames());
if (isset($_GET['group'])) {
    $main->setCurrentGroup($_GET['group']);
} else {
    $main->setCurrentGroup(null);
}

$layout = new Layout($main);
$layout->show();
?>
