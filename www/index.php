<?php
require_once('inc/Newsgroup.php');

function display_post_tree($post, $indent = 0)
{
    /* display the post itself */
    $safe_title = htmlentities($post->getTitle(), ENT_QUOTES);
    $safe_user = htmlentities($post->getUser(), ENT_QUOTES);
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
        display_post_tree($child, $indent + 1);
    }
    if ($indent == 0) {
        echo '</div>';
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>PHP Newsgroups</title>
    <script src="jquery-1.10.1.min.js"></script>
    <script src="newsgroup.js"></script>
    <link rel="stylesheet" media="all" type="text/css" href="style.css" />
</head>
<body>
    <table id="tblcolumns">
        <tr>
            <td id="grouplist">
                <div id="grouplistheader">
                    Groups
                </div>
                <ul>
                <?php
                    $group_names = Newsgroup::GetGroupNames();
                    foreach ($group_names as $name) {
                        $safe_name = htmlentities($name, ENT_QUOTES);
                        echo '<li>';
                        echo '<a href="index.php?group=' . $safe_name . '">';
                        if (isset($_GET['group']) && $_GET['group'] === $name) {
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
                if (isset($_GET['group'])) {
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
                            display_post_tree($post);
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
</body>
</html>
