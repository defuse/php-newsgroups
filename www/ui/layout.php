<?php
require_once('ui/header.php');
class Layout
{
    private $header_view;
    private $contents_view;

    public $flash = null;
    public $current_group = null;

    function __construct($view)
    {
        $this->header_view = new HeaderView();
        $this->contents_view = $view;
    }

    public function show()
    {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo htmlentities($this->contents_view->title(), ENT_QUOTES); ?></title>
    <link rel="stylesheet" media="all" type="text/css" href="style.css" />
    <?php $this->header_view->head(); ?>
    <?php $this->contents_view->head(); ?>
</head>
<body>
    <noscript>
        <div id="noscript_banner">
            <strong>JavaScript Notice:</strong> This web site needs JavaScript
            to function. Please enable JavaScript in your browser.
        </div>
    </noscript>
    <div id="header">
        <?php $this->header_view->show(); ?>
    </div>
    <?php
        if ($this->flash !== null) {
    ?>
    <div id="flash">
        <strong><?php echo htmlentities($this->flash, ENT_QUOTES); ?></strong>
    </div>
    <? } ?>
    <table id="tblcolumns">
        <tr>
            <td id="grouplist">
                <div id="grouplistheader">
                    Groups
                </div>
                <ul>
                <?php
                    $user = Login::GetLoggedInUser();
                    $user_class = $user ? $user->getUserClass() : UserClass::Anonymous();
                    $sidebar_groups = $user_class->getVisibleGroups();
                    foreach ($sidebar_groups as $group) {
                        $name = $group->getName();
                        $safe_name = htmlentities($name, ENT_QUOTES);
                        echo '<li>';
                        echo '<a href="index.php?group=' . $safe_name . '">';
                        if ($this->current_group !== null && $this->current_group->getName() === $name) {
                            echo '<b>' . $safe_name . '</b>';
                        } else  {
                            echo $safe_name;
                        }
                        echo '</a>';
                        echo '</li>';
                    }
                ?>
                </ul>
            </td>
            <td id="groupcontents">
                <?php $this->contents_view->show(); ?>
            </td>
        </tr>
    </table>
</body>
</html>
<?
    }
}
?>
