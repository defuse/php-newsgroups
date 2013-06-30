<?php
require_once('inc/Newsgroup.php');

try {
    Newsgroup::CreateGroup("defuse.ring0");
} catch (GroupExistsException $e) {
    echo "Group already exists.";
}

function display_post_tree($post, $indent = 0)
{
    /* display the post itself */
    $safe_title = htmlentities($post->getTitle(), ENT_QUOTES);
    $safe_user = htmlentities($post->getUser(), ENT_QUOTES);
    $safe_indent = htmlentities($indent, ENT_QUOTES);
?>
    <div class="post read i<?php echo $safe_indent; ?>">
        <div class="postmetadata">
            <?php echo $safe_user; ?>
        </div>
        <div class="posttitle">
            <?php echo $safe_title; ?>
        </div>
    </div>
<?
    
    /* recursively display the children */
    $children = $post->getChildren();
    foreach ($children as $child) {
        display_post_tree($child, $indent + 1);
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>PHP Newsgroups</title>
    <style type="text/css">

        body {
            background-color: #CCCCCC;
        }

        #tblcolumns {
            background-color: white;
            border-collapse: collapse;
            border: solid 1px black;
        }

        #grouplist {
            vertical-align: top;
            width: 200px;
            border-right: solid black 1px;
            padding: 10px;
        }

        #grouplistheader {
            font-size: 16pt;
            font-weight: bold;
        }

        #groupcontents {
            vertical-align: top;
            padding: 10px;
            width: 700px;
        }

        #groupcontentsheader {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        #postlisting {
            border-left: solid black 1px;
            border-right: solid black 1px;
            border-bottom: solid black 1px;
            width: 100%;
            padding: 0;
            height: 200px;
            overflow: auto;
        }

        .post {
            border-top: 1px solid black;
            font-size: 10pt;
        }

        .post:hover {
            background-color: #CCCCCC;
        }

        .i0 {
            padding-left: 10px;
        }

        .i1 {
            padding-left: 40px;
        }

        .i2 {
            padding-left: 70px;
        }

        .i3 {
            padding-left: 100px;
        }

        .i4 {
            padding-left: 130px;
        }

        .posttitle {
        }

        .postmetadata {
            float: right;
            margin-right: 10px;
        }

        .clear {
            clear: both;
        }

        .read {
            color: black;
        }

        .unread {
            font-weight: bold;
            color: #505050;
        }

        .expander {
            font-family: monospace;
        }

        .selected {
            background-color: cyan !important;
        }

        #postview {
            margin-top: 20px;
        }

        #postcontents {
            border: solid black 1px;
            font-family: monospace;
            padding: 10px;
        }

        .quote {
            border-left: solid blue 1px;
            margin-left: 10px;
            padding-left: 10px;
            padding-bottom: 5px;
            padding-top: 5px;
            margin-top: 7px;
            margin-bottom: 7px;
        }

        #viewtitle {
            font-size: 12pt;
            font-weight: bold;
        }

    </style>
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
                        echo $safe_name;
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
                <div id="groupcontentsheader">
                    <?php echo htmlentities($group->getName(), ENT_QUOTES); ?>
                </div>

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
                <b>Please select a group.</li>
            <?
                }
            ?>

            </td>
        </tr>
    </table>
</body>
</html>
