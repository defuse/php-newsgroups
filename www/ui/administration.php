<?php
require_once('ui/view.php');
require_once('inc/Newsgroup.php');
require_once('inc/permissions.php');
class AdministrationView extends View
{
    public $add_success = false;
    public $add_group_exists = false;

    public $delete_success = false;
    public $delete_failed = false;

    public $user_noexist = false;
    public $user_no_revoke_own = false;

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
        if ($this->user_noexist) {
            echo '<b>User does not exist.</b>';
        }
        if ($this->user_no_revoke_own) {
            echo '<b>You cannot revoke your own permissions.</b>';
        }
    ?>

    <h1>Groups</h1>

    <h2>Add a group</h2>
    <form action="admin.php" method="POST">
        Group name: <input type="text" name="groupname" value="" />
        <select name="default_ability">
        <?php
            $abilities = UserClass::GetAllAbilities();
            foreach ($abilities as $ability) {
                $safe_ability = htmlentities($ability, ENT_QUOTES);
                echo '<option value="' . $safe_ability . '">' . $safe_ability . '</option>';
            }
        ?>
        <input type="submit" name="newgroup" value="Add" />
        </select>
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
    <table>
    <tr>
        <th>Username</th>
        <th>Administrator</th>
        <th>Delete</th>
    </tr>
    <?php
        $all_users = Account::GetAllUsers();
        foreach ($all_users as $user) {
            $safe_name = htmlentities($user->getUsername(), ENT_QUOTES);
            echo '<tr>';
            echo "<td>$safe_name</td>";
            if ($user->isAdmin()) {
                ?>
                <td>
                    <form action="admin.php" method="POST">
                        <input type="hidden" name="username" value="<?php echo $safe_name; ?>" />
                        <input type="submit" name="admin_revoke" value="Revoke Administrator" />
                    </form>
                </td>
                <?
            } else {
                ?>
                <td>
                    <form action="admin.php" method="POST">
                        <input type="hidden" name="username" value="<?php echo $safe_name; ?>" />
                        <input type="submit" name="admin_give" value="Make Administrator" />
                    </form>
                </td>
                <?
            }
            echo '</tr>';
        }
    ?>
    </table>

    <h1>User Classes</h1>
    <h2>Add User Class</h2>
    <form action="admin.php" method="POST">
        <input type="text" name="name" value="" />
        <select name="default_ability">
        <?php
            $abilities = UserClass::GetAllAbilities();
            foreach ($abilities as $ability) {
                $safe_ability = htmlentities($ability, ENT_QUOTES);
                echo '<option value="' . $safe_ability . '">' . $safe_ability . '</option>';
            }
        ?>
        </select>
        <input type="submit" name="new_userclass" value="Add" />
    </form>

    <h2>Edit User Classes</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Delete</th>
        </tr>
        <?php
            $all_ucs = UserClass::GetAllUserClasses();
            foreach ($all_ucs as $uc) {
                $safe_uc_name = htmlentities($uc->getName(), ENT_QUOTES);
                $safe_uc_id = (int)$uc->getID();
                echo '<tr>';
                echo "<td>$safe_uc_name</td>";
            ?>
                <td>
                    <form action="admin.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $safe_uc_id; ?>" />
                        <input type="submit" name="delete_userclass" value="Delete" />
                    </form>
                </td>
            <?
                echo '</tr>';
            }
        ?>
    </table>

<?
    }
}
?>
