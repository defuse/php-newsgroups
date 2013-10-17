<?php

require_once('inc/mysql.php');
require_once('inc/PasswordHash.php');
require_once('inc/permissions.php');

class UserExistsException extends Exception { /* empty */ }
class UserDoesNotExistException extends Exception { /* empty */ }

class Login
{
    public static function TryLogin($username, $password)
    {
        self::StartSession();
        session_regenerate_id(true);
        if (Account::CheckPassword($username, $password)) {
            $account = new Account($username);
            $_SESSION['account'] = $account;
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public static function GetLoggedInUser()
    {
        self::StartSession();
        if (isset($_SESSION['account'])) {
            return $_SESSION['account'];
        } else {
            return FALSE;
        }
    }

    public static function GetEffectiveAccessControl()
    {
        $user = self::GetLoggedInUser();
        if ($user) {
            return new UserAccessControl($user);
        } else {
            return new AnonymousAccessControl();
        }
    }

    public static function RequireLogin($location)
    {
        $current_user = Login::GetLoggedInUser();
        if ($current_user === FALSE) {
            header("Location: $location");
            die();
        }
    }

    public static function RequireAdmin($location)
    {
        $current_user = Login::GetLoggedInUser();
        if ($current_user === FALSE || !$current_user->isAdmin()) {
            header("Location: $location");
            die();
        }
    }

    public static function LogOut()
    {
        // Explicitly remove the account in the session in case the stuff
        // below fails for whatever reason.
        if (isset($_SESSION['account'])) {
            $_SESSION['account'] = null;
        }

        self::StartSession();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    private static function StartSession()
    {
        static $started = FALSE;
        if ($started === FALSE) {
            session_start();
            $started = TRUE;
        } 
    }
}

class Account
{
    public static function CreateAccount($username, $password)
    {
        global $DB;
        if ($username == "") {
            throw new Exception('Empty username.');
        }

        if (!self::UserExists($username)) {
            $q = $DB->prepare(
                "INSERT INTO accounts 
                (username, password_hash, is_admin) 
                VALUES (:username, :password_hash, :is_admin)"
            );
            $q->bindValue(':username', $username);
            $q->bindValue(':password_hash', create_hash($password));
            $q->bindValue(':is_admin', 0);
            $q->execute();

            /* Add the new account to the default user group. */
            $user = new Account($username);
            $default_user_group = UserGroup::GetDefaultGroup();
            $default_user_group->addUser($user);

        } else {
            throw new Exception('User already exists');
        }
    }

    public static function UserExists($username)
    {
        global $DB;
        $q = $DB->prepare("SELECT id FROM accounts WHERE username = :username");
        $q->bindValue(':username', $username);
        $q->execute();
        $records = $q->fetchAll();
        return !empty($records);
    }

    /* TODO: make the constructor take the ID */
    public static function GetUserFromId($id)
    {
        global $DB;

        $q = $DB->prepare("SELECT username FROM accounts WHERE id = :id");
        $q->bindValue(':id', $id);
        $q->execute();
        $row = $q->fetch();
        if ($row === FALSE) {
            throw new UserDoesNotExistException();
        }
        return new Account($row['username']);
    }

    public static function CheckPassword($username, $password)
    {
        global $DB;

        $q = $DB->prepare("SELECT password_hash FROM accounts WHERE username = :username");
        $q->bindValue(':username', $username);
        $q->execute();
        $row = $q->fetch();
        if ($row === FALSE) {
            return FALSE;
        }

        $correct_hash = $row['password_hash'];
        if (!validate_password($password, $correct_hash)) {
            return FALSE;
        }

        return TRUE;
    }

    public static function GetAllUsers()
    {
        global $DB;

        $q = $DB->prepare("SELECT username FROM accounts");
        $q->execute();
        $all_users = array();
        while (($row = $q->fetch()) !== FALSE) {
            $all_users[] = new Account($row['username']);
        }
        return $all_users;
    }

    private $id;
    private $username;

    function __construct($username)
    {
        global $DB;

        $q = $DB->prepare("SELECT * FROM accounts WHERE username = :username");
        $q->bindValue(':username', $username);
        $q->execute();
        $row = $q->fetch();

        if ($row === FALSE) {
            throw new UserDoesNotExistException('User does not exist.');
        }

        $this->id = $row['id'];
        $this->username = $row['username'];
    }

    function getID()
    {
        return $this->id;
    }

    function getUsername()
    {
        return $this->username;
    }

    function isAdmin()
    {
        global $DB;

        $q = $DB->prepare("SELECT is_admin FROM accounts WHERE id = :id");
        $q->bindValue(':id', $this->id);
        $q->execute();
        $row = $q->fetch();

        return $row['is_admin'] == 1;
    }

    function setAdmin($is_admin)
    {
        $is_admin = ($is_admin) ? 1 : 0;
        global $DB;

        $q = $DB->prepare("UPDATE accounts SET is_admin = :is_admin WHERE id = :id");
        $q->bindValue(':is_admin', $is_admin);
        $q->bindValue(':id', $this->id);
        $q->execute();
    }

    function isDisabled()
    {
        global $DB;

        $q = $DB->prepare("SELECT is_disabled FROM accounts WHERE id = :id");
        $q->bindValue(':id', $this->id);
        $q->execute();
        $row = $q->fetch();

        return $row['is_disabled'] == 1;
    }

    function setDisabled($is_disabled)
    {
        $is_disabled = ($is_disabled) ? 1 : 0;
        global $DB;

        $q = $DB->prepare("UPDATE accounts SET is_disabled = :is_disabled WHERE id = :id");
        $q->bindValue(':is_disabled', $is_disabled);
        $q->bindValue(':id', $this->id);
        $q->execute();
    }

    function setPassword($new_password)
    {
        global $DB;

        $correct_hash = create_hash($new_password);
        $q = $DB->prepare("UPDATE accounts SET password_hash = :password_hash WHERE id = :id");
        $q->bindValue(':id', $this->id);
        $q->bindValue(':password_hash', $correct_hash);
        $q->execute();
    }

    function delete()
    {
        global $DB;

        /* Remove the user's user group memberships */
        $q = $DB->prepare(
            "DELETE FROM user_group_membership
             WHERE account_id = :account_id"
        );
        $q->bindValue(':account_id', $this->id);
        $q->execute();

        /* Delete the actual account */
        $q = $DB->prepare("DELETE FROM accounts WHERE id = :id");
        $q->bindValue(':id', $this->id);
        $q->execute();
    }

    function hasReadPost($post)
    {
        global $DB;
        $q = $DB->prepare("SELECT has_read FROM read_status WHERE user_id = :user_id AND post_id = :post_id");
        $q->bindValue(':user_id', $this->id);
        $q->bindValue(':post_id', $post->getID());
        $q->execute();
        $row = $q->fetch();
        if ($row === FALSE) {
            return null;
        }
        return $row['has_read'] == "1";
    }

    function setRead($post, $read)
    {
        global $DB;

        $read = $read ? "1" : "0";

        if ($this->hasReadPost($post) === null) {
            $q = $DB->prepare(
                "INSERT INTO read_status (user_id, post_id, has_read)
                VALUES (:user_id, :post_id, :has_read)"
            );
        } else {
            $q = $DB->prepare(
                "UPDATE read_status SET has_read = :has_read
                WHERE user_id = :user_id AND post_id = :post_id"
            );
        }
        $q->bindValue(':user_id', $this->id);
        $q->bindValue(':post_id', $post->getID());
        $q->bindValue(':has_read', $read);
        $q->execute();
    }
}

?>
