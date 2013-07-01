<?php
require_once('ui/header.php');
class Layout
{
    private $header_view;
    private $contents_view;

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
    <div id="header">
        <?php $this->header_view->show(); ?>
    </div>
    <?php $this->contents_view->show(); ?>
</body>
</html>
<?
    }
}
?>
