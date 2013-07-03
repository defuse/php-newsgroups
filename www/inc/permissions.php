<?php
require_once('inc/mysql.php');
require_once('inc/settings.php');
require_once('inc/Newsgroup.php');

class UserClassExistsException extends Exception { /* empty */ }
class UserClassDoesNotExistException extends Exception { /* empty */ }
class UserClassIsSpecialException extends Exception { /* empty */ }
class InvalidAbilityException extends Exception { /* empty */ }

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

    public static function Anonymous()
    {
        $anonymous_id = Settings::GetSetting("class.anonymous");
        return new UserClass($anonymous_id);
    }

    public static function GetAllAbilities()
    {
        return array('NOACCESS', 'READONLY', 'READWRITECAPTCHA', 'READWRITE');
    }

    public static function Read($ability)
    {
        return $ability == 'READONLY' || $ability == 'READWRITECAPTCHA' || $ability == 'READWRITE';
    }

    public static function Write($ability)
    {
        return $ability == 'READWRITECAPTCHA' || $ability == 'READWRITE';
    }

    public static function Captcha($ability)
    {
        return $ability == 'READWRITECAPTCHA';
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

    public static function UserClassIdExists($id)
    {
        global $DB;
        $q = $DB->prepare("SELECT id FROM user_classes WHERE id = :id");
        $q->bindValue(':id', $id);
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
        if (!self::UserClassIdExists($id)) {
            throw new UserClassDoesNotExistException("User class with id $id does not exist.");
        }
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

    public function fullDelete()
    {
        global $DB;

        /* Make sure it isn't a 'special' class that can't be deleted. */
        $default_id = Settings::GetSetting("class.default");
        $anonymous_id = Settings::GetSetting("class.anonymous");
        if ($this->id == $default_id || $this->id == $anonymous_id) {
            throw new UserClassIsSpecialException('Cannot delete anonymous or default class.');
        }

        /* Move all users in this class to the default class. */
        $q = $DB->prepare("UPDATE accounts SET user_class = :new WHERE user_class = :old");
        $q->bindValue(':new', $default_id);
        $q->bindValue(':old', $this->id);
        $q->execute();

        /* Delete all permissions regarding this class */
        $q = $DB->prepare("DELETE FROM permissions WHERE class_id = :class_id");
        $q->bindValue(':class_id', $this->id);
        $q->execute();

        /* Delete the actual user class */
        $q = $DB->prepare("DELETE FROM user_classes WHERE id = :id");
        $q->bindValue(':id', $this->id);
        $q->execute();
    }

    public function getAbilityForGroup($group)
    {
        global $DB;

        $q = $DB->prepare("SELECT ability FROM permissions WHERE class_id = :class_id AND group_id = :group_id");
        $q->bindValue(':class_id', $this->id);
        $q->bindValue(':group_id', $group->getID());
        $q->execute();
        $row = $q->fetch();
        if ($row === FALSE) {
            // If the row doesn't exist, we assume NOACCESS (fail safe).
            return 'NOACCESS'; 
        }
        return $row['ability'];
    }

    public function setAbilityForGroup($group, $ability)
    {
        global $DB;

        $all_abilities = self::GetAllAbilities();
        if (!in_array($ability, $all_abilities)) {
            throw new InvalidAbilityException("$ability is not a valid ability.");
        }

        if ($this->abilityForGroupExplicit($group)) {
            $q = $DB->prepare("UPDATE permissions SET ability = :ability WHERE class_id = :class_id AND group_id = :group_id");
            $q->bindValue(':class_id', $this->id);
            $q->bindValue(':group_id', $group->getID());
            $q->bindValue(':ability', $ability);
            $q->execute();
        } else {
            $q = $DB->prepare("INSERT INTO permissions (class_id, group_id, ability)
                               VALUES (:class_id, :group_id, :ability)");
            $q->bindValue(':class_id', $this->id);
            $q->bindValue(':group_id', $group->getID());
            $q->bindValue(':ability', $ability);
            $q->execute();
        }
    }

    public function getVisibleGroups()
    {
        $groups = Newsgroup::GetAllGroups();
        $visible_groups = array();
        foreach ($groups as $group) {
            if ($this->canReadGroup($group)) {
                $visible_groups[] = $group;
            }
        }
        return $visible_groups;
    }

    public function canReadGroup($group)
    {
        return self::Read($this->getAbilityForGroup($group));
    }

    private function abilityForGroupExplicit($group)
    {
        global $DB;

        $q = $DB->prepare("SELECT ability FROM permissions WHERE class_id = :class_id AND group_id = :group_id");
        $q->bindValue(':class_id', $this->id);
        $q->bindValue(':group_id', $group->getID());
        $q->execute();
        $row = $q->fetch();
        return $row !== false;
    }



    /* TODO: permission checking functions in here */
}
?>
