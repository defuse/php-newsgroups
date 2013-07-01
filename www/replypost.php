<?php
require_once('inc/Newsgroup.php');
require_once('ui/layout.php');
require_once('ui/postedit.php');

$view = new PostEditView();

if (isset($_POST['submit'])) {
    try {
        $post = new Post($_POST['replyto']);
        $group = new Newsgroup($post->getGroupName());
        $user = Login::GetLoggedInUser();
        if ($user === false) {
            $username = "";
        } else {
            $username = $user->getUsername();
        }
        $title = $_POST['title'];
        $contents = $_POST['contents'];
        $group->replyPost($post->getID(), $username, $title, $contents);
        die('Reply accepted.');
    } catch (PostDoesNotExistException $e) {
        die('Post replying to does not exist.');
    }
}

if (isset($_GET['replyto']) && !empty($_GET['replyto'])) {
    try {
        $post = new Post($_GET['replyto']);
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
        $safe_quote = htmlentities("$user said:\n", ENT_QUOTES);
        $quoted = preg_replace('/^/m', "> ", $post->getContents());
        $safe_quote .= htmlentities($quoted, ENT_QUOTES);
        $view->body_html = $safe_quote;

    } catch (PostDoesNotExistException $e) {
        die('Post does not exist.');
    }
} else {
    die('Please specify a post to reply to.');
}

$layout = new Layout($view);
$layout->show();
?>
