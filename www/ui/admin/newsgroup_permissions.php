<?php
require_once('ui/view.php');

class AdminNewsgroupPermissionsView extends View
{
    public $newsgroup = null;

    public function show()
    {
?>
    <div class="contentpadding">
    <?php if ($this->newsgroup) { ?>
        <h1>Access to Newsgroup '<?php echo htmlentities($this->newsgroup->getName(), ENT_QUOTES); ?>'</h1>

        <h2>Anonymous Access</h2>

        <form action="admin_newsgroup_permissions.php?newsgroup=<?php echo htmlentities($this->newsgroup->getName(), ENT_QUOTES);?>" method="POST">
        <table class="formalign">
            <tr>
                <td>Anonymous Access:</td>
                <td>
                    <select name="anonymous_access">
                        <?php
                            $access = AnonymousAccessControl::GetAnonymousAccessToGroup($this->newsgroup);
                            $selected = 'selected="selected"';
                            $sel_NOACCESS = $access == 'NOACCESS' ? $selected : "";
                            $sel_READONLY = $access == 'READONLY' ? $selected : "";
                            $sel_READWRITECAPTCHA = $access == 'READWRITECAPTCHA' ? $selected : "";
                            $sel_READWRITE = $access == 'READWRITE' ? $selected : "";
                        ?>
                        <option value="NOACCESS" <?php echo $sel_NOACCESS; ?>>No access</option>
                        <option value="READONLY" <?php echo $sel_READONLY; ?>>Read only</option>
                        <option value="READWRITECAPTCHA" <?php echo $sel_READWRITECAPTCHA; ?>>Read/Write with CAPTCHA</option>
                        <option value="READWRITE" <?php echo $sel_READWRITE; ?>>Read/Write</option>
                    </select>
                </td>
                <td>
                    <input type="submit" name="set_anonymous_access" value="Save" />
                </td>
            </tr>
        </table>
        </form>

        <h2>User Groups</h2>


    <? } ?>
    </div>
<?
    }
}
?>
