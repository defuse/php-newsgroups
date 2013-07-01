<?php
require_once('ui/view.php');
require_once('inc/Newsgroup.php');
class AdministrationView extends View
{
    public $add_success = false;
    public $add_group_exists = false;

    public $delete_success = false;
    public $delete_failed = false;

    public function show()
    {
?>
    <?php
        if ($this->add_success) {
            echo '<b>Group added.</b>';
        }
        if ($this->add_group_exists) {
            echo '<b>A group with that name already exists.</b>';
        }
        if ($this->delete_success) {
            echo '<b>Group deleted.</b>';
        }
        if ($this->delete_failed) {
            echo '<b>Failed deleting group.</b>';
        }
    ?>

    <h1>Groups</h1>

    <h2>Add a group</h2>
    <form action="admin.php" method="POST">
        Group name: <input type="text" name="groupname" value="" />
        <input type="submit" name="newgroup" value="Add" />
    </form>

    <table>
        <tr>
            <th>Group Name</th>
            <th>Delete</th>
        </tr>
    <?php
        $groups = Newsgroup::GetGroupNames();
        foreach ($groups as $group) {
            $safe_name = htmlentities($group, ENT_QUOTES);
            echo '<tr>';
            echo "<td>$safe_name</td>";
?>
            <td>
                <form action="admin.php" method="POST">
                <input type="hidden" name="groupname" value="<?php echo $safe_name; ?>" />
                <input type="submit" name="deletegroup" value="Delete" />
                </form>
            </td>
<?
            echo '</tr>';
        }
    ?>
    </table>

    <h1>Users</h1>
<?
    }
}
?>
