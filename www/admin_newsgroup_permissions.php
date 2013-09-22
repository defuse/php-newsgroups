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

$layout->show();
?>
