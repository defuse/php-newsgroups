<?php

require_once('inc/mysql.php');
require_once('inc/PasswordHash.php');
require_once('inc/permissions.php');

class UserExistsException extends Exception { /* empty */ }
class UserDoesNotExistException extends Exception { /* empty */ }

/* FIXME: This is vulnerable to session fixation and all of that */
class Login
{
    public static function TryLogin($username, $password)
    {
        self::StartSession();
        if (Account::CheckPassword($username, $password)) {
            $account = new Account($username);
            $_SESSION = array();
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
                (username, password_hash, is_admin, :user_class) 
                VALUES (:username, :password_hash, :is_admin, :user_class)"
            );
            $q->bindValue(':username', $username);
            $q->bindValue(':password_hash', create_hash($password));
            $q->bindValue(':is_admin', 0);
            $q->bindValue(':user_class', UserClass::GetDefaultUserClass()->getID());
            $q->execute();

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
        return $id;
    }

    function getUsername()
    {
        return $this->username;
    }

    function getUserClass()
    {
        global $DB;

        $q = $DB->prepare("SELECT user_class FROM accounts WHERE id = :id");
        $q->bindValue(':id', $this->id);
        $q->execute();
        $row = $q->fetch();
        return new UserClass($row['user_class']);
    }

    function setUserClass($uc)
    {
        global $DB;

        $q = $DB->prepare("UPDATE accounts SET user_class = :user_class WHERE id = :id");
        $q->bindValue(':id', $this->id);
        $q->bindValue(':user_class', $uc->getID());
        $q->execute();
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
}

?>
