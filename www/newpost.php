<?php
require_once('inc/Newsgroup.php');
require_once('inc/account.php');
require_once('inc/permissions.php');
require_once('inc/captcha.php');
require_once('ui/layout.php');
require_once('ui/postedit.php');

$user = Login::GetLoggedInUser();

$view = new PostEditView();
$layout = new Layout($view);

if (isset($_GET['group']) && !empty($_GET['group'])) {
    try {
        $group = new Newsgroup($_GET['group']);
        $view->new_post_group = $group;
    } catch (GroupDoesNotExistException $e) {
        header('Location: index.php');
        die();
    }
} else {
    header('Location: index.php');
    die();
}

if (isset($_POST['submit'])) {
    if ($user === false) {
        $username = "";
    } else {
        $username = $user->getUsername();
    }
    $title = $_POST['title'];
    $contents = $_POST['contents'];
    $group->newPost($username, $title, $contents);
    $view->post_accepted = true;
}

$layout->show();
?>
