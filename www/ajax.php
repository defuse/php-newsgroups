<?php
require_once('inc/Newsgroup.php');
require_once('inc/account.php');
require_once('inc/permissions.php');

$user = Login::GetLoggedInUser();
$user_class = $user ? $user->getUserClass() : UserClass::Anonymous();

if (isset($_POST['id']) && !empty($_POST['id'])) {
    try {
        $post = new Post($_POST['id']);
        if ($user_class->canReadGroup($post->getGroup())) {
            send_ajax_post($post);
        } else {
            send_ajax_post(null);
        }
    } catch (PostDoesNotExistException $e) {
        send_ajax_post(null);
    }
}

function send_ajax_post($post)
{
    if ($post === null) {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= "<response>";
        $xml .= "<status>fail</status>";
        $xml .= "</response>";
        send_xml_response($xml);
    } else {
        $safe_id = htmlentities($post->getID(), ENT_QUOTES);
        $safe_user = htmlentities($post->getUser(), ENT_QUOTES);
        $safe_time = htmlentities($post->getFormattedTime(), ENT_QUOTES);
        $safe_title = htmlentities($post->getTitle(), ENT_QUOTES);
        $safe_contents = htmlentities($post->getContentsHtml(), ENT_QUOTES);
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= "<response>";
        $xml .= "<status>success</status>";
        $xml .= "<id>$safe_id</id>";
        $xml .= "<user>$safe_user</user>";
        $xml .= "<time>$safe_time</time>";
        $xml .= "<title>$safe_title</title>";
        $xml .= "<contents>$safe_contents</contents>";
        $xml .= "</response>";
        send_xml_response($xml);
    }
}

function send_xml_response($xml)
{
    // header('Content-Type: text/xml');
    echo $xml;
    die();
}
?>
