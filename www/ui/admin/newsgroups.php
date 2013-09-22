<?php
require_once('ui/view.php');
require_once('inc/Newsgroup.php');
require_once('inc/permissions.php');
require_once('inc/settings.php');
class AdminNewsgroupView extends View
{
    public function show()
    {
?>
    <div class="contentpadding">

    <h1>Groups</h1>

    <h2>Add Group</h2>
    <form action="admin_newsgroups.php" method="POST">
        <table class="formalign">
            <tr>
                <td>Name:</td>
                <td><input type="text" name="groupname" value="" /></td>
            </tr>
            <tr>
                <td><input type="submit" name="newgroup" value="Add" /></td>
            </tr>
        </table>
    </form>

    <h2>Group List</h2>
    <table>
        <tr>
            <th>Group Name</th>
            <th>Delete</th>
        </tr>
    <?php
        $groups = Newsgroup::GetAllGroups();
        foreach ($groups as $group) {
            $safe_name = htmlentities($group->getName(), ENT_QUOTES);
            echo '<tr>';
            echo "<td>$safe_name</td>";
?>
            <td>
                <form action="admin_newsgroups.php" method="POST">
                <input type="hidden" name="groupname" value="<?php echo $safe_name; ?>" />
                <input type="submit" name="deletegroup" value="Delete" onclick="return confirm('Are you sure?')"/>
                </form>
            </td>
<?
            echo '</tr>';
        }
    ?>
    </table>
    </div>
<?
    }
}
?>
