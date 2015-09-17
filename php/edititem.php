<?php 

/* Spiros Ioannou 2009 , sivann _at_ gmail.com */
if (!isset($initok)) {echo "do not run this script directly";exit;}

//form variables
$formvars=array("itemtypeid","function","manufacturerid","label",
  "warrinfo","model","sn","sn2","sn3","locationid","locareaid",
  "origin","warrantymonths","purchasedate","purchprice","dnsname","userid",
  "comments","maintenanceinfo","ispart","hd",
  "cpu","cpuno","corespercpu", "ram", "rackmountable", "rackid","rackposition","rackposdepth","usize","status",
  "macs","ipv4","ipv6","remadmip","panelport","switchid","switchport","ports");

//Log settings for actions
$sql="SELECT uselog,log_actions FROM settings";
$sth=$dbh->query($sql);
$r=$sth->fetch(PDO::FETCH_ASSOC);
$log=$r['log_actions'];
$uselog=$r['uselog'];

/* delete item */
if (isset($_GET['delid'])) { 
  //first handle file associations
  //get a list of files associated with us
  $f=itemid2files($delid,$dbh);
  for ($fids=array(),$c=0;$c<count($f);$c++) {
    array_push($fids,$f[$c]['id']);
  }

  //remove file links
  $sql="DELETE from item2file where itemid=$delid";
  $sth=db_exec($dbh,$sql);

  //for each file: check if others link to it, and if not remove it:
  for ($c=0;$c<count($fids);$c++) {
    $nlinks=countfileidlinks($fids[$c],$dbh);
    if ($nlinks==0) delfile($fids[$c],$dbh);
  }

  //delete invoice links
  $sql="DELETE from item2inv where itemid=".$_GET['delid'];
  $sth=db_exec($dbh,$sql);

  //delete software links
  $sql="DELETE from item2soft where itemid=".$_GET['delid'];
  $sth=db_exec($dbh,$sql);

  //delete inter-item links
  $sql="DELETE from itemlink where itemid1=".$_GET['delid']." or itemid2=".$_GET['delid'];
  $sth=db_exec($dbh,$sql);

  //nullify TAGS
  $sql="UPDATE tag2item set itemid=null where itemid=".$_GET['delid'];
  $sth=db_exec($dbh,$sql);

  //delete item 
  $sql="DELETE from items where id=".$_GET['delid'];
  $sth=db_exec($dbh,$sql);

  echo "<script>document.location='$scriptname?action=listitems'</script>";
  echo "<a href='$scriptname?action=listitems'>Go here</a></body></html>"; 
  exit;
}

if (isset($_GET['cloneid'])) { 
  $cols="itemtypeid , function, manufacturerid ,model,origin,warrantymonths ,purchasedate ,purchprice, maintenanceinfo,".
        "comments,ispart ,hd,cpu,ram,locationid ,usize ,rackmountable ,label,status,cpuno , corespercpu , warrinfo";

  $sql="insert into items ($cols) ".
     " select $cols from items ".
     " where id={$_GET['cloneid']}";
  $sth=db_exec($dbh,$sql);

  $lastid=$dbh->lastInsertId();
  $newid=$lastid;
  echo "<script>document.location='$scriptname?action=edititem&id=$newid'</script>";
  echo "<a href='$scriptname?action=edititem&amp;id=$newid'>Go here</a></body></html>"; 
  exit;
}








/* delete associated file */
if (isset($_GET['delfid'])) { /* displayed from showfiles() */

  //remove file link
  $sql="DELETE from item2file where itemid=$id AND fileid=".$_GET['delfid'];
  $sth=db_exec($dbh,$sql);

  //check if others point to this file
  $nlinks=countfileidlinks($_GET['delfid'],$dbh);
  if ($nlinks==0) delfile($_GET['delfid'],$dbh);
  //echo "$nlinks DELETED ".$_GET['delfid'];
  echo "<script>window.location='$scriptname?action=$action&id=$id'</script> ";
  echo "<br><a href='$scriptname?action=$action&id=$id'>Go here</a></body></html>";
  exit;
}

//check for arguments
if (!isset($_GET['id'])) {echo "edititem:missing arguments";exit;}

/* update item data */
//if came here from a form post, update db with new values
if (isset($_POST['itemtypeid']) && ($_GET['id']!="new") && isvalidfrm()) {
//get form post variables and create the sql query
  $set="";
  $c=count($formvars);$i=0;
  foreach ($formvars as $formvar){
    if (isset($_POST[$formvar]))
      $$formvar=trim($_POST[$formvar]);//create $sn from $_POST['sn']
    else {$i++;continue;} //for files which are in _FILES not in _POST

    if ($formvar == "purchasedate") $$formvar=ymd2sec($$formvar);
    if ($formvar == "maintend") $$formvar=ymd2sec($$formvar);
    if ($formvar == "warrantymonths") {
		if ($$formvar=="") 
		  $$formvar="NULL";
		else
		  $$formvar=(int)($$formvar);
      $set.="$formvar=".($$formvar).""; //without quotes for integer
    }
    else {
      $set.="$formvar='".htmlspecialchars($$formvar,ENT_QUOTES,"UTF-8")."'";
	}
    $set.=", ";
    $i++;
  }
  $set[strlen($set)-2]=" ";
  if (!isset($_POST['itlnk'])) $itlnk=array();
  if (!isset($_POST['invlnk'])) $invlnk=array();

if($uselog) {
// Get current info of item
  $sql="SELECT sn,sn2,sn3,dnsname,macs,ipv4,ipv6,rackposition,rackposdepth FROM items where items.id='$id'";
  $sth=db_execute($dbh,$sql);
  $curriteminfo=$sth->fetchAll(PDO::FETCH_ASSOC);
  $curriteminfo=$curriteminfo[0];

  $currsn=$curriteminfo['sn'];
  $currsn2=$curriteminfo['sn2'];
  $currsn3=$curriteminfo['sn3'];
  $currdnsname=$curriteminfo['dnsname'];
  $currmacs=$curriteminfo['macs'];
  $curripv4=$curriteminfo['ipv4'];
  $curripv6=$curriteminfo['ipv6'];
  $currrackposition=$curriteminfo['rackposition'];
  $currrackposdepth=$curriteminfo['rackposdepth'];

// Get new info of item
  $newsn=$_POST['sn'];
  $newsn2=$_POST['sn2'];
  $newsn3=$_POST['sn3'];
  $newdnsname=$_POST['dnsname'];
  $newmacs=$_POST['macs'];
  $newipv4=$_POST['ipv4'];
  $newipv6=$_POST['ipv6'];
  $newrackposition=$_POST['rackposition'];
  $newrackposdepth=$_POST['rackposdepth'];

// Get current user of item
  $sql="SELECT items.userid,users.username from users,items where userid=users.id and items.id='$id'";
  $sth=db_execute($dbh,$sql);
  $curruser=$sth->fetchAll(PDO::FETCH_ASSOC);
  $curruser=$curruser[0];

// Get new user of item
  $sql="SELECT username from users where id=$userid";
  $sth=db_execute($dbh,$sql);
  $newuser=$sth->fetchAll(PDO::FETCH_ASSOC);
  $newuser=$newuser[0];

// Get current status of item
  $sql="SELECT items.status,statustypes.statusdesc from statustypes,items where status=statustypes.id and items.id='$id'";
  $sth=db_execute($dbh,$sql);
  $currstatus=$sth->fetchAll(PDO::FETCH_ASSOC);
  $currstatus=$currstatus[0];

// Get new status of item
  $sql="SELECT id,statusdesc from statustypes where id=$status";
  $sth=db_execute($dbh,$sql);
  $newstatus=$sth->fetchAll(PDO::FETCH_ASSOC);
  $newstatus=$newstatus[0];

// Get current location of item
  $sql="SELECT items.locationid,locations.name,locations.floor from locations,items where locationid=locations.id and items.id='$id'";
  $sth=db_execute($dbh,$sql);
  $currlocation=$sth->fetchAll(PDO::FETCH_ASSOC);
  $currlocation=$currlocation[0];
  $currlocationid=$currlocation['locationid'];
  $currlocationname=$currlocation['name'];
  $currlocationfloor="Floor ".$currlocation['floor'];

//Check only if not empty to avoid SQL errors
if(!empty($locationid))
{
// Get new location of item
  $sql="SELECT id,name,floor from locations where id=$locationid";
  $sth=db_execute($dbh,$sql);
  $newlocation=$sth->fetchAll(PDO::FETCH_ASSOC);
  $newlocation=$newlocation[0];
  $newlocationid=$newlocation['id'];
  $newlocationname=$newlocation['name'];
  $newlocationfloor="Floor ".$newlocation['floor'];
}
else
{
  $newlocation = $newlocationid = $newlocationname = $newlocationfloor= "";
}

// Get current location and area of item
  $sql="SELECT items.locationid,items.locareaid,locations.name,locations.floor,locareas.areaname from locations,locareas,items where items.locationid=locations.id and locareaid=locareas.id and items.locationid=locareas.locationid and items.id='$id'";
  $sth=db_execute($dbh,$sql);
  $currlocationarea=$sth->fetchAll(PDO::FETCH_ASSOC);
  $currlocationarea=$currlocationarea[0];
  $currlocationarealocationid=$currlocationarea['locationid'];
  $currlocationarealocareaid=$currlocationarea['locareaid'];
  $currlocationareaareaname=$currlocationarea['areaname'];

//Check only if not empty to avoid SQL errors
if(!empty($locareaid))
{
// Get new location area of item
  $sql="SELECT id,areaname from locareas where locareas.id=$locareaid and locareas.locationid=$newlocationid";
  $sth=db_execute($dbh,$sql);
  $newlocationarea=$sth->fetchAll(PDO::FETCH_ASSOC);
  $newlocationarea=$newlocationarea[0];
  $newlocationareaid=$newlocationarea['id'];
  $newlocationareaareaname=$newlocationarea['areaname'];
}
else
{
  $newlocationarea = $newlocationareaid = $newlocationareaareaname = "";
}

// Get current rack of item
  $sql="SELECT items.rackid,racks.id,racks.label,racks.model,racks.usize from racks,items where items.rackid=racks.id and items.id='$id'";
  $sth=db_execute($dbh,$sql);
  $currrack=$sth->fetchAll(PDO::FETCH_ASSOC);
  $currrack=$currrack[0];
  $currrackid=$currrack['id'];
  $currracklabel=$currrack['label'];
  $currrackmodel=$currrack['model'];
  $currrackusize=$currrack['usize']."U";
  $currrackname=$currracklabel.", ".$currrackusize." ".$currrackmodel;

if(!empty($rackid))
{
// Get new rack of item
  $sql="SELECT id,label,model,usize from racks where id=$rackid";
  $sth=db_execute($dbh,$sql);
  $newrack=$sth->fetchAll(PDO::FETCH_ASSOC);
  $newrack=$newrack[0];
  $newrackid=$newrack['id'];
  $newracklabel=$newrack['label'];
  $newrackmodel=$newrack['model'];
  $newrackusize=$newrack['usize']."U";
  $newrackname=$newracklabel.", ".$newrackusize." ".$newrackmodel;
  print_r($newrack);
}
else
{
  $newrack = $newrackid = $newracklabel = $newrackusize = $newrackname = "";
}


$actions_entry=array();
// changed user
if ($log&2 && $userid!=$curruser['userid']) $actions_entry[]="Updated user from {$curruser['username']} to {$newuser['username']}";

// changed status
if ($log&4 && $newstatus['id']!=$currstatus['status']) $actions_entry[]="Updated status from {$currstatus['statusdesc']} to {$newstatus['statusdesc']}";

if($log&8) { // added, removed or changed serial 1
    if($newsn!=$currsn && empty($currsn) && !empty($newsn)) $actions_entry[]="Added S/N {$newsn}";
    elseif($newsn!=$currsn && !empty($currsn) && empty($newsn)) $actions_entry[]="Removed S/N {$currsn}";
    elseif($newsn!=$currsn) $actions_entry[]="Updated S/N from {$currsn} to {$newsn}";

    // added, removed or changed serial 2
    if($newsn2!=$currsn2 && empty($currsn2) && !empty($newsn2)) $actions_entry[]="Added S/N 2 {$newsn2}";
    elseif($newsn2!=$currsn2 && !empty($currsn2) && empty($newsn2)) $actions_entry[]="Removed S/N 2 {$currsn2}";
    elseif($newsn2!=$currsn2) $actions_entry[]="Updated S/N 2 from {$currsn2} to {$newsn2}";

    // added, removed or changed serial 3
    if($newsn3!=$currsn3 && empty($currsn3) && !empty($newsn3)) $actions_entry[]="Added S/N 3 {$newsn3}";
    elseif($newsn3!=$currsn3 && !empty($currsn3) && empty($newsn3)) $actions_entry[]="Removed S/N 3 {$currsn3}";
    elseif($newsn3!=$currsn3) $actions_entry[]="Updated S/N 3 from {$currsn3} to {$newsn3}";
}

if ($log&16) { //added, removed or changed location or area/room
    if($newlocationid!=$currlocationid && empty($currlocationid) && !empty($newlocationid)) $actions_entry[]="Added location {$newlocationname}, {$newlocationfloor}";
    elseif($newlocationid!=$currlocationid && !empty($currlocationid) && empty($newlocationid)) $actions_entry[]="Removed location {$currlocationname}, {$currlocationfloor}";
    elseif($newlocationid!=$currlocationid && $newlocationname==$currlocationname) $actions_entry[]="Updated location from {$currlocationname}, {$currlocationfloor} to {$newlocationfloor}";
    elseif($newlocationid!=$currlocationid && $newlocationname!=$currlocationname) $actions_entry[]="Updated location from {$currlocationname}, {$currlocationfloor} to {$newlocationname}, {$newlocationfloor}";

    if($newlocationareaid!=$currlocationarealocareaid && empty($currlocationarealocareaid) && !empty($newlocationareaid)) $actions_entry[]="Added area/room {$newlocationareaareaname}";
    elseif($newlocationareaid!=$currlocationarealocareaid && !empty($currlocationarealocareaid) && empty($newlocationareaid)) $actions_entry[]="Removed area/room {$currlocationareaareaname}";
    elseif($newlocationareaid!=$currlocationarealocareaid) $actions_entry[]="Updated area/room from {$currlocationareaareaname} to {$newlocationareaareaname}";
}

if ($log&32) { //added, removed or changed rack or rack position
    if($newrackid!=$currrackid && empty($currrackid) && !empty($newrackid)) $actions_entry[]="Added rack {$newrackname}";
    elseif($newrackid!=$currrackid && !empty($currrackid) && empty($newrackid)) $actions_entry[]="Removed rack {$currrackname}";
    elseif($newrackid!=$currrackid) $actions_entry[]="Updated rack from {$currrackname} to {$newrackname}";

    $currrackposdepthlabel="";
    if($currrackposdepth == '6') $currrackposdepthlabel=" (FM-)";
    if($currrackposdepth == '3') $currrackposdepthlabel=" (-MB)";
    if($currrackposdepth == '4') $currrackposdepthlabel=" (F--)";
    if($currrackposdepth == '2') $currrackposdepthlabel=" (-M-)";
    if($currrackposdepth == '1') $currrackposdepthlabel=" (--B)";
    if($currrackposdepth == '7') $currrackposdepthlabel=" (FMB)";

    $newrackposdepthlabel="";
    if($newrackposdepth == '6') $newrackposdepthlabel=" (FM-)";
    if($newrackposdepth == '3') $newrackposdepthlabel=" (-MB)";
    if($newrackposdepth == '4') $newrackposdepthlabel=" (F--)";
    if($newrackposdepth == '2') $newrackposdepthlabel=" (-M-)";
    if($newrackposdepth == '1') $newrackposdepthlabel=" (--B)";
    if($newrackposdepth == '7') $newrackposdepthlabel=" (FMB)";

    $rack_posdepth_msg="";
    if($newrackposdepth!=$currrackposdepth && empty($currrackposdepth) && !empty($newrackposdepth)) $rack_posdepth_msg=$newrackposdepthlabel;
    elseif($newrackposdepth!=$currrackposdepth && !empty($currrackposdepth) && empty($newrackposdepth)) $rack_posdepth_msg=$currrackposdepthlabel;
    elseif($newrackposdepth==$currrackposdepth && empty($currrackposdepth) && !empty($newrackposdepth)) $rack_posdepth_msg=$newrackposdepthlabel;
    elseif($newrackposdepth==$currrackposdepth && !empty($currrackposdepth) && empty($newrackposdepth)) $rack_posdepth_msg=$currrackposdepthlabel;
    elseif($newrackposdepth==$currrackposdepth) $rack_posdepth_msg=$currrackposdepthlabel;
    elseif($currrackposdepth!=$newrackposdepth) $rack_posdepth_msg=$newrackposdepthlabel;

    if($newrackposition!=$currrackposition && empty($currrackposition) && !empty($newrackposition)) $actions_entry[]="Added rack position {$newrackposition}{$rack_posdepth_msg}";
    elseif($newrackposition!=$currrackposition && !empty($currrackposition) && empty($newrackposition) && !empty($newrackposdepth)) $actions_entry[]="Removed rack position {$currrackposition}";
    elseif($newrackposition!=$currrackposition && !empty($currrackposition) && empty($newrackposition) && empty($newrackposdepth)) $actions_entry[]="Removed rack position {$currrackposition}{$currrackposdepthlabel}";
    elseif($newrackposition!=$currrackposition) $actions_entry[]="Updated rack position from {$currrackposition}{$currrackposdepthlabel} to {$newrackposition}{$newrackposdepthlabel}";
    elseif($newrackposition==$currrackposition && empty($currrackposdepth) && !empty($newrackposdepth)) $actions_entry[]="Added rack position {$currrackposition} depth {$newrackposdepthlabel}";
    elseif($newrackposition==$currrackposition && !empty($currrackposdepth) && empty($newrackposdepth)) $actions_entry[]="Removed rack position depth {$currrackposdepthlabel}";
    elseif($newrackposition==$currrackposition && $newrackposdepth!=$currrackposdepth) $actions_entry[]="Updated rack position {$currrackposition} depth from {$currrackposdepthlabel} to {$newrackposdepthlabel}";
}

if($log&64) { // added, removed or changed dnsname
    if($newdnsname!=$currdnsname && empty($currdnsname) && !empty($newdnsname)) $actions_entry[]="Added DNS Name {$newdnsname}";
    elseif($newdnsname!=$currdnsname && !empty($currdnsname) && empty($newdnsname)) $actions_entry[]="Removed DNS Name {$currdnsname}";
    elseif($newdnsname!=$currdnsname) $actions_entry[]="Updated DNS Name from {$currdnsname} to {$newdnsname}";
}

if($log&128) { // added, removed or changed macs
    if($newmacs!=$currmacs && empty($currmacs) && !empty($newmacs)) $actions_entry[]="Added MACs {$newmacs}";
    elseif($newmacs!=$currmacs && !empty($currmacs) && empty($newmacs)) $actions_entry[]="Removed MACs {$currmacs}";
    elseif($newmacs!=$currmacs) $actions_entry[]="Updated MACs from {$currmacs} to {$newmacs}";
}

if($log&256) { // added, removed or changed ipv4
    if($newipv4!=$curripv4 && empty($curripv4) && !empty($newipv4)) $actions_entry[]="Added IPv4 {$newipv4}";
    elseif($newipv4!=$curripv4 && !empty($curripv4) && empty($newipv4)) $actions_entry[]="Removed IPv4 {$curripv4}";
    elseif($newipv4!=$curripv4) $actions_entry[]="Updated IPv4 from {$curripv4} to {$newipv4}";

    // added, removed or changed ipv6
    if($newipv6!=$curripv6 && empty($curripv6) && !empty($newipv6)) $actions_entry[]="Added IPv6 {$newipv6}";
    elseif($newipv6!=$curripv6 && !empty($curripv6) && empty($newipv6)) $actions_entry[]="Removed IPv6 {$curripv6}";
    elseif ($newipv6!=$curripv6) $actions_entry[]="Updated IPv6 from {$curripv6} to {$newipv6}";
}

if($log&512) {
    //Get current links and compare with new item links
    $sql="SELECT itemid2 from itemlink where itemid1=$id";
    $sth=db_execute($dbh,$sql);
    $r=$sth->fetchAll(PDO::FETCH_ASSOC);
    $curriteminks=array();
    for ($i=0;$i<count($r);$i++)
    {
        $curritemlinks[]=$r[$i]['itemid2'];
    }

    // Determine removed and added links
    if(!is_array($itlnk)) $newitemlinks=array();
    else $newitemlinks = $itlnk;
    $removeditemlinks = array_values(array_diff($curritemlinks, $newitemlinks));
    $addeditemlinks = array_values(array_diff($newitemlinks, $curritemlinks));

    // Write messages to action log
    for ($i=0;$i<count($removeditemlinks);$i++)
    {
        $actions_entry[]="Removed associated item #{$removeditemlinks[$i]}";
    }

    for ($i=0;$i<count($addeditemlinks);$i++)
    {
        $actions_entry[]="Added association with item #{$addeditemlinks[$i]}";
    }
}

if($log&1024) {
    //Get current links and compare with new item links
    $sql="SELECT invid from item2inv where itemid=$id";
    $sth=db_execute($dbh,$sql);
    $r=$sth->fetchAll(PDO::FETCH_ASSOC);
    $currinvlinks=array();
    for ($i=0;$i<count($r);$i++)
    {
        $currinvlinks[]=$r[$i]['invid'];
    }

    // Determine removed and added links
     if(!is_array($invlnk)) $newinvlinks=array();
    else $newinvlinks = $invlnk;
    $removedinvlinks = array_values(array_diff($currinvlinks, $newinvlinks));
    $addedinvlinks = array_values(array_diff($newinvlinks, $currinvlinks));

    // Write messages to action log
    for ($i=0;$i<count($removedinvlinks);$i++)
    {
        $actions_entry[]="Removed associated invoice #{$removedinvlinks[$i]}";
    }

    for ($i=0;$i<count($addedinvlinks);$i++)
    {
        $actions_entry[]="Added association with invoice #{$addedinvlinks[$i]}";
    }
}

if($log&2048) {
    //Get current links and compare with new item links
    $sql="SELECT softid from item2soft where itemid=$id";
    $sth=db_execute($dbh,$sql);
    $r=$sth->fetchAll(PDO::FETCH_ASSOC);
    $currsoftlinks=array();
    for ($i=0;$i<count($r);$i++)
    {
        $currsoftlinks[]=$r[$i]['softid'];
    }

    // Determine removed and added links
    if(!is_array($softlnk)) $newsoftlinks=array();
    else $newsoftlinks = $softlnk;
    $removedsoftlinks = array_values(array_diff($currsoftlinks, $newsoftlinks));
    $addedsoftlinks = array_values(array_diff($newsoftlinks, $currsoftlinks));

    // Write messages to action log
    for ($i=0;$i<count($removedsoftlinks);$i++)
    {
        $actions_entry[]="Removed associated software #{$removedsoftlinks[$i]}";
    }

    for ($i=0;$i<count($addedsoftlinks);$i++)
    {
        $actions_entry[]="Added association with software #{$addedsoftlinks[$i]}";
    }
}

if($log&4096) {
    //Get current links and compare with new item links
    $sql="SELECT contractid from contract2item where itemid=$id";
    $sth=db_execute($dbh,$sql);
    $r=$sth->fetchAll(PDO::FETCH_ASSOC);
    $currcontrlinks=array();
    for ($i=0;$i<count($r);$i++)
    {
        $currcontrlinks[]=$r[$i]['contractid'];
    }

    // Determine removed and added links
    if(!is_array($contrlnk)) $newcontrlinks=array();
    else $newcontrlinks = $contrlnk;
    $removedcontrlinks = array_values(array_diff($currcontrlinks, $newcontrlinks));
    $addedcontrlinks = array_values(array_diff($newcontrlinks, $currcontrlinks));

    // Write messages to action log
    for ($i=0;$i<count($removedcontrlinks);$i++)
    {
        $actions_entry[]="Removed associated contract #{$removedcontrlinks[$i]}";
    }

    for ($i=0;$i<count($addedcontrlinks);$i++)
    {
        $actions_entry[]="Added association with contract #{$addedcontrlinks[$i]}";
    }
}

foreach($actions_entry as $m)
{
    $sql="INSERT into actions (itemid, actiondate,description,invoiceinfo,isauto,entrydate) values ".
	 "($id,".time().",'$m by {$_COOKIE["itdbuser"]}' , '',1,".time().")";
    db_exec($dbh,$sql);
}

} //end action log

  $sql="UPDATE items set $set WHERE id=$id";
  db_exec($dbh,$sql);


  //update item links
  //remove old links for this object
  $dbh->beginTransaction();
  $sql="delete from itemlink where itemid1=$id";
  db_exec($dbh,$sql);
  //add new links for each checked checkbox
  for ($i=0;$i<count($itlnk);$i++) {
    $sql="INSERT into itemlink (itemid1, itemid2) values ($id,".$itlnk[$i].")";
    db_exec($dbh,$sql);
  }
  //update invoice links
  //remove old links for this object
  $sql="delete from item2inv where itemid=$id";
  db_exec($dbh,$sql);
  //add new links for each checked checkbox
  for ($i=0;$i<count($invlnk);$i++) {
    $sql="INSERT into item2inv (itemid, invid) values ($id,".$invlnk[$i].")";
    db_exec($dbh,$sql);
  }
  $dbh->commit();

  //update software - item links 
  //remove old links for this object
  $sql="delete from item2soft where itemid=$id";
  db_exec($dbh,$sql);
  //add new links for each checked checkbox
  for ($i=0;$i<count($softlnk);$i++) {
    $sql="INSERT into item2soft (itemid,softid) values ($id,".$softlnk[$i].")";
    db_exec($dbh,$sql);
  }

  //update contract - item links 
  //remove old links for this object
  $sql="delete from contract2item where itemid=$id";
  db_exec($dbh,$sql);
  //add new links for each checked checkbox
  for ($i=0;$i<count($contrlnk);$i++) {
    $sql="INSERT into contract2item (itemid,contractid) values ($id,".$contrlnk[$i].")";
    db_exec($dbh,$sql);
  }
} //if updating
/* add new item */
elseif (isset($_POST['itemtypeid']) && ($_GET['id']=="new")&&isvalidfrm()) {

  //ok, save new item
  //find a new ID 
  //handle file uploads
  $photofn="";
  $manualfn="";

  foreach($_POST as $k => $v) { if (!is_array($v)) ${$k} = (trim($v));}
  $purchasedate2=ymd2sec($purchasedate);// mktime(0, 0, 0, $x[1], $x[0], $x[2]);

  $mend=ymd2sec($maintend);

  if ($switchid=="") $switchid="NULL";
  if ($usize=="") $usize="NULL";
  if ($locationid=="") $locationid="NULL";
  if ($locareaid=="") $locareaid="NULL";
  if ($rackid=="") $rackid="NULL";
  if ($rackposition=="") $rackposition="NULL";
  if ($userid=="") $userid="NULL";
  $warrantymonths=(int)$warrantymonths;
  if (!$warrantymonths || !strlen($warrantymonths) || !is_integer($warrantymonths)) $warrantymonths="NULL";




  //// STORE DATA
  $sql="INSERT into items (label, itemtypeid, function, manufacturerid, ".
  " warrinfo, model, sn, sn2, sn3, origin, warrantymonths, purchasedate, purchprice, ".
  " dnsname, userid, locationid,locareaid, maintenanceinfo,  ".
  " comments,ispart, rackid, rackposition,rackposdepth, rackmountable, ".
  " usize, status, macs, ipv4, ipv6, remadmip, ".
  " hd, cpu,cpuno,corespercpu, ram, ".
  " panelport, switchid, switchport, ports) VALUES ".
  " ('$label', '$itemtypeid', '$function', '$manufacturerid', ".
  " '$warrinfo', '$model', '$sn', '$sn2', '$sn3', '$origin', ".
  "  $warrantymonths, '$purchasedate2', ".
  " '$purchprice', '$dnsname', $userid, $locationid,$locareaid, '$maintenanceinfo', ".
  " '". htmlspecialchars($comments,ENT_QUOTES,'UTF-8')  ."',$ispart, $rackid, $rackposition,$rackposdepth, $rackmountable, " .
  "  $usize, $status, '$macs', '$ipv4', '$ipv6', '$remadmip', ".
  " '$hd', '$cpu', '$cpuno', '$corespercpu', '$ram', ".
  " '$panelport', $switchid,  '$switchport', '$ports' ) ";

  //echo $sql."<br>";
  db_exec($dbh,$sql);

  $lastid=$dbh->lastInsertId();
  $id=$lastid;

  //add new links for each checked checkbox
  if (isset($_POST['itlnk'])) {
    $itlnk=$_POST['itlnk'];
    for ($i=0;$i<count($itlnk);$i++) {
      $sql="INSERT into itemlink (itemid1, itemid2) values ($lastid,".$itlnk[$i].")";
      db_exec($dbh,$sql);
    }
  }//add item links

  //add new links for each checked checkbox
  if (isset($_POST['invlnk'])) {
    $itlnk=$_POST['invlnk'];
    for ($i=0;$i<count($invlnk);$i++) {
      $sql="INSERT into item2inv (itemid, invid) values ($lastid,".$invlnk[$i].")";
      db_exec($dbh,$sql);
    }
  }//add invoice links

  //update software - item links 
  //remove old links for this object
  $sql="DELETE from item2soft where itemid=$lastid";
  db_exec($dbh,$sql);
  //add new links for each checked checkbox
  for ($i=0;$i<count($softlnk);$i++) {
    $sql="INSERT into item2soft (itemid,softid) values ($lastid,".$softlnk[$i].")";
    db_exec($dbh,$sql);
  }

  //update contract - item links 
  //remove old links for this object
  $sql="DELETE from contract2item where itemid=$lastid";
  db_exec($dbh,$sql);
  //add new links for each checked checkbox
  for ($i=0;$i<count($contrlnk);$i++) {
    $sql="INSERT into contract2item (itemid,contractid) values ($lastid,".$contrlnk[$i].")";
    db_exec($dbh,$sql);
  }

  if($uselog && $log&1)
  {
  //add new action entry
  $sql="INSERT into actions (itemid, actiondate,description,invoiceinfo,isauto,entrydate) values ".
       "($lastid,".time().",'Item #$id added by {$_COOKIE["itdbuser"]}' , '',1,".time().")";
  db_exec($dbh,$sql);
  }

  print "\n<br><b>Added item <a href='$scriptname?action=edititem&amp;id=$lastid'>$lastid</a></b><br>\n";
  if ($lastid) echo "<script>window.location='$scriptname?action=edititem&id=$lastid'</script> "; //go to the new item

}//xxxadd new item

function isvalidfrm() {
global $dbh,$disperr,$err,$_POST;
  //check for mandatory fields
  $err="";
  $disperr="";
  if ($_POST['itemtypeid']=="") $err.="Missing Item Type<br>";
  if ($_POST['userid']=="") $err.="Missing User<br>";
  if ($_POST['manufacturerid']=="") $err.="Missing manufacturer<br>";
  if (!isset($_POST['rackmountable'])) $err.="Missing 'Rackmountable' classification<br>";
  if (!isset($_POST['ispart'])) $err.="Missing 'Part' classification<br>";
  if (!isset($_POST['status'])) $err.="Missing 'Status' classification<br>";
  if ($_POST['model']=="") $err.="Missing model<br>";


  $myid=$_GET['id'];
  if ($myid != "new" && is_numeric($myid) && (strlen($_POST['sn']) || strlen($_POST['sn2']))) {
	  $sql="SELECT id from items where  id <> $myid AND ((length(sn)>0 AND sn in ('{$_POST['sn']}', '{$_POST['sn2']}')) OR (length(sn2)>0 AND sn2 in ('{$_POST['sn']}', '{$_POST['sn2']}')))  LIMIT 1";
	  $sth=db_execute($dbh,$sql);
	  $dups=$sth->fetchAll(PDO::FETCH_ASSOC);
	  if (count($dups[0])) {
		  $err.="Duplicate SN with id <a href='$scriptname?action=edititem&amp;id={$dups[0]['id']}'><b><u>{$dups[0]['id']}</u></b></a>";
	  }
  }




  if (strlen($err)) {
      $disperr= "
      <div class='ui-state-error ui-corner-all' style='padding: 0 .7em;width:300px;margin-bottom:3px;'> 
	      <p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;'></span>
	      <strong>Error: Item not saved, correct these errors:</strong><br><div style='text-align:left'>$err</div></p>
      </div>
      ";
    return 0;
  }
  return 1;
}

require('itemform.php');
?>
