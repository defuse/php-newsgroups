<?php

require_once('inc/mysql.php');
require_once('inc/permissions.php');

class GroupExistsException extends Exception { /* empty */ }
class GroupDoesNotExistException extends Exception { /* empty */ }
class PostDoesNotExistException extends Exception { /* empty */ }

class Post
{
    private $id;
    private $group_id;
    private $user;
    private $post_date;
    private $title;
    private $contents;

    function __construct($id)
    {
        global $DB;

        $q = $DB->prepare(
            "SELECT * FROM posts WHERE id = :id"
        );
        $q->bindValue(':id', $id);
        $q->execute();
        $row = $q->fetch();
        if ($row === FALSE) {
            throw new PostDoesNotExistException("Post with id $id does not exist.");
        }

        $this->id = $id;
        $this->group_id = $row['group_id'];
        $this->user = $row['user'];
        $this->post_date = $row['post_date'];
        $this->title = $row['title'];
        $this->contents = $row['contents'];
    }

    function getGroup()
    {
        global $DB;

        $q = $DB->prepare(
            "SELECT name FROM groups WHERE id = :id"
        );
        $q->bindValue(':id', $this->group_id);
        $q->execute();
        $row = $q->fetch();
        return new Newsgroup($row['name']);
    }

    function getID()
    {
        return $this->id;
    }

    function getUser()
    {
        return $this->user;
    }

    function getTime()
    {
        return $this->post_date;
    }

    function getTitle()
    {
        return $this->title;
    }

    function getContents()
    {
        return $this->contents;
    }

    function getContentsHtml()
    {
        $html = "";
        $lines = explode("\n", $this->contents);
        $indent = 0;
        foreach ($lines as $line) {
            $leading_brackets = $this->stripLeadingBrackets($line);
            if ($indent < $leading_brackets) {
                while ($indent < $leading_brackets) {
                    $html .= '<div class="quote">';
                    $indent++;
                }
            } else if ($indent > $leading_brackets) { 
                while ($indent > $leading_brackets) {
                    $html .= "</div>";
                    $indent--;
                }
            }
            $html .= htmlentities($line, ENT_QUOTES) . '<br />';
        }
        return $html;
    }

    function stripLeadingBrackets(&$str)
    {
        $count = 0;
        $last_index = -1;
        for ($i = 0; $i < strlen($str); $i++) {
            if ($str[$i] !== ">" && $str[$i] !== " " && $str[$i] !== "\t") {
                break;
            }
            if ($str[$i] === ">") {
                $last_index = $i;
                $count++;
            }
        }
        $str = substr($str, $last_index + 1);
        return $count;
    }

    function getChildren()
    {
        global $DB;
        $q = $DB->prepare(
            "SELECT child_id FROM replies WHERE parent_id = :parent_id"
        );
        $q->bindValue(':parent_id', $this->id);
        $q->execute();
        $children = array();
        while (($row = $q->fetch()) !== FALSE) {
            $children[] = new Post($row['child_id']);
        }
        return $children;
    }

}

class Newsgroup
{
    private $group_id;
    private $group_name;
    private $root_post_id;

    function __construct($group_name)
    {
        global $DB;

        $q = $DB->prepare("SELECT id, root_post_id FROM groups WHERE name = :name");
        $q->bindValue(':name', $group_name);
        $q->execute();

        $result = $q->fetch();
        if ($result === FALSE) {
            throw new GroupDoesNotExistException('Group $group_name does not exist.');
        }

        $this->group_id = $result['id'];
        $this->group_name = $group_name;
        $this->root_post_id = $result['root_post_id'];
    }

    public function getID()
    {
        return $this->group_id;
    }

    public function getName()
    {
        return $this->group_name;
    }

    public function newPost($user, $title, $contents)
    {
        $this->replyPost($this->root_post_id, $user, $title, $contents);
    }

    public function replyPost($parent_id, $user, $title, $contents)
    {
        global $DB;

        /* Insert the post */
        $q = $DB->prepare(
            "INSERT INTO posts (group_id, user, post_date, title, contents)
             VALUES (:group_id, :user, :post_date, :title, :contents)"
         );
        $q->bindValue(':group_id', $this->group_id);
        $q->bindValue(':user', $user);
        $q->bindValue(':post_date', time());
        $q->bindValue(':title', $title);
        $q->bindValue(':contents', $contents);
        $q->execute();

        $new_post_id = $DB->lastInsertId();

        /* Link it to its parent */
        $q = $DB->prepare(
            "INSERT INTO replies (parent_id, child_id)
             VALUES (:parent_id, :child_id)"
        );
        $q->bindValue(':parent_id', $parent_id);
        $q->bindValue(':child_id', $new_post_id);
        $q->execute();
    }

    public function getTopLevelPosts()
    {
        $root = new Post($this->root_post_id);
        return $root->getChildren();
    }

    public function fullDelete()
    {
        global $DB;

        $this->deletePostAndReplies($this->root_post_id);

        $q = $DB->prepare("DELETE FROM permissions WHERE group_id = :group_id");
        $q->bindValue(':group_id', $this->group_id);
        $q->execute();

        $q = $DB->prepare("DELETE FROM groups WHERE id = :id");
        $q->bindValue(':id', $this->group_id);
        $q->execute();
    }

    private function deletePostAndReplies($id)
    {
        global $DB;

        $q = $DB->prepare("SELECT child_id FROM replies WHERE parent_id = :parent_id");
        $q->bindValue(':parent_id', $id);
        $q->execute();

        while (($row = $q->fetch()) !== FALSE) {
            $this->deletePostAndReplies($row['child_id']);
        }

        $q = $DB->prepare("DELETE FROM posts WHERE id = :id");
        $q->bindValue(':id', $id);
        $q->execute();

        $q = $DB->prepare("DELETE FROM replies WHERE parent_id = :parent_id");
        $q->bindValue(':parent_id', $id);
        $q->execute();
    }

    public static function CreateGroup($group_name, $default_ability)
    {
        global $DB;

        if (self::GroupExists($group_name)) {
            throw new GroupExistsException("Group $group_name already exists.");
        }

        /* Create the root post */
        $q = $DB->prepare(
            "INSERT INTO posts (user, post_date, title)
            VALUES (:user, :post_date, :title)"
        );
        $q->bindValue(':user', 'SYSTEM');
        $q->bindValue(':post_date', time());
        $q->bindValue(':title', "This is the root-level post for $group_name.");
        $res = $q->execute();

        /* Get the id of the just-created root post */
        $root_post_id = $DB->lastInsertId();

        /* Create the group */
        $q = $DB->prepare(
            "INSERT INTO groups (name, root_post_id)
            VALUES (:name, :root_post_id)
        ");
        $q->bindValue(':name', $group_name);
        $q->bindValue(':root_post_id', $root_post_id);
        $q->execute();

        /* Get the id of the just-created group */
        $group_name_id = $DB->lastInsertId();

        /* Fix the root post (we didn't add the group id before). */
        $q = $DB->prepare(
            "UPDATE posts SET group_id = :group_id WHERE id = :id"
        );
        $q->bindValue(':group_id', $group_name_id);
        $q->bindValue(':id', $root_post_id);
        $q->execute();

        /* Create the permissions for all user classes */
        $user_classes = UserClass::GetAllUserClasses();
        foreach ($user_classes as $uc) {
            $q = $DB->prepare(
                "INSERT INTO permissions (class_id, group_id, ability)
                 VALUES (:class_id, :group_id, :ability)"
             );
            $q->bindValue(':class_id', $uc->getID());
            $q->bindValue(':group_id', $group_name_id);
            $q->bindValue(':ability', $default_ability);
            $q->execute();
        }
    }

    public static function GroupExists($group_name)
    {
        global $DB;

        $q = $DB->prepare(
            "SELECT id FROM groups WHERE name = :name"
        );
        $q->bindValue(':name', $group_name);
        $q->execute();

        return $q->fetch() !== FALSE;
    }

    /* TODO: deprecate this, or make it private, use GetAllGroups */
    public static function GetGroupNames()
    {
        global $DB;

        $q = $DB->prepare(
            "SELECT name FROM groups"
        );
        $q->execute();
        $names = array();
        while (($row = $q->fetch()) !== FALSE) {
            $names[] = $row['name'];
        }
        return $names;
    }

    public static function GetAllGroups()
    {
        $names = self::GetGroupNames();
        $groups = array();
        foreach ($names as $name) {
            $groups[] = new Newsgroup($name);
        }
        return $groups;
    }

}

?>
