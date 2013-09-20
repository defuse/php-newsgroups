<?php
require_once('/etc/creds.php');

$DB = get_db();
$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$DB->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
function get_db()
{
    $creds = Creds::getCredentials("newsgroups");
    try {
        $DB = new PDO(
            "mysql:host={$creds[C_HOST]};dbname={$creds[C_DATB]}",
            $creds[C_USER],
            $creds[C_PASS],
            array(PDO::ATTR_PERSISTENT => true)
        );
        $DB->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch(Exception $e) {
        die('Failed to connect to database.');
    }
    unset($creds);
    return $DB;
}
?>
