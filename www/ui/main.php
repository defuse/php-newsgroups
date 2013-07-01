<?php
require_once('ui/view.php');
require_once('inc/Newsgroup.php');
class MainView
{
    private $group_names;
    private $current_group;

    public function setGroupNames($names_array)
    {
        $this->group_names = $names_array;
    }

    public function setCurrentGroup($current_group)
    {
        $this->current_group = $current_group;
    }

    public function head()
    {
?>
    <script src="jquery-1.10.1.min.js"></script>
    <script src="newsgroup.js"></script>
<?
    }

    public function title()
    {
        return "PHP Newsgroups";
    }

    public function show()
    {
?>
<table id="tblcolumns">
    <tr>
        <td id="grouplist">
            <div id="grouplistheader">
                Groups
            </div>
            <ul>
            <?php
                foreach ($this->group_names as $name) {
                    $safe_name = htmlentities($name, ENT_QUOTES);
                    echo '<li>';
                    echo '<a href="index.php?group=' . $safe_name . '">';
                    if ($this->current_group === $name) {
                        echo '<b>' . $safe_name . '</b>';
                    } else  {
                        echo $safe_name;
                    }
                    echo '</a>';
                    echo '</li>';
                }
            ?>
            </ul>
        </td>
        <td id="groupcontents">
        <?php
            $group = null;
            if ($this->current_group !== null) {
                try {
                    $group = new Newsgroup($_GET['group']);
                } catch (GroupDoesNotExistException $e) {
                    echo "The group does not exist.";
                }
            }

            if ($group !== null) {
        ?>
            <input type="hidden" id="groupname" value="<?php echo
            htmlentities($group->getName(), ENT_QUOTES); ?>" />
            <input type="button" class="newpostbutton" value="New Post" />
            <div id="groupcontentsheader">
                <?php echo htmlentities($group->getName(), ENT_QUOTES); ?>
            </div>
            <div style="clear: both;">

            <div id="postlisting">
                <?php
                    $posts = $group->getTopLevelPosts();
                    foreach ($posts as $post) {
                        $this->display_post_tree($post);
                    }
                ?>
            </div>

        <?
            } else {
        ?>
            <b>Please select a group.</b></li>
        <?
            }
        ?>

            <div id="postview">
                <div style="float: right;">
                    <input type="button" class="replybutton" value="Reply"/>
                </div>
                From: <span class="vp_user">USER GOES HERE</span> <br />
                Date: <span class="vp_date">DATE GOES HERE</span> <br /><br />
                <div id="postcontents">
                </div>
                <div style="float: right; margin-top: 10px;">
                    <input type="button" class="replybutton" value="Reply"/>
                </div>
                <div style="clear: both;"></div>
            </div>

        </td>
    </tr>
</table>
<?
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
    ?>
        <div class="post read" >
            <input type="hidden" class="postid" value="<?php echo $safe_id; ?>" />
            <table class="posttable" cellspacing="0">
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
                    <td class="titlecell" style="padding-left: <?php echo 10 + 30*$safe_indent; ?>px;">
                        <span class="posttitle">
                            <?php echo $safe_title; ?>
                        </span>
                    </td>
                    <td class="metadatacell">
                        <?php echo $safe_user; ?>
                    </td>
                </tr>
            </table>
        </div>
    <?
        /* recursively display the children */
        if ($indent == 0) {
            echo '<div class="hiddenposts">';
        }
        foreach ($children as $child) {
            $this->display_post_tree($child, $indent + 1);
        }
        if ($indent == 0) {
            echo '</div>';
        }
    }
}
?>
