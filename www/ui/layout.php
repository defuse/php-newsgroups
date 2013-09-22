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
    <link rel="stylesheet" media="all" type="text/css" href="css/style.css" />
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
    <div id="main_content">
    <table id="tblcolumns" cellpadding="0" cellspacing="0">
        <tr>
            <td id="grouplist">
                <div id="grouplistheader">
                    Groups
                </div>
                <div id="grouplistlist">
                <?php
                    $user = Login::GetLoggedInUser();
                    $access = Login::GetEffectiveAccessControl();
                    $sidebar_groups = $access->getVisibleNewsgroups(); 
                    foreach ($sidebar_groups as $group) {
                        $name = $group->getName();
                        $safe_name = htmlentities($name, ENT_QUOTES);
                        echo '<a href="index.php?group=' . $safe_name . '">';
                        if ($this->current_group !== null && $this->current_group->getName() === $name) {
                            echo '<div class="grouplistitem grouplist_selected">';
                        } else  {
                            echo '<div class="grouplistitem">';
                        }
                        echo $safe_name;
                        echo '</div>';
                        echo '</a>';
                    }
                ?>
                </div>
            </td>
            <td id="groupcontents">
                <?php
                    if ($this->flash !== null) {
                ?>
                <div id="flash">
                    <?php echo htmlentities($this->flash, ENT_QUOTES); ?>
                </div>
                <? } ?>
                <?php $this->contents_view->show(); ?>
            </td>
        </tr>
    </table>
    </div>
</body>
</html>
<?
    }
}
?>
