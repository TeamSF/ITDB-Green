<?php 
function sec2ymd($secs)
{
  if (strlen($secs))
    return date("Ymd",$secs);
  else 
    return "";
}

//convert Y/M/D dates to unix timestamp
function ymd2sec($d)
{
  global $settings;

  if (!strlen($d))
    $purchasedate2="NULL";
  elseif ($settings['dateformat']=="ymd"){
    $x=explode("-",$d);
    if ((count($x)==1) && strlen(trim($d))==4) { //only year
      $d2=  mktime(0, 0, 0, 1, 1, $d);
    }
    else {
      $d2=  mktime(0, 0, 0, $x[1], $x[2], $x[0]);
    }
    return $d2;
  }
  elseif ($settings['dateformat']=="dmy"){
    $x=explode("/",$d);
    if ((count($x)==1) && strlen(trim($d))==4) { //only year
      $d2=  mktime(0, 0, 0, 1, 1, $d);
    }
    else {
      $d2=  mktime(0, 0, 0, $x[1], $x[0], $x[2]);
    }
//echo "$d -> $d2<br>";
    return $d2;
  }
  elseif ($settings['dateformat']=="mdy"){
    $x=explode("/",$d);
    if ((count($x)==1) && strlen(trim($d))==4) { //only year
      $d2=  mktime(0, 0, 0, 1, 1, $d);
    }
    else {
      $d2=  mktime(0, 0, 0, $x[0], $x[1], $x[2]);
    }
    return $d2;
  }
  return "";
}

//remove invalid filename characters
function validfn($s) {
  $f =preg_split('//u', 'ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΣΤΥΦΧΨΩΪΫΌΎΏΆΈΰαβγδεζηθικλμνξοπρςστυφχψωίϊΐϋόύώάέΰ');
  $t =preg_split('//u', 'ABGDEZHUIKLMNJOPRSSTYFXCVIUOUVAEUabgdezhuiklmnjoprsstyfxcviiiuouvaeu');
  $s=str_replace($f,$t,$s);
  $reserved = preg_quote('\/:*?"<>|', '/');
  $s=preg_replace("/([-\\x00-\\x20\\x7f-\\xff{$reserved}])/e", "", $s); 
  $s=strtolower($s);
  return $s;
}


//encode string for sql/html
function strenc($s)
{
  $s=htmlspecialchars($s,ENT_QUOTES,"UTF-8");
  return $s;
}
//////////////////// Database functions /////
// check permissions, log errors and transactions
// 
//encode string for sql/html

//for insert, update, delete
function db_exec($dbh,$sql,$skipauth=0,$skiphist=0,&$wantlastid=0)
{
global $authstatus,$userdata, $remaddr, $dblogsize,$errorstr,$errorbt;

  if (!$skipauth && !$authstatus) {echo "<big><b>Not logged in</b></big><br>";return 0;}
  if (stristr($sql,"insert ")) $skiphist=1; //for lastid function to work.

  //find user access
  $usr=$userdata[0]['username'];
  $sqlt="SELECT usertype FROM users where username='$usr'";
  $sth=$dbh->prepare($sqlt);
  $sth->execute();
  $ut=$sth->fetch(PDO::FETCH_ASSOC);
  $usertype=($ut['usertype']);
  $sth->closeCursor();

  if (!$skipauth && $usertype && (stristr($sql,"DELETE") || stristr($sql,"UPDATE") || stristr($sql,"INSERT")) 
      && !stristr($sql," tt ")) { /*tt:temporary table used for complex queries*/
    echo "<big><b>Access Denied, user '$usr' is read-only</b></big><br>";
    return 0;
  }

  $r=$dbh->exec($sql);
  $error = $dbh->errorInfo();
  if($error[0] && isset($error[2])) {
    $errorstr= "<br><b>db_exec:DB Error: ($sql): ".$error[2]."<br></b>";
    $errorbt = debug_backtrace();
    echo "</table></table></div>\n<pre>".$errorstr;
    print_r ($errorbt);
    return 0;
  }
  $wantlastid=$dbh->lastInsertId();

  if (!$skiphist) {
    $hist="";
    $t=time();
    $escsql=str_replace("'","''",$sql);
    $histsql="INSERT into history (date,sql,ip,authuser) VALUES ($t,'$escsql','$remaddr','".$_COOKIE["itdbuser"]."')";
    //update history table
    $rh=$dbh->exec($histsql);
    $lasthistid=$dbh->lastInsertId();

    $error = $dbh->errorInfo();
    if($error[0] && isset($error[2])) {
      $errorstr= "<br><b>HIST DB Error: ($histsql): ".$error[2]."<br></b>";
      $errorbt = debug_backtrace();
      echo $errorstr;
      print_r ($errorbt);
      return 0;
    }
    else { /* remove old history entries */
	$lastkeep=(int)($lasthistid)-$dblogsize;
	$sql="DELETE from history where id<$lastkeep";
	$sth=$dbh->exec($sql);
    }

  }
  return $r;
} //db_exec

//for select
function db_execute($dbh,$sql,$skipauth=0)
{
  global $authstatus,$errorstr,$errorbt;
  if (!$skipauth && !$authstatus) {echo "<big><b>Not logged in</b></big><br>";return 0;}
  $sth = $dbh->prepare($sql);
  $error = $dbh->errorInfo();
  if($error[0] && isset($error[2])) {
    $errorstr= "\n<br><b>db_execute:DB Error: ($sql): ".$error[2]."<br></b>\n";
    $errorbt= debug_backtrace();

    echo "</table></table></div>\n<pre>".$errorstr;
    print_r ($errorbt);
    echo "</pre>";

    return 0;
  }
  $sth->execute();
  return $sth;
}

function opendb($dbfile) {
    global $dbh;
    //open db
    try {
      $dbh = new PDO("sqlite:$dbfile");
    } 
    catch (PDOException $e) {
      print "Open database Error!: " . $e->getMessage() . "<br>";
      die();
    }
    return $dbh;
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);


    //$ret = $dbh->exec("PRAGMA case_sensitive_like = 0;");

}

function ckdberr($resource) {
    global $errorstr;
    $error = $resource->errorInfo();
    if($error[0] && isset($error[2])) {
        $errorstr= $error[2];
        $errorbt = debug_backtrace();
        logerr($errorstr."   BACKTRACE: ".$errorbt);
        return 1;
    }
    return 0;
}


/*execute with prepared statements
Example:
        $sql="SELECT * from tablename where id=:id order by date";
        $stmt=db_execute($dbh,$sql,array('id'=>$items['id']));
        $res=$stmt->fetch(PDO::FETCH_ASSOC);
*/

function db_execute2($dbh,$sql,$params=NULL) {
    global $errorstr,$errorbt,$errorno;

    $sth = $dbh->prepare($sql);
    $error = $dbh->errorInfo();

    if(((int)$error[0]||(int)$error[1]) && isset($error[2])) {
        $errorstr= "DB Error: ($sql): <br>\n".
            $error[2]."<br>\nParameters:"."params\n";
            //implode(",",$params);
        $errorbt= debug_backtrace();
        $errorno=$error[1]+$error[0];
        logerr("$errorstr BACKTRACE:".$errorbt);
        return 0;
    }

    if (is_array($params))
        $sth->execute($params);
    else
        $sth->execute();

    $error = $sth->errorInfo();
    if(((int)$error[0]||(int)$error[1]) && isset($error[2])) {
        $errorstr= "DB Error: ($sql): <br>\n".$error[2]."<br>\nParameters:".implode(",",$params);
        $errorbt= debug_backtrace();
        $errorno=$error[1]+$error[0];
        logerr("$errorstr BACKTRACE:".$errorbt);
    }

    return $sth;
}



function connect_to_ldap_server($ldap_server,$ldap_port,$username,$passwd,$ldap_dn) {
    global $gen_error,$gen_errorstr;

    $ds=ldap_connect($ldap_server,$ldap_port);  // must be a valid LDAP server!
    //echo "connect result is " . $ds . "<br />\n";
    //Check for passwd too - otherwise empty passwords are accepted!
    if(($ds) && ($passwd)){
        $dn="uid=".$username.",".$ldap_dn;
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r=@ldap_bind($ds,$dn, $passwd);
        //Catch possible error
        $r_error = "LDAP Error #".ldap_errno($ds).": ".ldap_error($ds)."<br>";
        if(!$r){
            //Try with other DN syntax --> username@mydomain.com
            //Active Directory compatible / in case UID is not set
            $r2=@ldap_bind($ds,$username.$ldap_dn, $passwd);
            //Catch possible error
            $r2_error = "LDAP Error #".ldap_errno($ds).": ".ldap_error($ds)."<br>";
            if(!$r2){
                //Try with a third DN syntax --> just the username
                //Active Directory compatible / in case UID is not set
                $r3=@ldap_bind($ds,$username.$ldap_dn, $passwd);
                //Catch possible error
                $r3_error = "LDAP Error #".ldap_errno($ds).": ".ldap_error($ds)."<br>";
                if(!$r3){
                    //Show errors only if both attempts failed
                    echo "Login with DN ".$dn." failed: ".$r_error;
                    echo "Login with DN ".$username.$ldap_dn." failed: ".$r2_error;
                    echo "Login with DN ".$username." failed: ".$r3_error;
                    $gen_errorstr="ldap_bind: ".ldap_error($ds);
                    $gen_error=100;
                    ldap_close($ds);
                    return FALSE;
                }
                else {
                    return $ds;
                }
            }
            else {
                return $ds;
            }
        }
        else {
            return $ds;
        }
    }
    else {
        return FALSE;
    }
}

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
        $still_assigned=array();
        $assigned_id=array();
        $assigned=false;
        $disperr="";
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
            $disperr.="<div class='ui-state-error ui-corner-all' style='padding: 0 .7em;min-width:930px;margin-bottom:3px;margin-top:6px;margin-left:120px;margin-right:15px;'>
                       <p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;'></span> <strong align=left;>Error: Cannot delete the following user";
            if(count($still_assigned)!=1) $disperr.="s";
            $disperr.=": ";

            for ($i=0;$i<count($still_assigned);$i++)
            {
                $disperr.= "<a href='$scriptname?action=edituser&amp;id=$assigned_id[$i]' style='color:#2233DD'>".$still_assigned[$i];
                if($i+1 !=count($still_assigned)) $disperr.=", ";
                $disperr.="</a>";
            }
            $disperr.="<br>User";
            if(count($still_assigned)!=1) $disperr.="s";
            $disperr.=" still";
            if(count($still_assigned)==1) $disperr.=" has Items assigned!</strong></p></div>";
            else $disperr.=" have Items assigned!</strong></p></div>";
        }
        return $disperr;
    }
}

?>
