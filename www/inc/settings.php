<?php
require_once('inc/mysql.php');

class Settings
{
    public static function GetSetting($name)
    {
        global $DB;

        $q = $DB->prepare("SELECT value FROM settings WHERE name = :name");
        $q->bindValue(':name', $name);
        $q->execute();
        $row = $q->fetch();
        if ($row === FALSE) {
            return null;
        } else {
            return $row['value']; 
        }
    }

    public static function SetSetting($name, $value)
    {
        global $DB;

        $current_value = self::GetSetting($name);
        if ($current_value === null) {
            $q = $DB->prepare("INSERT INTO settings (name, value) VALUES (:name, :value)");
            $q->bindParam(':name', $name);
            $q->bindParam(':value', $value);
            $q->execute();
        } else {
            $q = $DB->prepare("UPDATE settings SET value = :value WHERE name = :name");
            $q->bindParam(':name', $name);
            $q->bindParam(':value', $value);
            $q->execute();
        }
    }
}
?>
