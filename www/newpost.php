<?php
require_once('inc/Newsgroup.php');
require_once('inc/account.php');
require_once('ui/layout.php');
require_once('ui/postedit.php');

$view = new PostEditView();

if (isset($_GET['group']) && !empty($_GET['group'])) {
    try {
        $group = new Newsgroup($_GET['group']);
        $view->new_post_group = $group;
    } catch (GroupDoesNotExistException $e) {
        die('Group does not exist.');
    }
} else {
    die('Please specify a group.');
}

if (isset($_POST['submit'])) {
    $user = Login::GetLoggedInUser();
    if ($user === false) {
        $username = "";
    } else {
        $username = $user->getUsername();
    }
    $title = $_POST['title'];
    $contents = $_POST['contents'];
    $group->newPost($username, $title, $contents);
    die('Post accepted.');
}

$layout = new Layout($view);
$layout->show();
?>
