<?php
require_once('inc/Newsgroup.php');
require_once('inc/account.php');
require_once('inc/permissions.php');
require_once('inc/captcha.php');
require_once('ui/layout.php');
require_once('ui/postedit.php');

$user = Login::GetLoggedInUser();
$user_class = $user ? $user->getUserClass() : UserClass::Anonymous();

$view = new PostEditView();
$layout = new Layout($view);

if (isset($_GET['group']) && !empty($_GET['group'])) {
    try {
        $group = new Newsgroup($_GET['group']);
        if (!$user_class->canWriteGroup($group)) {
            // TODO: better way to deal with it -- inform them.
            header('Location: index.php');
            die();
        }
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
    // NB: The write permission check happens above in the $_GET['group'] stuff.
    if ($user_class->captchaForGroup($group) && !Captcha::CheckCaptcha()) {
        $layout->flash = "Incorrect captcha.";
        $view->title = $_POST['title'];
        $view->body_html = htmlentities($_POST['contents'], ENT_QUOTES);
    } else {
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
}

if ($user_class->captchaForGroup($group)) {
    $view->captcha = true;
}

$layout->show();
?>
