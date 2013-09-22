<?php
require_once('ui/admin/newsgroup_permissions.php');
require_once('ui/layout.php');
require_once('inc/permissions.php');
require_once('inc/Newsgroup.php');

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

$layout->show();
?>
