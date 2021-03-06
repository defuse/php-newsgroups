<?php
require_once('inc/account.php');
require_once('inc/Newsgroup.php');
require_once('ui/layout.php');
require_once('ui/admin/users.php');
require_once('inc/permissions.php');
require_once('inc/settings.php');
Login::RequireAdmin('index.php');

$view = new AdminUserView();
$layout = new Layout($view);

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

if (isset($_POST['disable'])) {
    $username = $_POST['username'];
    try {
        $user = new Account($username);
        $user->setDisabled(true);
    } catch (UserDoesNotExistException $e) {
        $layout->flash = "User does not exist.";
    }
}

if (isset($_POST['enable'])) {
    $username = $_POST['username'];
    try {
        $user = new Account($username);
        $user->setDisabled(false);
    } catch (UserDoesNotExistException $e) {
        $layout->flash = "User does not exist.";
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

if (isset($_POST['reset_password'])) {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    try {
        $user = new Account($username);
        if (empty($new_password)) {
            $layout->flash = "New password cannot be empty.";
        } else {
            $user->setPassword($new_password);
            $layout->flash = "Password changed.";
        }
    } catch (UserDoesNotExistException $e) {
        $layout->flash = "User does not exist.";
    }
}

$layout->show();
?>
