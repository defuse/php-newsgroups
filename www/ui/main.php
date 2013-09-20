<?php
require_once('ui/view.php');
require_once('inc/Newsgroup.php');

function post_date_asc($p1, $p2)
{
    if ($p1->getTime() > $p2->getTime()) {
        return 1;
    } elseif ($p1->getTime() < $p2->getTime()) {
        return -1;
    } else {
        return 0;
    }
}

function post_date_desc($p1, $p2)
{
    if ($p1->getTime() > $p2->getTime()) {
        return -1;
    } elseif ($p1->getTime() < $p2->getTime()) {
        return 1;
    } else {
        return 0;
    }
}

class MainView
{
    public $page;
    public $user;
    public $current_group;

    public function head()
    {
?>
    <script src="js/jquery-1.10.1.min.js"></script>
    <script src="js/newsgroup.js"></script>
<?
    }

    public function title()
    {
        return "PHP Newsgroups";
    }

    public function show()
    {
?>
        <?php
            if ($this->current_group !== null) {
        ?>
            <input type="hidden" id="groupname" value="<?php echo htmlentities($this->current_group->getName(), ENT_QUOTES); ?>" />
            <input type="hidden" id="grouppagenumber" value="<?php echo htmlentities($this->page + 1, ENT_QUOTES); ?>" />
            <input type="hidden" id="currenttime" value="<?php echo htmlentities(time(), ENT_QUOTES); ?>" />
            <input type="button" class="newpostbutton" value="New Post" />
            <div id="groupcontentsheader">
                <?php echo htmlentities($this->current_group->getName(), ENT_QUOTES); ?>
            </div>
            <div style="clear: both;">

            <div id="postlisting">
                <?php
                    $posts = $this->current_group->getTopLevelPosts($this->page);
                    usort($posts, "post_date_desc");
                    foreach ($posts as $post) {
                        $this->display_post_tree($post);
                    }
                ?>
            </div>
            <div id="pager">
            <?php
                $safe_group = htmlentities($this->current_group->getName(), ENT_QUOTES);

                /* Go back to the first page */
                echo '<span id="pager_first">';
                if ($this->page != 0) {
                    echo '<a href="index.php?group=' . $safe_group . '">&lt;&lt;</a>';
                } else {
                    echo '&lt;&lt;';
                }
                echo '</span>';

                /* Go to the previous page */
                echo '<span id="pager_prev">';
                if ($this->page > 0) {
                    $safe_page = htmlentities($this->page + 1 - 1, ENT_QUOTES);
                    echo '<a href="index.php?group=' . $safe_group . '&page=' . $safe_page
                        . '">&lt;</a>';
                } else {
                    echo '&lt;';
                }
                echo '</span>';

                /* Show the current page nubmer */
                echo '<span id="pager_number">';
                echo htmlentities('Page: ' . ($this->page + 1), ENT_QUOTES);
                echo '</span>';

                /* Go to the next page. */
                echo '<span id="pager_next">';
                $safe_page = htmlentities($this->page + 1 + 1, ENT_QUOTES);
                echo '<a href="index.php?group=' . $safe_group . '&page=' . $safe_page
                    . '">&gt;</a>';
                echo '</span>';
            ?>

            </div>

        <?  } ?>

            <div id="postview">
                <div style="float: right;">
                    <input type="button" class="replybutton" value="Reply"/>
                </div>
                <table id="postattrs">
                <tr><td class="postattr">Subject:&nbsp;&nbsp;&nbsp;</td><td><span class="vp_subject">SUBJECT GOES HERE</span></td>
                <tr><td class="postattr">From:</td><td><span class="vp_user">USER GOES HERE</span></td>
                <tr><td class="postattr">Date:</td><td><span class="vp_date">DATE GOES HERE</span></td>
                </table>
                <div id="postcontents">
                </div>
                <div style="float: right; margin-top: 10px;">
                    <input type="button" class="replybutton" value="Reply"/>
                </div>
                <div style="clear: both;"></div>
            </div>

<?
    }

    private function subpost_unread($post)
    {
        $children = $post->getChildren();

        /* breadth first search */

        /* check the children directly underneath */
        foreach ($children as $child) {
            if (!$this->user->hasReadPost($child)) {
                return TRUE;
            }
        }

        /* check their children recursively */
        foreach ($children as $child) {
            if ($this->subpost_unread($child)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    private function display_post_tree($post, $indent = 0)
    {
        /* display the post itself */
        $safe_title = htmlentities($post->getTitle(), ENT_QUOTES);
        if ($post->getUser() !== "") {
            $safe_user = htmlentities($post->getUser(), ENT_QUOTES);
        } else {
            $safe_user = '<i>Anonymous</i>';
        }
        $safe_id = (int)$post->getID();
        $safe_indent = (int)$indent;
        $children = $post->getChildren();
        usort($children, "post_date_asc");
        $safe_read = "read";
        if ($this->user && !$this->user->hasReadPost($post)) {
            $safe_read = "unread";
        } elseif ($this->user && $indent == 0 && $this->subpost_unread($post)) {
            $safe_read = "subunread";
        }
    ?>
        <div class="post" >
            <input type="hidden" class="postid" value="<?php echo $safe_id; ?>" />
            <input type="hidden" class="postindent" value="<?php echo $safe_indent; ?>" />
            <table class="posttable" cellspacing="0">
                <colgroup>
                    <!-- remember to these in newsgroup.js, too. -->
                    <col style="width: 20px;">
                    <col style="width: auto;">
                    <col style="width: 150px;">
                    <col style="width: 190px;">
                </colgroup>
                <tr>
                    <?php if ($indent == 0 && !empty($children)) { ?>
                        <td class="expander">
                                +
                        </td>
                    <? } else { ?>
                        <td class="expander-dummy">
                            &nbsp;
                        </td>
                    <? } ?>
                    <td class="titlecell <?php echo $safe_read; ?>" >
                        <span class="posttitle" style="padding-left: <?php echo 10 + 30*$safe_indent; ?>px;">
                            <?php echo $safe_title; ?>
                        </span>
                    </td>
                    <td class="metadatauser <?php echo $safe_read; ?>">
                        <?php echo $safe_user; ?>
                    </td>
                    <td class="metadatatime <?php echo $safe_read; ?>">
                        <?php echo htmlentities($post->getFormattedTime(), ENT_QUOTES); ?>
                    </td>
                </tr>
            </table>
        </div>
    <?
        /* recursively display the children */
        if ($indent == 0) {
            echo '<div class="hiddenposts">';
        } else {
            echo '<div class="childposts">';
        }
        foreach ($children as $child) {
            $this->display_post_tree($child, $indent + 1);
        }
        echo '</div>';
    }
}
?>
