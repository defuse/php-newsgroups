<?php
require_once('ui/view.php');
require_once('inc/Newsgroup.php');
require_once('inc/permissions.php');
require_once('inc/settings.php');
class AdminUserView extends View
{
    public function show()
    {
?>
    <div class="contentpadding">
    <h1>Users</h1>
    <table>
    <tr>
        <th>Username</th>
        <th>Administrator</th>
        <th>Disable</th>
        <th>Delete</th>
        <th>Password Reset</th>
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
                    <form action="admin_users.php" method="POST">
                        <input type="hidden" name="username" value="<?php echo $safe_name; ?>" />
                        <input type="submit" name="admin_revoke" value="Revoke Administrator" />
                    </form>
                </td>
                <?
            } else {
                ?>
                <td>
                    <form action="admin_users.php" method="POST">
                        <input type="hidden" name="username" value="<?php echo $safe_name; ?>" />
                        <input type="submit" name="admin_give" value="Make Administrator" />
                    </form>
                </td>
                <?
            }

            if ($user->isDisabled()) {
                ?>
                    <td>
                        <form action="admin_users.php" method="POST">
                            <input type="hidden" name="username" value="<?php echo $safe_name; ?>" />
                            <input type="submit" name="enable" value="Enable" />
                        </form>
                    </td>
                <?
            } else {
                ?>
                    <td>
                        <form action="admin_users.php" method="POST">
                            <input type="hidden" name="username" value="<?php echo $safe_name; ?>" />
                            <input type="submit" name="disable" value="Disable" />
                        </form>
                    </td>
                <?
            }

            ?>
                <td>
                    <form action="admin_users.php" method="POST">
                        <input type="hidden" name="username" value="<?php echo $safe_name; ?>" />
                        <input type="submit" name="delete_user" value="Delete" onclick="return confirm('Are you sure?');"/>
                    </form>
                </td>

                <td>
                    <form action="admin_users.php" method="POST">
                        <input type="hidden" name="username" value="<?php echo $safe_name; ?>" />
                        <input type="password" name="new_password" value="" />
                        <input type="submit" name="reset_password" value="Change Password" />
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
