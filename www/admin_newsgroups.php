<?php
require_once('inc/account.php');
require_once('inc/Newsgroup.php');
require_once('ui/layout.php');
require_once('ui/admin/newsgroups.php');
require_once('inc/permissions.php');
require_once('inc/settings.php');
Login::RequireAdmin('index.php');

$view = new AdminNewsgroupView();
$layout = new Layout($view);

if (isset($_POST['newgroup'])) {
    $groupname = $_POST['groupname'];
    try {
        NewsGroup::CreateGroup($groupname, $_POST['anonymous_access']);
        $layout->flash = "Group added.";
    } catch (GroupExistsException $e) {
        $layout->flash = "A group with that name already exists.";
    } catch (InvalidAccessLevelException $e) {
        $layout->flash = "Invalid access level selection.";
    }
}

if (isset($_POST['deletegroup'])) {
    $groupname = $_POST['groupname'];
    $group = new Newsgroup($groupname);
    $group->fullDelete();
    $layout->flash = "Group deleted.";
}

$layout->show();
?>
