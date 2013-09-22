<?php
require_once('inc/mysql.php');
require_once('inc/settings.php');
require_once('inc/Newsgroup.php');

class UserGroupExistsException extends Exception { /* empty */ }
class UserGroupDoesNotExistException extends Exception { /* empty */ }
class InvalidAccessLevelException extends Exception { /* empty */ }
class CannotDeleteDefaultGroupException extends Exception { /* empty */ }

class UserGroup
{
    public static function GetAllUserGroups()
    {
        global $DB;

        $q = $DB->prepare("SELECT id FROM user_groups");
        $q->execute();

        $all_groups = array();
        while (($row = $q->fetch()) !== FALSE) {
            $all_groups[] = new UserGroup($row['id']);
        }
        return $all_groups;
    }

    public static function GetAllUserGroupsForNewsgroup($newsgroup)
    {
        $all_groups = self::GetAllUserGroups();
        $all_groups_for_ng = array();
        foreach ($all_groups as $user_group) {
            if ($user_group->hasExplicitAccessToNewsgroup($newsgroup)) {
                $all_groups_for_ng[] = $user_group;
            }
        }
        return $all_groups_for_ng;
    }

    public static function CreateUserGroup($name)
    {
        global $DB;

        $q = $DB->prepare("SELECT id FROM user_groups WHERE name = :name");
        $q->bindValue(':name', $name);
        $q->execute();

        if ($q->fetch() === FALSE) {
            $q = $DB->prepare("INSERT INTO user_groups (name) VALUES (:name)");
            $q->bindValue(':name', $name);
            $q->execute();
            return TRUE;
        } else {
            throw new UserGroupExistsException("User group $name already exists.");
        }
    }

    public static function GetDefaultGroup()
    {
        return new UserGroup((int)Settings::GetSetting("user_group.default"));
    }

    public static function IsValidAccessLevel($level)
    {
        $access_levels = array(
            'NOACCESS',
            'READONLY',
            'READWRITECAPTCHA',
            'READWRITE'
        );
        return in_array($level, $access_levels, TRUE);
    }

    private $id;

    function __construct($id)
    {
        global $DB;

        $this->id = $id;

        /* Make sure a group with this ID actually exists. */
        $q = $DB->prepare("SELECT id FROM user_groups WHERE id = :id");
        $q->bindValue(':id', $this->id);
        $q->execute();
        if ($q->fetch() === FALSE) {
            throw new UserGroupDoesNotExistException("User group does not exist.");
        }
    }

    public function getName()
    {
        global $DB;
        $q = $DB->prepare("SELECT name FROM user_groups WHERE id = :id");
        $q->bindValue(':id', $this->id);
        $q->execute();
        $row = $q->fetch();
        return $row['name'];
    }

    public function getID()
    {
        return $this->id;
    }

    public function getAllMembers()
    {
        global $DB;

        $q = $DB->prepare(
            "SELECT account_id FROM user_group_membership
             WHERE user_group_id = :user_group_id"
        );
        $q->bindValue(':user_group_id', $this->id);
        $q->execute();

        $users_in_group = array();
        while (($row = $q->fetch()) !== FALSE) {
            $users_in_group[] = Account::GetUserFromId($row['account_id']);
        }
        return $users_in_group;
    }

    public function getNumberOfMembers()
    {
        return count($this->getAllMembers());
    }

    public function isMember($user)
    {
        global $DB;

        $q = $DB->prepare(
            "SELECT account_id FROM user_group_membership
             WHERE account_id = :account_id AND user_group_id = :user_group_id"
        );
        $q->bindValue(':account_id', $user->getID());
        $q->bindValue(':user_group_id', $this->id);
        $q->execute();

        return $q->fetch() !== FALSE;
    }

    public function removeUser($user)
    {
        global $DB;

        $q = $DB->prepare(
            "DELETE FROM user_group_membership
             WHERE account_id = :account_id AND user_group_id = :user_group_id"
        );
        $q->bindValue(':account_id', $user->getID());
        $q->bindValue(':user_group_id', $this->id);
        $q->execute();
    }

    public function addUser($user)
    {
        global $DB;

        if (!$this->isMember($user)) {
            $q = $DB->prepare(
                "INSERT INTO user_group_membership (account_id, user_group_id)
                 VALUES (:account_id, :user_group_id)"
            );
            $q->bindValue(':account_id', $user->getID());
            $q->bindValue(':user_group_id', $this->id);
            $q->execute();
        }
    }

    public function getAccessToNewsgroup($newsgroup)
    {
        global $DB;

        $q = $DB->prepare(
            "SELECT access FROM group_permissions
             WHERE user_group_id = :user_group_id
             AND newsgroup_id = :newsgroup_id"
        );
        $q->bindValue(':user_group_id', $this->id);
        $q->bindValue(':newsgroup_id', $newsgroup->getID());
        $q->execute();

        $row = $q->fetch();

        if ($row === FALSE) {
            return 'NOACCESS';
        } else {
            return $row['access'];
        }
    }

    public function hasExplicitAccessToNewsgroup($newsgroup)
    {
        global $DB;

        $q = $DB->prepare(
            "SELECT access FROM group_permissions
             WHERE user_group_id = :user_group_id
             AND newsgroup_id = :newsgroup_id"
        );
        $q->bindValue(':user_group_id', $this->id);
        $q->bindValue(':newsgroup_id', $newsgroup->getID());
        $q->execute();

        $row = $q->fetch();

        return $row !== FALSE;
    }

    public function setAccessToNewsgroup($newsgroup, $access)
    {
        global $DB;

        if ($this->hasExplicitAccessToNewsgroup($newsgroup)) {
            /* If we have an explicit access setting already, modify the
             * existing row. */
            $q = $DB->prepare(
                "UPDATE group_permissions SET access = :access
                 WHERE user_group_id = :user_group_id
                 AND newsgroup_id = :newsgroup_id"
             );
            $q->bindValue(':access', $access);
            $q->bindValue(':user_group_id', $this->id);
            $q->bindValue(':newsgroup_id', $newsgroup->getID());
            $q->execute();
        } else {
            /* Otherwise, we have to add the row to contain the setting. */
            $q = $DB->prepare(
                "INSERT INTO group_permissions
                 (user_group_id, newsgroup_id, :access)
                 VALUES (:user_group_id, :newsgroup_id, :access)"
            );
            $q->bindValue(':user_group_id', $this->id);
            $q->bindValue(':newsgroup_id', $newsgroup->getID());
            $q->bindValue(':access', $access);
            $q->execute();
        }
    }

    public function removeExplicitAccessToNewsgroup($newsgroup)
    {
        global $DB;

        $q = $DB->prepare(
            "DELETE FROM group_permissions
             WHERE user_group_id = :user_group_id
             AND newsgroup_id = :newsgroup_id"
        );
        $q->bindValue(':user_group_id', $this->id);
        $q->bindValue(':newsgroup_id', $newsgroup->getID());
        $q->execute();
    }

    public function makeDefault()
    {
        Settings::SetSetting("user_group.default", $this->id);
    }

    public function isDefaultUserGroup()
    {
        return (int)Settings::GetSetting("user_group.default") == $this->id;
    }

    public function fullDelete()
    {
        global $DB;

        if ($this->isDefaultUserGroup()) {
            throw new CannotDeleteDefaultGroupException();
        }

        $q = $DB->prepare("DELETE FROM user_groups WHERE id = :id");
        $q->bindValue(':id', $this->id);
        $q->execute();

        $q = $DB->prepare(
            "DELETE FROM user_group_membership
             WHERE user_group_id = :user_group_id"
        );
        $q->bindValue(':user_group_id', $this->id);
        $q->execute();

        $q = $DB->prepare(
            "DELETE FROM group_permissions
             WHERE user_group_id = :user_group_id"
        );
        $q->bindValue(':user_group_id', $this->id);
        $q->execute();
    }

}

interface IAccessControl
{
    public function canReadGroup($newsgroup);
    public function canWriteGroup($newsgroup);
    public function captchaRequiredForGroup($newsgroup);
}

class UserAccessControl
{
    private $account;

    function __construct ($account)
    {
        $this->account = $account;
    }

    public function canReadGroup($newsgroup)
    {
        $access = $this->getAccessToGroup($newsgroup);
        switch ($access) {
            case "READONLY":
            case "READWRITECAPTCHA":
            case "READWRITE":
                return TRUE;
            default:
                return FALSE;
        }
    }

    public function canWriteGroup($newsgroup)
    {
        $access = $this->getAccessToGroup($newsgroup);
        switch ($access) {
            case "READWRITECAPTCHA":
            case "READWRITE":
                return TRUE;
            default:
                return FALSE;
        }

    }

    public function captchaRequiredForGroup($newsgroup)
    {
        $access = $this->getAccessToGroup($newsgroup);
        switch ($access) {
            case "READWRITECAPTCHA":
                return TRUE;
            default:
                return FALSE;
        }
    }

    private function getAccessToGroup($newsgroup)
    {
        global $DB;

        $q = $DB->prepare(
            "SELECT access FROM group_permissions
             INNER JOIN user_group_membership
             ON user_group_membership.user_group_id = group_permissions.user_group_id
             WHERE user_group_membership.account_id = :account_id
             AND group_permissions.newsgroup_id = :newsgroup_id"
         );
        $q->bindValue(':account_id', $account->getID());
        $q->bindValue(':newsgroup_id', $newsgroup->getID());
        $res = $q->execute();

        /* Use the most permissive access out of all the groups they are in. */
        $access = 'NOACCESS';
        while (($row = $q->fetch()) !== FALSE) {
            $access = $this->mostPermissiveAccessOf($access, $row['access']);
        }

        /* Logged-in users always have as much access as anonymous users. */
        $anonymous_access = AnonymousAccessControl::getAnonymousAccessToGroup($newsgroup);
        $access = $this->mostPermissiveAccessOf($access, $anonymous_access);

        return $access;
    }

    private function mostPermissiveAccessOf($access1, $access2)
    {
        $permissive_order = array(
            /* Least permissive. */
            'NOACCESS',
            'READONLY',
            'READWRITECAPTCHA',
            'READWRITE'
            /* Most permissive. */
        );

        $idx1 = array_search($access1, $permissive_order, true);
        $idx2 = array_search($access2, $permissive_order, true);

        if ($idx1 === FALSE || $idx2 == FALSE) {
            throw new InvalidAccessLevelException();
        }

        if ($idx1 < $idx2) {
            return $access1;
        }
        else if ($idx1 > $idx2) {
            return $access2;
        } else {
            return $access1;
        }
    }
}

class AnonymousAccessControl
{
    public function __construct ()
    {

    }

    public function canReadGroup($newsgroup)
    {
        $access = self::GetAnonymousAccessToGroup($newsgroup);
        switch ($access) {
            case "READONLY":
            case "READWRITECAPTCHA":
            case "READWRITE":
                return TRUE;
            default:
                return FALSE;
        }
    }

    public function canWriteGroup($newsgroup)
    {
        $access = self::GetAnonymousAccessToGroup($newsgroup);
        switch ($access) {
            case "READWRITECAPTCHA":
            case "READWRITE":
                return TRUE;
            default:
                return FALSE;
        }
    }

    public function captchaRequiredForGroup($newsgroup)
    {
        $access = self::GetAnonymousAccessToGroup($newsgroup);
        switch ($access) {
            case "READWRITECAPTCHA":
                return TRUE;
            default:
                return FALSE;
        }
    }

    public static function GetAnonymousAccessToGroup($newsgroup)
    {
        global $DB;
        $q = $DB->prepare("SELECT anonymous_access FROM groups WHERE id = :id");
        $q->bindValue(':id', $newsgroup->getID());
        $q->execute();
        $row = $q->fetch();
        return $row['anonymous_access'];
    }
}

?>
