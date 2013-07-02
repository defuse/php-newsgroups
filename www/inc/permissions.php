<?php
require_once('inc/mysql.php');
require_once('inc/settings.php');
require_once('inc/Newsgroup.php');

class UserClassExistsException extends Exception { /* empty */ }
class UserClassDoesNotExistException extends Exception { /* empty */ }

class UserClass
{
    public static function GetDefaultUserClass()
    {
        $default_class_id = Settings::GetSetting('class.default');
        if ($default_class_id === null) {
            throw new Exception('Default user class is not configured.');
        }
        return new UserClass((int)$default_class_id);
    }

    public static function GetAllUserClasses()
    {
        global $DB;

        $q = $DB->prepare("SELECT id FROM user_classes");
        $q->execute();

        $user_clases = array();
        while (($row = $q->fetch()) !== false) {
            $user_classes[] = new UserClass($row['id']);
        }
        return $user_classes;
    }

    public static function GetAllAbilities()
    {
        return array('NOACCESS', 'READONLY', 'READWRITECAPTCHA', 'READWRITE');
    }

    public static function UserClassExists($name)
    {
        global $DB;
        $q = $DB->prepare("SELECT id FROM user_classes WHERE name = :name");
        $q->bindValue(':name', $name);
        $q->execute();
        $row = $q->fetch();
        return $row !== FALSE;
    }

    public static function CreateUserClass($name, $default_ability)
    {
        global $DB;

        if (self::UserClassExists($name)) {
            throw new UserClassExistsException("User class '$name' exists.");
        }

        /* Create the user class */
        $q = $DB->prepare("INSERT INTO user_classes (name) VALUES (:name)");
        $q->bindValue(':name', $name);
        $q->execute();
        $uc_id = $DB->lastInsertId();

        /* Create permissions for all groups */
        $groups = Newsgroup::GetAllGroups();
        foreach ($groups as $group) {
            $q = $DB->prepare(
                "INSERT INTO permissions (class_id, group_id, ability)
                 VALUES (:class_id, :group_id, :ability)"
             );
            $q->bindValue(':class_id', $uc_id);
            $q->bindValue(':group_id', $group->getID());
            $q->bindValue(':ability', $default_ability);
            $q->execute();
        }
    }

    private $id;

    function __construct($id)
    {
        // TODO: check that it exists
        $this->id = $id;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getName()
    {
        global $DB;
        $q = $DB->prepare("SELECT name FROM user_classes WHERE id = :id");
        $q->bindValue(':id', $this->id);
        $q->execute();
        $row = $q->fetch();
        return $row['name'];
    }

    /* TODO: permission checking functions in here */
}
?>
