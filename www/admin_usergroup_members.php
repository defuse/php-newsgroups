<?php
require_once('ui/admin/usergroup_members.php');
require_once('ui/layout.php');
require_once('inc/permissions.php');
Login::RequireAdmin('index.php');

$view = new AdminUserGroupMembersView();
$layout = new Layout($view);

if (isset($_GET['usergroup'])) {
    try {
        $view->usergroup = new UserGroup($_GET['usergroup']);
    } catch (UserGroupDoesNotExistException $e) {
        $layout->flash = "User group does not exist.";
    }
} else {
    $layout->flash = "Please select a user group.";
}

if ($view->usergroup && isset($_POST['modify_member'])) {
    $user = Account::GetUserFromId($_POST['user_id']);
    if (isset($_POST['is_member']) && $_POST['is_member'] == "yes") {
        $view->usergroup->addUser($user);
    } else {
        $view->usergroup->removeUser($user);
    }
}

$layout->show();
?>
