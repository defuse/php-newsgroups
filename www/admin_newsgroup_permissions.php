<?php
require_once('ui/admin/newsgroup_permissions.php');
require_once('ui/layout.php');
require_once('inc/permissions.php');
require_once('inc/Newsgroup.php');
Login::RequireAdmin('index.php');

$view = new AdminNewsgroupPermissionsView();
$layout = new Layout($view);

if (isset($_GET['newsgroup'])) {
    try {
        $view->newsgroup = new Newsgroup($_GET['newsgroup']);
    } catch (GroupDoesNotExistException $e) {
        $layout->flash = "Newsgroup does not exist.";
    }
}

if ($view->newsgroup && isset($_POST['set_anonymous_access'])) {
    try {
        $access = $_POST['anonymous_access'];
        AnonymousAccessControl::SetAnonymousAccessToGroup($view->newsgroup, $access);
        $layout->flash = "Anonymous access saved.";
    } catch (InvalidAccessLevelException $e) {
        $layout->flash = "Invalid access level selection.";
    }
}

if ($view->newsgroup && isset($_POST['add_user_group'])) {
    try {
        $name = $_POST['user_group_name'];
        $user_group = UserGroup::GetUserGroupByName($name);
        $user_group->setAccessToNewsgroup($view->newsgroup, 'NOACCESS');
        $layout->flash = "Added.";
    } catch (UserGroupDoesNotExistException $e) {
        $layout->flash = "User group does not exist.";
    }
}

if ($view->newsgroup && isset($_POST['remove_user_group'])) {
    try {
        $user_group = new UserGroup($_POST['user_group_id']);
        $user_group->removeExplicitAccessToNewsgroup($view->newsgroup);
    } catch (UserGroupDoesNotExistException $e) {
        $layout->flash = "User group does not exist.";
    }
}

if ($view->newsgroup && isset($_POST['set_access_level'])) {
    try {
        $user_group = new UserGroup($_POST['user_group_id']);
        $user_group->setAccessToNewsgroup($view->newsgroup, $_POST['access_level']);
    } catch (UserGroupDoesNotExistException $e) {
        $layout->flash = "User group does not exist.";
    } catch (InvalidAccessLevelException $e) {
        $layout->flash = "Invalid access level.";
    }
}

$layout->show();
?>
