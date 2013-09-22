<?php
require_once('ui/view.php');
require_once('inc/permissions.php');

class AdminUserGroupsView extends View
{
    public function show()
    {
?>
    <div class="contentpadding">
    <h1>User Groups</h1>

    <h2>Default User Group</h2>

    <p>
    When a new account is created, it is added to the default user group. Select
    which group to use as the default user group below. Changing this setting
    does not affect any existing user group memberships.
    </p>

    <form action="admin_usergroups.php" method="POST">
        <select name="default_group_id">
            <?php
                $usergroups = UserGroup::GetAllUserGroups();
                foreach ($usergroups as $usergroup) {
                    $selected = "";
                    if ($usergroup->isDefaultUserGroup()) {
                        $selected = 'selected="selected"';
                    }
                    $safe_id = htmlentities($usergroup->getID(), ENT_QUOTES);
                    $safe_name = htmlentities($usergroup->getName(), ENT_QUOTES);

                    echo "<option value=\"$safe_id\" $selected >$safe_name</option>";
                }
            ?>
        </select>
        <input type="submit" name="set_default_usergroup" value="Save" />
    </form>

    <h2>Add User Group</h2>

    <form action="admin_usergroups.php" method="POST">
        <table class="formalign">
            <tr>
                <td>Name:</td>
                <td><input type="text" name="usergroup_name" value="" /></td>
            </tr>
            <tr>
                <td><input type="submit" name="add_usergroup" value="Add" /></td>
            </tr>
        </table>
    </form>

    <h2>User Group List</h2>

    <table class="formalign">
        <tr>
            <th>User Group</th>
            <th>Members</th>
            <th>Edit Members</th>
            <th>Delete</th>
        </tr>
        <?php
            $usergroups = UserGroup::GetAllUserGroups();
            foreach ($usergroups as $usergroup) {
                // TODO: 'Are you sure' button like the others
                echo '<tr>';
                echo '<td>' . htmlentities($usergroup->getName(), ENT_QUOTES) . '</td>';
                echo '<td>' . htmlentities($usergroup->getNumberOfMembers(), ENT_QUOTES) . '</td>';
                echo '<td><a href="admin_usergroup_members.php?usergroup=' . htmlentities($usergroup->getID(), ENT_QUOTES) . '">Edit Members</a></td>';
                echo '<td>
                        <form action="admin_usergroups.php" method="POST">
                        <input type="hidden" name="usergroup_id" value="' . htmlentities($usergroup->getID(), ENT_QUOTES) . '" />
                        <input type="submit" name="del_usergroup" value="Delete" />
                        </form>
                      </td>';
                echo '</tr>';
            }
        ?>
    </table>

    </div>
<?
    }
}
?>
