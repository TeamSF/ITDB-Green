<?php
//ITDB:IT-items database
//sivann at gmail.com 2008-2014

$version=file_get_contents("VERSION");
$fordbversion=6;

/*********************************************************************** 
 *********************************************************************** 
 ***********************************************************************/

$itdb_start=getmicrotime();
function getmicrotime() {
    $a = explode (' ',microtime());
    return(double) $a[0] + $a[1];
} 

$initok=1;
require("init.php");


$head="";

if (!isset($_GET['action']))
  $_GET['action']="";
else {
  $_GET['action']=str_replace("/","",$_GET['action']);
  $_GET['action']=str_replace("%","",$_GET['action']);
  $_GET['action']=str_replace(";","",$_GET['action']);

}

if ((isset($_GET['export']) && ($_GET['export']==1))) {
  $action = "listitems2"; 
  require ("php/listitems2.php");
  exit;
}

$req="php/{$_GET['action']}.php";
$stitle="";

if ((isset($_GET['dlg']) && ($_GET['dlg']==1))) {
  $dlg=1;
}
else  {
  $dlg=0;
}


switch ($_GET['action']) {
  case "listitems2": 
    $title="Find Item2";
    break;
  case "listitems": 
    $title="Find Item";
    $head.="<link rel='stylesheet' type='text/css' href='css/jquery.tag.list.css' />\n";
    break;
  case "listagents": 
    $title="List Agents";
    break;
  case "editagent": 
    $title="Edit Agent";
    break;
  case "edititem": 
    $title="Edit Item";
    $stitle="Item";
    $head.="<script language='javascript' type='text/javascript' src='js/jquery.tag.js'></script>\n".
	   "<link rel='stylesheet' type='text/css' href='css/jquery.tag.css' />\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.metadata.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.front.js'></script>\n";
    break;
  case "editsoftware": 
    $title="Edit Software";
    $stitle="Software";
    $head.="<script language='javascript' type='text/javascript' src='js/jquery.tag.js'></script>\n".
	   "<link rel='stylesheet' type='text/css' href='css/jquery.tag.css' />\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.metadata.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.front.js'></script>\n";
    break;
  case "listsoftware": 
    $title="List Software";
    $head.="<link rel='stylesheet' type='text/css' href='css/jquery.tag.list.css' />\n";
    break;
  case "listcontracts": 
    $title="List Contracts";
    break;
  case "listinvoices": 
    $title="List Invoices";
    break;
  case "editinvoice": 
    $title="Edit Invoice";
    $stitle="Invoice";
    $head.="<script language='javascript' type='text/javascript' src='js/jquery.metadata.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.front.js'></script>\n";
    break;
  case "listfiles": 
    $title="List Files";
    break;
  case "editfile": 
    $title="Edit File";
    $stitle="File";
    $head.="<script language='javascript' type='text/javascript' src='js/jquery.metadata.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.front.js'></script>\n";
    break;
  case "listusers": 
    $title="List Users";
    break;
  case "listracks": 
    $title="List Racks";
    break;
  case "translations": 
    $title="Translations";
    break;
  case "settings": 
    $title="Settings";
    break;

  case "import": 
    $title="Import";
    break;
  case "edituser": 
    $stitle="User";
    $title="Edit User";
    $head.="<script language='javascript' type='text/javascript' src='js/jquery.metadata.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.front.js'></script>\n";
    break;

  case "editrack": 
    $stitle="Rack";
    $title="Edit Rack";
    $head.="<script language='javascript' type='text/javascript' src='js/jquery.metadata.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.front.js'></script>\n";
    break;

  case "edititypes": 
    $title="Edit Item Types";
    break;
  case "editcontract": 
    $title="Edit Contract";
    $stitle="Contract";
    $head.="<script language='javascript' type='text/javascript' src='js/jquery.metadata.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.js'></script>\n".
	   "<script language='javascript' type='text/javascript' src='js/jquery.validate.front.js'></script>\n";
    break;
  case "editcontracttypes": 
    $title="Edit Contract Types";
    break;
  case "edittags": 
    $title="Edit Tags";
    break;
  case "editusers": 
    $title="Edit Users";
    break;
  case "listlocations": 
    $title="List Locations";
    break;
  case "editlocation": 
    $title="Edit Location";
    break;
  case "editstatustypes": 
    $title="Edit Item Status Types";
    break;
  case "editfiletypes": 
    $title="Edit File Types";
    break;
  case "printlabels": 
    $title="Print Labels ";
    break;
  case "reports": 
    $title="Reports";
    $head.="<script language='javascript' type='text/javascript' src='js/jqplot/jquery.jqplot.js'></script>\n".
	   "<script type='text/javascript' src='js/jqplot/plugins/jqplot.pieRenderer.js'></script>\n".
	   "<script type='text/javascript' src='js/jqplot/plugins/jqplot.barRenderer.js'></script>\n".
	   "<!--[if lt IE 9]><script language='javascript' type='text/javascript' src='js/jqplot/excanvas.js'></script><![endif]-->\n".
	   "<link rel='stylesheet' type='text/css' href='css/jquery.jqplot.css' />";
    break;
  case "showhist": 
    $title="History";
    break;
  case "browse": 
    $title="Browse Data";
    $head.="<script type='text/javascript' src='js/jstree/jquery.jstree.js'></script>";
    break;
  case "viewrack": 
    $title="Rack";
    $stitle="Rack";
    break;
  case "about":
    $title="About";
    $stitle="About";
    $req="php/about.php";
    break;
  default: 
    $title="";
    $stitle="";
    $req="php/home.php";
    break;
}
if (isset($_GET['id'])) 
  $id=$_GET['id']; 
else 
  $id="";

if (strlen($stitle)) $stitle.=":".$id;

$x="style_".$_GET['action'];
$$x="color:#BAFF04 ";


require('php/header.php');

if ($authstatus && (dbversion() != $fordbversion)) {
  echo "<body>";
  require ("php/itdbupdate.php");
  echo "</body>\n</html>\n";
  exit;
}

if ($dlg && $authstatus) {
  echo "<body>";
  require($req);
  echo "</body>\n</html>\n";
  exit;
}

$username = "";
$password = "";

// Connect & bind to ldap
$ldap=connect_to_ldap_server($settings['ldap_server'],$settings['ldap_port'],$username,$password,$settings['ldap_dn']);

// OU for users
$ou = $settings['ldap_getusers'];
// Filter which objects of ldap shall be included in search results
$filter = $settings['ldap_getusers_filter'];



function get_entries_from_ldap_server($ldap,$ou,$filter)
{
    if(($ldap) && ($ou) && ($filter))
    {
        // Specify which attributes shall be taken from the filtered objects
        $attributes = array ('samaccountname','cn');
        // Execute the search with object filter an attribute list
        $search = ldap_search($ldap,$ou,$filter,$attributes);

        // Get the results of the ldap search
        $ldap_entries = ldap_get_entries($ldap, $search);

        return $ldap_entries;
    }
    else
    {
        return FALSE;
    }
}


$ldap_entries = get_entries_from_ldap_server($ldap,$ou,$filter);


function get_ldap_users($ldap_entries)
{
    if (count($ldap_entries) != 0)
    {
        $all_ldap_users = array();
        for ($i = 0; $i<$ldap_entries["count"]; $i++) {
            $ldap_username = $ldap_entries[$i]["samaccountname"][0];
            if ($ldap_username!='admin') {
                // Build array with all users from ldap
                $all_ldap_users[] = $ldap_username;
            }
        }
        return $all_ldap_users;
    }
    else
    {
        return FALSE;
    }
}




function get_local_users_copied_from_ldap()
{
    global $dbh;

    // Get all local users which are from ldap and have no password
    $sql="SELECT username from users where usertype='2' AND (pass IS NULL OR pass='')";
    $sth=db_execute($dbh,$sql);
    $r=$sth->fetchAll(PDO::FETCH_ASSOC);
    while ($row = array_shift($r)) {
        $all_local_users[] = $row['username'];
    }
    return $all_local_users;
}

function compare_local_users_ldap_users($local_users,$ldap_users)
{
    if ((count($local_users) != 0) && (count($ldap_users) != 0))
    {
        //Compare local user array and ldap user array - returns users only existing locally
        $only_local_users = array_diff($local_users, $ldap_users);

        return $only_local_users;
    }
    else
    {
        return FALSE;
    }
}







function update_local_users_with_ldap_users($ldap_entries)
{
    global $dbh;

    for ($i = 0; $i<$ldap_entries["count"]; $i++) {
        $ldap_username = $ldap_entries[$i]["samaccountname"][0];
        $ldap_userdesc = $ldap_entries[$i]["cn"][0];
        $user_id = getuseridbyname($ldap_username);
        // Skip local user "admin" with user id "1"
        if ($ldap_username!='admin' || $user_id!="1" ) {
            // Insert new users into database
            if ($user_id == "-1")
            {
                $usertype = "2";
                $sql="INSERT into users (username , userdesc , usertype) ".
                 " VALUES ('$ldap_username','$ldap_userdesc', '$usertype')";
                db_exec($dbh,$sql,0,0,$lastid);
                $lastid=$dbh->lastInsertId();
            }
            // Update existing users
            else
            {
                // Update description of user when necessary
                if ((getuserdescbyname($ldap_username)) != $ldap_userdesc)
                {
                    $sql="UPDATE users set userdesc='$ldap_userdesc' WHERE id='$user_id'";
                    db_exec($dbh,$sql);
                }
            }
        }
    }

    $ldap_users = get_ldap_users($ldap_entries);
    $local_users = get_local_users_copied_from_ldap();

    if ((count($local_users) != 0) && (count($ldap_users) != 0))
    {
        $only_local_users = compare_local_users_ldap_users($local_users,$ldap_users);

        //Delete every user existing only locally that came from ldap and does not have any associated items
        //When user has items assigned, display an errorcontainer instead
        foreach ($only_local_users as $key => $local_user)
        {
            $user_id = getuseridbyname($local_user);
            $assigned_items=countitemsofuser($user_id);
            if ($assigned_items == 0)
            {
                deluser($user_id,$dbh);
            }
            else
            {
                $disperr= "
                <div class='ui-state-error ui-corner-all' style='padding: 0 .7em;width:930px;margin-bottom:3px;margin-top:6px;margin-left:11px;'>
                  <p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;'></span>
                  <strong>Error: Cannot delete the user ".$local_user.".<br>User has <a href='$scriptname?action=edituser&amp;id=$user_id' style='color:#2233DD'>".$assigned_items." items</a> assigned</strong></p>
                </div>
                ";
                return $disperr;
            }
        }
        return "FEHLER ODER DISPERR";
    }
    else
    {
        return "IRGENDWAS - WEIS NOCH NICHT :D";
    }
}











$still_assigned=array();
$assigned_id=array();
$assigned=false;
//Delete every user from ldap without any associated items
foreach ($only_local_users as $key => $local_user)
{
    $user_id = getuseridbyname($local_user);
    $assigned_items=countitemsofuser($user_id);
    if ($assigned_items == 0)
    {
        deluser($user_id,$dbh);
    }
    else
    {
        $still_assigned[]=$local_user;
        $assigned_id[]=$user_id;
        $assigned=true;
    }
}
if($assigned==true)
    {
        ?>
        <div class='ui-state-error ui-corner-all' style='padding: 0 .7em;min-width:930px;margin-bottom:3px;margin-top:6px;margin-left:120px;margin-right:15px;'>
            <p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;'></span>
            <strong align=left;>Error: Cannot delete the following user<?php if(count($still_assigned)!=1) echo "s"; ?>:
        <?php
        for ($i=0;$i<count($still_assigned);$i++)
        {
                ?>
                <a href=<?php echo $scriptname;?>?action=edituser&amp;id=<?php echo $assigned_id[$i]; ?> style='color:#2233DD'> <?php echo $still_assigned[$i]; if($i+1 !=                                 count($still_assigned)) echo ", "; ?></a>
                <?php
        }
        ?>
        <br>User<?php if(count($still_assigned)!=1) echo "s"; ?> still has Items assigned!</strong></p></div>
        <?php
}
















$disperr = update_local_users_with_ldap_users($ldap_entries);



function old_stuff()
{
//print_r($all_local_users);

/*
// $i = entries
// $ii = attributes for entry
// $iii = values per attribute
for ($i = 0; $i<$info["count"]; $i++) {
  for ($ii=0; $ii<$info[$i]["count"]; $ii++){
     $data = $info[$i][$ii];
     for ($iii=0; $iii<$info[$i][$data]["count"]; $iii++) {
       echo $data.":".$info[$i][$data][$iii]."<br>";
     }
  }
}

print("<pre>".print_r($info,true)."</pre>");

echo "JETZT GEHTS LOS<br>";
//Now, to display the results we want:
for ($i=0; $i<$info["count"]; $i++)
    {
    // to show the attribute displayName (note the case!)
    $ldap_username = $info[$i]["samaccountname"][0];
    //$test = getuseridbyname($ldap_username);
    //echo "TEST_IST:".$test."TEST<br>";
    if($test != "-1"){
        echo $test;
        echo $info[$i]["samaccountname"][0];
        echo "<br>";
    }
}

//$entry = ldap_first_entry($ldap, $search);

//$attrs = ldap_get_attributes($ldap, $entry);
*/

//echo "Search has ".$info["count"]." entries returned\n";
}

?>

<body onload='BodyLoad()' class='mainbody'>


<!--div id='mainheader'> <?php echo $settings['companytitle']?> </div-->
<div id='leftcolumn' >
<div onclick='self.location.href="<?php echo $scriptname?>"' id='leftlogo' >
<span style='padding-top:5px;'> <a href='<?php echo $scriptname?>'> ITDB </a></span>
</div>

<span id=logo>
IT ITems DataBase
</span>

<hr class='green1'>
<?php 
if ($authstatus) {
?>

<table class='thdr' width='90%' border=0>

<tr><td><a style='<?php echo $style_?>' class='ahdr' href="<?php echo $scriptname?>" ><?php echo t("Home") ?></a></td> <td></td> </tr>
<tr><td><a style='<?php echo $style_about?>' class='ahdr' href="<?php echo $scriptname?>?action=about" ><?php te("About")?></a></td> <td></td> </tr>

<tr><td colspan=2><hr class='light1'> </td></tr>

<tr>
<td><a style="<?php echo $style_listitems.$style_edititem; ?>" class='ahdr' title='<?php te("List Items");?>' href="<?php echo $scriptname?>?action=listitems" ><?php te("Items")?></a> </td>
<td><a title='<?php te("Add new Item");?>' class='ahdr' href="<?php echo $scriptname?>?action=edititem&amp;id=new" ><img  alt="+" src='images/add.png'></a></td>
</tr>

<tr>
<td><a style="<?php echo $style_listsoftware.$style_editsoftware; ?>" title='<?php te("List Software");?>' class='ahdr' href="<?php echo $scriptname?>?action=listsoftware" ><?php te("Software");?></a> </td>
<td><a title='<?php te("Add new Software");?>' class='ahdr' href="<?php echo $scriptname?>?action=editsoftware&amp;id=new" ><img  alt="+" src='images/add.png'></a></td>
</tr>

<tr>
<td><a style="<?php echo $style_listinvoices.$style_editinvoice; ?>" title='<?php te("List Invoices");?>' class='ahdr' href="<?php echo $scriptname?>?action=listinvoices" ><?php te("Invoices");?></a> </td>
<td><a title='<?php te("Add new Invoice");?>' class='ahdr' href="<?php echo $scriptname?>?action=editinvoice&amp;id=new" ><img  alt="+" src='images/add.png'></a></td>
</tr>


<tr>
<td><a style="<?php echo $style_listagents.$style_editagent; ?>" title='<?php te("Vendors/Buyers/ Manufacturers");?>' class='ahdr' href="<?php echo $scriptname?>?action=listagents" ><?php te("Agents");?></a> </td>
<td><a title='<?php te("Add new Agent");?>' class='ahdr' href="<?php echo $scriptname?>?action=editagent&amp;id=new" ><img  alt="+" src='images/add.png'></a></td>
</tr>

<tr>
<td><a style="<?php echo $style_listfiles.$style_editfile; ?>" title='<?php te("Documents, Manuals, Offers, Licenses, ...");?>' class='ahdr' href="<?php echo $scriptname?>?action=listfiles" ><?php te("Files");?></a> </td>
<td><a title='<?php te("Add new File");?>' class='ahdr' href="<?php echo $scriptname?>?action=editfile&amp;id=new" ><img  alt="+" src='images/add.png'></a></td> 
</tr>


<tr>
<td><a style="<?php echo $style_listcontracts.$style_editcontract; ?>" title='<?php te("Support and Maintanance, Leases, ...");?>' class='ahdr' href="<?php echo $scriptname?>?action=listcontracts" ><?php te("Contracts");?></a> </td>
<td><a title='<?php te("Add new Contract");?>' class='ahdr' href="<?php echo $scriptname?>?action=editcontract&amp;id=new" ><img  alt="+" src='images/add.png'></a></td>
</tr>

<tr>
<td><a style="<?php echo $style_listlocations; ?>" class='ahdr' href="<?php echo $scriptname?>?action=listlocations" ><?php te("Locations");?></a></td>
<td><a style="<?php echo $style_editlocation; ?>" class='ahdr' href="<?php echo $scriptname?>?action=editlocation&amp;id=new" ><img  alt="+" src='images/add.png'></a></td>
</tr>

<tr>
<td><a style="<?php echo $style_listusers; ?>" class='ahdr' href="<?php echo $scriptname?>?action=listusers" ><?php te("Users");?></a></td>
<td><a style="<?php echo $style_edituser; ?>" class='ahdr' href="<?php echo $scriptname?>?action=edituser&amp;id=new" ><img  alt="+" src='images/add.png'></a></td>
</tr>


<tr>
<td><a style="<?php echo $style_listracks; ?>" class='ahdr' href="<?php echo $scriptname?>?action=listracks" ><?php te("Racks");?></a></td>
<td><a style="<?php echo $style_editrack; ?>" class='ahdr' href="<?php echo $scriptname?>?action=editrack&amp;id=new" ><img  alt="+" src='images/add.png'></a></td>
</tr>

<tr><td colspan=2><hr class='light1'> </td></tr>

<tr><td colspan=2><a style="<?php echo $style_edititypes; ?>" class='ahdr' href="<?php echo $scriptname?>?action=edititypes" ><?php te("Item Types");?></a></td></tr>
<tr><td colspan=2><a style="<?php echo $style_editcontracttypes; ?>" class='ahdr' href="<?php echo $scriptname?>?action=editcontracttypes" ><?php te("Contr. Types")?></a></td></tr>
<tr><td colspan=2><a style="<?php echo $style_editstatustypes; ?>" class='ahdr' href="<?php echo $scriptname?>?action=editstatustypes" ><?php te("Status Types");?></a></td></tr>
<tr><td colspan=2><a style="<?php echo $style_editfiletypes; ?>" class='ahdr' href="<?php echo $scriptname?>?action=editfiletypes" ><?php te("File Types");?></a></td></tr>

<tr><td colspan=2><a style="<?php echo $style_edittags; ?>" class='ahdr' href="<?php echo $scriptname?>?action=edittags" ><?php te("Tags")?></a></td></tr>

<tr><td colspan=2><hr class='light1'> </td></tr>

<tr><td colspan=2><a style="<?php echo $style_printlabels; ?>" class='ahdr' href="<?php echo $scriptname?>?action=printlabels" ><?php te("Print Labels")?></a></td></tr>
<tr><td colspan=2><a style="<?php echo $style_reports; ?>" class='ahdr' href="<?php echo $scriptname?>?action=reports" ><?php te("Reports")?></a></td></tr>
<tr><td colspan=2><a style="<?php echo $style_browse; ?>" class='ahdr' href="<?php echo $scriptname?>?action=browse" ><?php te("Browse Data")?></a></td></tr>
<tr><td colspan=2><hr class='light1'></td></tr>

<tr><td colspan=2><a style="<?php echo $style_settings; ?>" class='ahdr' href="<?php echo $scriptname?>?action=settings" ><?php te("Settings");?></a></td></tr>

<tr><td colspan=2><a style="<?php echo $style_import; ?>" class='ahdr' href="<?php echo $scriptname?>?action=import" ><?php te("Import");?></a></td></tr>
<tr><td colspan=2><a style="<?php echo $style_translations; ?>" class='ahdr' href="<?php echo $scriptname?>?action=translations" ><?php te("Translations");?></a></td></tr>
<tr><td colspan=2><a style="<?php echo $style_showhist; ?>" class='ahdr' href="<?php echo $scriptname?>?action=showhist" >DB Log</a></td></tr>
</table>
<?php 

}
else {
  if (isset($_COOKIE["itdbuser"])) $itdbuser=$_COOKIE["itdbuser"]; 
  else $itdbuser="username";

  echo "\n<form name=itdbloginfrm method=post>".
   "<input name=authusername size=10 onfocus=\"this.value='';\" ".
   "value='$itdbuser'>\n<br>".
   "<input name=authpassword size=10  type=password onfocus=\"this.value='';\" ".
   "value=''>\n".
   "<br><br><button type=submit><img src='images/key.png'> Login</button>";
   "\n";
}

if ($authstatus) {
  echo "\n<div style='height:5px'></div>".
       "<form method=post><button type='submit'><img width=20 src='images/logout_red.png'> ".t("Logout")."</button>".
       "\n<input type=hidden name=logout value='1'></form>";


  if (strlen($stitle)) {
    $url="$fscriptname?action=$action&id=$id";

    $sql="SELECT * FROM viewhist order by id DESC limit 1";
    $sth=db_execute($dbh,$sql);
    $viewhist=$sth->fetchAll(PDO::FETCH_ASSOC);
    if (!$demomode) {
      if ($viewhist[0]['url']!=$url) {
	$sql="INSERT into viewhist (url,description)".
	     " VALUEs ('$url','$stitle')";
	db_exec($dbh,$sql,1,1,$lastid);

	$lastkeep=(int)($lastid)-40;
	$sql="DELETE from viewhist where id<$lastkeep";
	db_exec($dbh,$sql,1,1);
	$sth=$dbh->exec($sql);
      }
    }
  }

  $sql="SELECT * FROM viewhist order by id DESC";
  $sth=db_execute($dbh,$sql);
  $viewhist=$sth->fetchAll(PDO::FETCH_ASSOC);

  ?>
  <div title='<?php te("Recent History");?>' style='font-size:7pt;height:75px;width:100%;overflow:auto;margin-top:5px ;margin-bottom:5px;text-align:left;color:white;border-bottom:1px solid #8FAFE4;'>
  <?php 
  for ($i=0;$i<count($viewhist);$i++){
    if (!($i%2)) $bgc="";else$bgc="background-color:#295BAD";
    echo "<div style='border-bottom:1px solid #8FAFE4;width:100%;clear:both;$bgc'><a style='color:white' href='".$viewhist[$i]['url']."'>".$viewhist[$i]['description']."</a></div>\n";
  }

  ?>
  </div>

<?php 
}

if (strstr($authmsg,"elcome") || strstr($authmsg,"thenticated")) 
  echo "<div class=info>$authmsg</div><br>";
elseif (!strstr($authmsg,"elcome")) 
  echo "<br><div class=warning>$authmsg</div>";


if ($authstatus) {
?>
  <a title='<?php te("Download DataBase file. Contains all data except uploaded files/documents");?>' class='ahdr' href='getdb.php'><img src='images/database_save.png'>DB (SQLite)</a><br>
  <a title='<?php te("Download a complete installation backup (much larger)");?>' class='ahdr' href='gettar.php'><img src='images/backup.gif' width=20>Full Backup</a><br>
<?php 
}

echo "<br> <small>".
     "<a href='CHANGELOG.txt' class='ahdr'>Version $version</a><br><a style='color:white' href='http://www.sivann.gr/software/itdb/'>sivann</a></small>\n";
?>
<br>
<a title='phpinfo' href='phpinfo.php'><img src='images/infosmall.png'></a>
</div>
<!-- END OF #leftcolumn -->


<div id='mainpage'>
<?php 
echo $disperr;
if ($authstatus) 
  require($req);
else {
  echo "<b>Please log in</b>";
  require("php/about.php");
}

$itdb_end=getmicrotime();

echo "</div>";// <!-- end of #mainpage -->

echo "<span style='color:#aaa'>server time = ".number_format(($itdb_end - $itdb_start),3)." secs</span>"; 

?>
</body>
</html>

