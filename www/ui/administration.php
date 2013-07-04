<?php
require_once('ui/view.php');
require_once('inc/Newsgroup.php');
require_once('inc/permissions.php');
require_once('inc/settings.php');
class AdministrationView extends View
{
    public function show()
    {
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
        $groups = Newsgroup::GetAllGroups();
        foreach ($groups as $group) {
            $safe_name = htmlentities($group->getName(), ENT_QUOTES);
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
        <th>User Class</th>
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

            ?>
                <td>
                    <form action="admin.php" method="POST">
                        <select name="user_class">
                        <?php
                            $all_ucs = UserClass::GetAllUserClasses();
                            foreach ($all_ucs as $uc) {
                                $safe_uc_name = htmlentities($uc->getName(), ENT_QUOTES);
                                $safe_uc_id = (int)$uc->getID();
                                if ($uc->getID() == $user->getUserClass()->getID()) {
                                    echo "<option value=\"$safe_uc_id\" selected=\"selected\">$safe_uc_name</option>";
                                } else {
                                    echo "<option value=\"$safe_uc_id\">$safe_uc_name</option>";
                                }
                            }
                        ?>
                        </select>
                        <input type="hidden" name="username" value="<?php echo $safe_name; ?>" />
                        <input type="submit" name="set_userclass" value="Set" />
                    </form>
                </td>
            <?

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

    <h2>Special User Classes</h2>
    <form action="admin.php" method="POST">
        Default (New Users):
        <select name="default_class">
            <?php
                $all_ucs = UserClass::GetAllUserClasses();
                foreach ($all_ucs as $uc) {
                    $safe_uc_name = htmlentities($uc->getName(), ENT_QUOTES);
                    $safe_uc_id = (int)$uc->getID();
                    if ($uc->getID() == (int)Settings::GetSetting("class.default")) {
                        echo "<option value=\"$safe_uc_id\" selected=\"selected\">$safe_uc_name</option>";
                    } else {
                        echo "<option value=\"$safe_uc_id\">$safe_uc_name</option>";
                    }
                }
            ?>
        </select>
        <br />
        Anonymous (Not Logged In):
        <select name="anonymous_class">
            <?php
                $all_ucs = UserClass::GetAllUserClasses();
                foreach ($all_ucs as $uc) {
                    $safe_uc_name = htmlentities($uc->getName(), ENT_QUOTES);
                    $safe_uc_id = (int)$uc->getID();
                    if ($uc->getID() == (int)Settings::GetSetting("class.anonymous")) {
                        echo "<option value=\"$safe_uc_id\" selected=\"selected\">$safe_uc_name</option>";
                    } else {
                        echo "<option value=\"$safe_uc_id\">$safe_uc_name</option>";
                    }
                }
            ?>
        </select>
        <br />
        <input type="submit" name="special_userclasses" value="Save" />
    </form>

    <h1>Permissions</h1>
    <form action="admin.php" method="POST">
    <table border="1">
        <tr>
            <th>Group</th>
            <?php
                $all_ucs = UserClass::GetAllUserClasses();

                foreach ($all_ucs as $uc) {
                    $safe_uc_name = htmlentities($uc->getName(), ENT_QUOTES);
                    echo "<th>$safe_uc_name</th>";
                }
            ?>
        </tr>
        <?php
            $all_groups = Newsgroup::GetAllGroups();
            $all_ucs = UserClass::GetAllUserClasses();
            $all_abilities = UserClass::GetAllAbilities();
            foreach ($all_groups as $group) {
                $safe_group_name = htmlentities($group->getName(), ENT_QUOTES);
                echo '<tr>';
                echo "<td>$safe_group_name</td>";
                foreach ($all_ucs as $uc) {
                    $select_name = htmlentities("gu_" . $group->getID() . "_" . $uc->getID(), ENT_QUOTES);
                    echo "<td>";
                    echo "<select name=\"$select_name\">";
                    foreach ($all_abilities as $ability) {
                        $safe_ability = htmlentities($ability, ENT_QUOTES);
                        if ($ability === $uc->getAbilityForGroup($group)) {
                            echo "<option value=\"$safe_ability\" selected=\"selected\">$safe_ability</option>";
                        } else {
                            echo "<option value=\"$safe_ability\">$safe_ability</option>";
                        }
                    }
                    echo "</select>";
                }
                echo '<tr>';
            }
        ?>
    </table>
    <input type="submit" name="save_permissions" value="Save" />
    </form>

    <h1>CAPTCHA</h1>
    <form action="admin.php" method="POST">
        <?php
            if (Settings::GetSetting("recaptcha.onregister") == "1") {
                echo '<input type="checkbox" name="recaptcha_register_enable" checked="checked">';
            } else {
                echo '<input type="checkbox" name="recaptcha_register_enable">';
            }
        ?>
        Require CAPTCHA to register.
        <br />
        Recaptcha Public Key:
        <input name="recaptcha_public" value="<?php
            $recaptcha_public = Settings::GetSetting("recaptcha.public_key");
            if ($recaptcha_public !== FALSE) {
                echo htmlentities($recaptcha_public, ENT_QUOTES);
            }
        ?>" />
        <br />
        Recaptcha Private Key:
        <input name="recaptcha_private" value="<?php
            $recaptcha_private = Settings::GetSetting("recaptcha.private_key");
            if ($recaptcha_private !== FALSE) {
                echo htmlentities($recaptcha_private, ENT_QUOTES);
            }
        ?>" />
        <br />
        <input type="submit" name="recaptcha_save" value="Save" />
    </form>
<?
    }
}
?>
