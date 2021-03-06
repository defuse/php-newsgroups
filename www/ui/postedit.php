<?php
require_once('ui/view.php');
require_once('inc/Newsgroup.php');
require_once('inc/captcha.php');
class PostEditView extends View
{
    /* If new post, set this to the Newsgroup object. */
    public $new_post_group = null;
    /* If reply, set this to the Post object. */
    public $in_reply_to = null;

    public $title = "";
    public $body_html = "";

    public $captcha = false;

    public $post_accepted = false;

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
        if ($this->post_accepted) {
            echo '<script type="text/javascript"> window.close(); </script>';
            echo '<strong>Post accepted.</strong>';
            return;
        }

        if ($this->new_post_group) {
            $action = "newpost.php?group=" . $this->new_post_group->getName();
        } else {
            $action = "replypost.php";
        }
?>
        <div class="postedit">
        <form action="<?php echo htmlentities($action, ENT_QUOTES); ?>" method="POST">
            <?php if ($this->in_reply_to) { ?>
                <input type="hidden" name="replyto" value="<?php echo (int)$this->in_reply_to->getID(); ?>" />
            <? } ?>
            Subject: 
            <input type="text" name="title" 
                class="posteditsubject"
                value="<?php echo htmlentities($this->title, ENT_QUOTES); ?>" />
            <br />
            <textarea name="contents" rows="30" cols="80" class="posteditbox"><?php 
                echo $this->body_html;
            ?></textarea> <br />
            <?php
                if ($this->captcha) {
                    Captcha::ShowCaptcha();
                }
            ?>
            <input type="submit" name="submit" value="Submit" />
        </form>
        </div>
<?
    }
}
?>
