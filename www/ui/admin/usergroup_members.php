<?php
require_once('ui/view.php');
require_once('inc/permissions.php');
require_once('inc/account.php');

class AdminUserGroupMembersView extends View
{
    public $usergroup = null;

    public function show()
    {
?>
    <div class="contentpadding">
    <?php if ($this->usergroup) { ?>
        <h1>Members of User Group '<?php echo htmlentities($this->usergroup->getName(), ENT_QUOTES); ?>'</h1>

        <table>
            <tr>
                <th>User</th>
                <th>Member</th>
            </tr>
        <?php
            $all_users = Account::GetAllUsers();
            foreach ($all_users as $user) {
                echo '<tr>';
                echo '<td>' . htmlentities($user->getUsername(), ENT_QUOTES) . '</td>';
                echo '<td>';
                $safe_id = htmlentities($this->usergroup->getID(), ENT_QUOTES);
                echo '<form action="admin_usergroup_members.php?usergroup=' . $safe_id . '" method="POST">';
                if ($this->usergroup->isMember($user)) {
                    echo '<input type="checkbox" name="is_member" value="yes" checked="checked" />';
                } else {
                    echo '<input type="checkbox" name="is_member" value="yes" />';
                }
                echo '<input type="submit" name="modify_member" value="Save" />';
                echo '<input type="hidden" name="user_id" value="' . htmlentities($user->getID(), ENT_QUOTES) . '" />';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
        ?>
        </table>
    
    <? } ?>
    </div>
<?
    }
}
?>
