<?php
require_once('ui/admin/usergroups.php');
require_once('ui/layout.php');
require_once('inc/permissions.php');
Login::RequireAdmin('index.php');

$view = new AdminUserGroupsView();
$layout = new Layout($view);

if (isset($_POST['add_usergroup'])) {
    $name = $_POST['usergroup_name'];
    try {
        UserGroup::CreateUserGroup($name);
        $layout->flash = "User group added.";
    } catch (UserGroupExistsException $e) {
        $layout->flash = "A user group with that name already exists.";
    }
}

if (isset($_POST['del_usergroup'])) {
    try {
        $id = $_POST['usergroup_id'];
        $usergroup = new UserGroup($id);
        $usergroup->fullDelete();
        $layout->flash = "User group deleted.";
    } catch (CannotDeleteDefaultGroupException $e) {
        $layout->flash = $usergroup->getName() .
                         " can not be deleted while it is the default group.";
    } catch (UserGroupDoesNotExistException $e) {
        $layout->flash = "User group does not exist.";
    }
}

if (isset($_POST['set_default_usergroup'])) {
    try {
        $id = $_POST['default_group_id'];
        $usergroup = new UserGroup($id);
        $usergroup->makeDefault();
        $layout->flash = "Default user group set.";
    } catch (UserGroupDoesNotExistException $e) {
        $layout->flash = "User group does not exist.";
    }
}

$layout->show();
?>
