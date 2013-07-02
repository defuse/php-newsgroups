<?php
require_once('inc/account.php');
require_once('inc/Newsgroup.php');
require_once('ui/layout.php');
require_once('ui/administration.php');
require_once('inc/permissions.php');
Login::RequireAdmin('index.php');

$view = new AdministrationView();

if (isset($_POST['newgroup'])) {
    $groupname = $_POST['groupname'];
    try {
        NewsGroup::CreateGroup($groupname, $_POST['default_ability']);
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

if (isset($_POST['admin_give'])) {
    $username = $_POST['username'];
    try {
        $user = new Account($username);
        $user->setAdmin(true);
    } catch (UserDoesNotExistException $e) {
        $view->user_noexist = true;
    }
}

if (isset($_POST['admin_revoke'])) {
    $username = $_POST['username'];
    if ($username !== Login::GetLoggedInUser()->getUsername()) {
        try {
            $user = new Account($username);
            $user->setAdmin(false);
        } catch (UserDoesNotExistException $e) {
            $view->user_noexist = true;
        }
    } else {
        $view->user_no_revoke_own = true;
    }
}

if (isset($_POST['new_userclass'])) {
    try {
        UserClass::CreateUserClass($_POST['name'], $_POST['default_ability']);
        /* TODO: sucess message */
    } catch (UserClassExistsException $e) {
        /* TODO */
    }
}

if (isset($_POST['delete_userclass'])) {
    $uc_id = $_POST['id'];
    try {
        $uc = new UserClass($uc_id);
        $uc->delete();
    } catch (UserClassDoesNotExistException $e) {

    } catch (UserClassIsSpecialException $e) {

    }
    /* TODO */
}


$layout = new Layout($view);
$layout->show();
?>
