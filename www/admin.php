<?php
require_once('inc/account.php');
require_once('inc/Newsgroup.php');
require_once('ui/layout.php');
require_once('ui/administration.php');
Login::RequireAdmin('index.php');

$view = new AdministrationView();

if (isset($_POST['newgroup'])) {
    $groupname = $_POST['groupname'];
    try {
        NewsGroup::CreateGroup($groupname);
        $view->add_success = true;
    } catch (GroupExistsException $e) {
        $view->add_group_exists = true;
    }
}

if (isset($_POST['deletegroup'])) {
    $groupname = $_POST['groupname'];
    try {
        $group = new Newsgroup($groupname);
        $group->fullDelete();
        $view->delete_success = true;
    } catch (GroupDoesNotExistException $e) {
        $view->delete_failed = true;
    }
}


$layout = new Layout($view);
$layout->show();
?>
