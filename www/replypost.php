<?php
require_once('inc/Newsgroup.php');

if (isset($_POST['submit'])) {
    try {
        $post = new Post($_POST['replyto']);
        $group = new Newsgroup($post->getGroupName());
        $user = $_POST['user'];
        $title = $_POST['title'];
        $contents = $_POST['contents'];
        $group->replyPost($post->getID(), $user, $title, $contents);
        die('Reply accepted.');
    } catch (PostDoesNotExistException $e) {
        die('Post replying to does not exist.');
    }
}

if (isset($_GET['replyto']) && !empty($_GET['replyto'])) {
    try {
        $post = new Post($_GET['replyto']);
        $replyto = (int)$post->getID();
        $title = $post->getTitle();
        if (strpos($title, "Re:") === 0) {
            $title_guess = $title;
        } else {
            $title_guess = "Re: " . $title;
        }
        $safe_quote = htmlentities($post->getUser() . " said:\n", ENT_QUOTES);
        $quoted = preg_replace('/^/m', "> ", $post->getContents());
        $safe_quote .= htmlentities($quoted, ENT_QUOTES);
    } catch (PostDoesNotExistException $e) {
        die('Post does not exist.');
    }
} else {
    die('Please specify a post to reply to.');
}
?>
<html>
    <head>
        <title>NEW POST: ASDFASDFSDF</title>
    </head>
    <body>
        <form action="replypost.php" method="POST">
            <input type="hidden" name="replyto" value="<?php echo $replyto; ?>" />
            Username: <input type="text" name="user" value="Anonymous" /> <br />
            Subject: <input type="text" name="title" value="<?php echo htmlentities($title_guess, ENT_QUOTES); ?>" /> <br />
            Post body: <br />
            <textarea name="contents" rows="30" cols="80"><?php echo $safe_quote; ?></textarea>
            <br />
            <input type="submit" name="submit" value="Submit" />
        </form>
    </body>
</html>
