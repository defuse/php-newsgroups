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

if (isset($_POST['newgroup'])) {
    $groupname = $_POST['groupname'];
    try {
        NewsGroup::CreateGroup($groupname, $_POST['default_ability']);
        $layout->flash = "Group added.";
    } catch (GroupExistsException $e) {
        $layout->flash = "A group with that name already exists.";
    }
}

if (isset($_POST['deletegroup'])) {
    $groupname = $_POST['groupname'];
    $group = new Newsgroup($groupname);
    $group->fullDelete();
    $layout->flash = "Group deleted.";
}

if (isset($_POST['admin_give'])) {
    $username = $_POST['username'];
    try {
        $user = new Account($username);
        $user->setAdmin(true);
    } catch (UserDoesNotExistException $e) {
        $layout->flash = "User does not exist.";
    }
}

if (isset($_POST['admin_revoke'])) {
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
    try {
        UserClass::CreateUserClass($_POST['name'], $_POST['default_ability']);
        $layout->flash = "User class added.";
    } catch (UserClassExistsException $e) {
        $layout->flash = "A user class with that name already exists.";
    }
}

if (isset($_POST['delete_userclass'])) {
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

if (isset($_POST['delete_user'])) {
    try {
        $username = $_POST['username'];
        $layout->flash = "The user has been deleted.";
        if ($username !== Login::GetLoggedInUser()->getUsername()) {
            $user = new Account($username);
            $user->delete();
        } else {
            $layout->flash = "You cannot delete yourself.";
        }
    } catch (UserDoesNotExistException $e) {
        /* ignore it, it's gone */
    }
}

if (isset($_POST['save_permissions'])) {
    $all_groups = Newsgroup::GetAllGroups();
    $all_ucs = UserClass::GetAllUserClasses();
    foreach ($all_groups as $group) {
        foreach ($all_ucs as $uc) {
            $select_name = "gu_" . $group->getID() . "_" . $uc->getID();
            $ability = $_POST[$select_name];
            try {
                $uc->setAbilityForGroup($group, $ability);
            } catch (InvalidAbilityException $e) {
                $layout->flash = "Invalid ability.";
            }
        }
    }
    $layout->flash = "Permissions saved.";
}

if (isset($_POST['recaptcha_save'])) {
    Settings::SetSetting('recaptcha.public_key', trim($_POST['recaptcha_public']));
    Settings::SetSetting('recaptcha.private_key', trim($_POST['recaptcha_private']));
    if (isset($_POST['recaptcha_register_enable'])) {
        Settings::SetSetting('recaptcha.onregister', "1");
    } else {
        Settings::SetSetting('recaptcha.onregister', "0");
    }
    $layout->flash = "Captcha settings saved.";
}

$layout->show();
?>
