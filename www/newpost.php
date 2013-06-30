<?php
require_once('inc/Newsgroup.php');

if (isset($_GET['group']) && !empty($_GET['group'])) {
    try {
        $group = new Newsgroup($_GET['group']);
    } catch (GroupDoesNotExistException $e) {
        die('Group does not exist.');
    }
} else {
    die('Please specify a group.');
}

if (isset($_POST['submit'])) {
    $user = $_POST['user'];
    $title = $_POST['title'];
    $contents = $_POST['contents'];
    $group->newPost($user, $title, $contents);
    die('Post accepted.');
}

$safe_gname = htmlentities($group->getName(), ENT_QUOTES);

?>
<html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>New post to <?php echo $safe_gname; ?></title>
</head>
<body>
    <form action="newpost.php?group=<?php echo $safe_gname; ?>" method="POST">
        Username: <input type="text" name="user" value="Anonymous" /> <br />
        Subject: <input type="text" name="title" value="Your Subject..." /> <br />
        Post body: <br />
        <textarea name="contents" rows="30" cols="80">Type here...</textarea>
        <br />
        <input type="submit" name="submit" value="Submit" />
    </form>
</body>
</html>
