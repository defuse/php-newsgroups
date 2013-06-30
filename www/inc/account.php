<?php

require_once('inc/mysql.php');
require_once('inc/PasswordHash.php');

class Account
{
    public static function CreateAccount($username, $email, $password)
    {
        global $DB;
        if ($username == "") {
            throw new Exception('Empty username.');
        }

        if (!self::UserExists($username)) {
            $q = $DB->prepare(
                "INSERT INTO accounts 
                (username, email, password_hash, is_admin) 
                VALUES (:username, :email, :password_hash, :is_admin)"
            );
            $q->bindValue(':username', $username);
            $q->bindValue(':email', $email);
            $q->bindValue(':password_hash', create_hash($password));
            $q->bindValue(':is_admin', 0);
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
}

?>
