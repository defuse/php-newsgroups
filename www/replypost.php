<?php
require_once('inc/Newsgroup.php');
require_once('ui/layout.php');
require_once('ui/postedit.php');

$user = Login::GetLoggedInUser();
$access = Login::GetEffectiveAccessControl();

$view = new PostEditView();
$layout = new Layout($view);

if (isset($_GET['replyto']) && !empty($_GET['replyto'])) {
    try {
        $post = new Post($_GET['replyto']);
        $group = $post->getGroup();
        if (!$access->canWriteGroup($group)) {
            // TODO: better way to deal with it, inform them.
            header('Location: index.php');
            die();
        }
        $view->in_reply_to = $post;

        /* Subject line */
        $title = $post->getTitle();
        if (strpos($title, "Re:") === 0) {
            $title_guess = $title;
        } else {
            $title_guess = "Re: " . $title;
        }
        $view->title = $title_guess;

        /* Post body */
        $user = $post->getUser();
        if ($user === "") {
            $user = "Anonymous";
        }
        $date = $post->getFormattedTime();
        $safe_quote = htmlentities("On $date, $user wrote:\n", ENT_QUOTES);
        $quoted = preg_replace('/^/m', "> ", $post->getContents());
        $safe_quote .= htmlentities($quoted, ENT_QUOTES);
        $view->body_html = $safe_quote . "\n";

    } catch (PostDoesNotExistException $e) {
        header('Location: index.php');
        die();
    }
} elseif (!isset($_POST['submit'])) {
    header('Location: index.php');
    die();
}

if (isset($_POST['submit'])) {
    try {
        $post = new Post($_POST['replyto']);
        $group = $post->getGroup();
        if (!$access->canWriteGroup($group)) {
            header('Location: index.php');
            die();
        }
        if ($access->captchaRequiredForGroup($group) && !Captcha::CheckCaptcha()) {
            $layout->flash = "Incorrect captcha.";
            $view->in_reply_to = $post;
            $view->title = $_POST['title'];
            $view->body_html = htmlentities($_POST['contents'], ENT_QUOTES);
        } else {
            $user = Login::GetLoggedInUser();
            if ($user === false) {
                $username = "";
            } else {
                $username = $user->getUsername();
            }
            $title = $_POST['title'];
            $contents = $_POST['contents'];
            $group->replyPost($post->getID(), $username, $title, $contents);
            $view->post_accepted = true;
        }
    } catch (PostDoesNotExistException $e) {
        die('Post replying to does not exist.');
    }
}


// NB: both the GET and POST handlers above MUST set $group
if ($access->captchaRequiredForGroup($group)) {
    $view->captcha = true;
}

$layout->show();
?>
