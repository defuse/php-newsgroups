<?php
require_once('ui/view.php');
require_once('inc/Newsgroup.php');
class PostEditView extends View
{
    /* If new post, set this to the Newsgroup object. */
    public $new_post_group = null;
    /* If reply, set this to the Post object. */
    public $in_reply_to = null;

    public $title = "";
    public $body_html = "";

    public function title()
    {
        if ($this->new_post_group !== null) {
            return "New Post to " . $this->new_post_group->getName();
        } else {
            return "New Reply";
        }
    }

    public function show()
    {
        if ($this->new_post_group) {
            $action = "newpost.php?group=" . $this->new_post_group->getName();
        } else {
            $action = "replypost.php";
        }
?>
        <form action="<?php echo htmlentities($action, ENT_QUOTES); ?>" method="POST">
            <?php if ($this->in_reply_to) { ?>
                <input type="hidden" name="replyto" value="<?php echo (int)$this->in_reply_to->getID(); ?>" />
            <? } ?>
            Subject: 
            <input type="text" name="title" 
                value="<?php echo htmlentities($this->title, ENT_QUOTES); ?>" />
            <br />
            Post body: <br />
            <textarea name="contents" rows="30" cols="80"><?php 
                echo $this->body_html;
            ?></textarea> <br />
            <input type="submit" name="submit" value="Submit" />
        </form>
<?
    }
}
?>
