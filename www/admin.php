<?php
require_once('inc/account.php');
require_once('inc/Newsgroup.php');
require_once('ui/layout.php');
require_once('ui/administration.php');
require_once('inc/permissions.php');
require_once('inc/settings.php');
Login::RequireAdmin('index.php');

$view = new AdministrationView();
$layout = new Layout($view);

$redirect = false;

if (isset($_POST['newgroup'])) {
    $redirect = true;
    $groupname = $_POST['groupname'];
    try {
        NewsGroup::CreateGroup($groupname, $_POST['default_ability']);
        $layout->flash = "Group added.";
    } catch (GroupExistsException $e) {
        $layout->flash = "A group with that name already exists.";
    }
}

if (isset($_POST['deletegroup'])) {
    $redirect = true;
    $groupname = $_POST['groupname'];
    $group = new Newsgroup($groupname);
    $group->fullDelete();
    $layout->flash = "Group deleted.";
}

if (isset($_POST['admin_give'])) {
    $redirect = true;
    $username = $_POST['username'];
    try {
        $user = new Account($username);
        $user->setAdmin(true);
    } catch (UserDoesNotExistException $e) {
        $layout->flash = "User does not exist.";
    }
}

if (isset($_POST['admin_revoke'])) {
    $redirect = true;
    $username = $_POST['username'];
    if ($username !== Login::GetLoggedInUser()->getUsername()) {
        try {
            $user = new Account($username);
            $user->setAdmin(false);
        } catch (UserDoesNotExistException $e) {
            $layout->flash = "User does not exist.";
        }
    } else {
        $layout->flash = "You cannot revoke your own permissions.";
    }
}

if (isset($_POST['new_userclass'])) {
    $redirect = true;
    try {
        UserClass::CreateUserClass($_POST['name'], $_POST['default_ability']);
        $layout->flash = "User class added.";
    } catch (UserClassExistsException $e) {
        $layout->flash = "A user class with that name already exists.";
    }
}

if (isset($_POST['delete_userclass'])) {
    $redirect = true;
    $uc_id = $_POST['id'];
    try {
        $uc = new UserClass($uc_id);
        $uc->fullDelete();
        $layout->flash = "User class deleted.";
    } catch (UserClassIsSpecialException $e) {
        $layout->flash = "The user class you tried to delete is set as the Anonymous or Default user class. Please change those settings first.";
    }
}

if (isset($_POST['special_userclasses'])) {
    $redirect = true;
    try {
        $default_uc = new UserClass((int)$_POST['default_class']);
        $anonymous_uc = new UserClass((int)$_POST['anonymous_class']);

        Settings::SetSetting('class.default', $default_uc->getID());
        Settings::SetSetting('class.anonymous', $anonymous_uc->getID());
        $layout->flash = "Special user classes saved.";
    } catch (UserClassDoesNotExistException $e) {
        $layout->flash = "The user class does not exist.";
    }
}

if (isset($_POST['set_userclass'])) {
    $redirect = true;
    try {
        $uc = new UserClass((int)$_POST['user_class']);
        $user = new Account($_POST['username']);
        $user->setUserClass($uc);
    } catch (UserClassDoesNotExistException $e) {
        $layout->flash = "The user class does not exist.";
    } catch (UserDoesNotExistException $e) {
        $layout->flash = "The user does not exist.";
    }

}

if ($redirect) {
    header('Location: admin.php');
}

$layout->show();
?>
