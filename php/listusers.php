<SCRIPT LANGUAGE="JavaScript"> 
$(function () {
  $('table#userslisttbl').dataTable({
	"sPaginationType": "full_numbers",
	"bJQueryUI": true,
	"iDisplayLength": 25,
	"bLengthChange": true,
	"bFilter": true,
	"bSort": true,
	"bInfo": true,
	"sDom": '<"H"Tlpf>rt<"F"ip>',
	"aaSorting": [],
	"oTableTools": {
	    "sSwfPath": "swf/copy_cvs_xls_pdf.swf"
	}

  });
});

$(document).ready(function() {
    <?php
        if (isset($_POST['sync']) ) { //if we came from ldap sync, refresh to show new synced ldap users
	       echo "window.location=window.location;";
        }
    ?>
});
</SCRIPT>
<?php 

if (!isset($initok)) {echo "do not run this script directly";exit;}

$sql="SELECT * from users ORDER by username ASC";
$sth=db_execute($dbh,$sql);

if (isset($_POST['sync'])) {
    $ldap = connect_to_ldap_server($settings['ldap_server'],$settings['ldap_port'],$settings['ldap_binduser'],base64_decode($settings['ldap_bindpass']),$settings['ldap_dn']);
    if ($ldap) {
        $ldap_entries = get_entries_from_ldap_server($ldap,$settings['ldap_getusers'],$settings['ldap_getusers_filter']);
        if ($ldap_entries) {
            $ldap_sync_result = update_local_users_with_ldap_users($ldap_entries);
            $_SESSION["ldap_sync_error_container"] = $ldap_sync_result;
        }
    }
}
?>

<h1><?php te("Users");?> <a title='<?php te("Add new User");?>' href='<?php echo $scriptname?>?action=edituser&amp;id=new'><img border=0 src='images/add.png' ></a>
<?php if ($settings['useldapsync'] == 1) { ?>
<form id='mainform' method=post style="display:inline"><sub>
<a title='<?php te("Sync LDAP Users");?>'><input type="image" name="sync" img src="images/refresh.png" value="sync"></input></a>
</sub></form>
<?php }; ?>
</h1>

<table class='display' width="100%" id='userslisttbl'>
<thead>
<tr>
  <th width='2%'><?php te("Edit");?></th>
  <th width='5%'><?php te("Username");?></th>
  <th><?php te("User Description");?></th>
  <th><?php te("Type");?></th>
  <th width='5%'><?php te("Items");?></th>
</tr>
</thead>
<tbody>

<?php 
$usertype[0]=t("Full Access");
$usertype[1]=t("Read Only");
$usertype[2]=t("copied from LDAP (read only)");

$i=0;
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
  $i++;
  $itemcount=countitemsofuser($r['id']);
  echo "\n<tr>";
  echo "<td><a class='editid' href='$scriptname?action=edituser&amp;id=".$r['id']."'>{$r['id']}</a></td>\n";
  echo "<td>{$r['username']}</td>\n";
  echo "<td>{$r['userdesc']}</td>\n";
  echo "<td>{$usertype[$r['usertype']]}</td>\n";
  echo "<td>$itemcount</td>\n";
  echo "</tr>\n";
}
?>

</tbody>
</table>

</form>
</body>
</html>
