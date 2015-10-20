<?php

if (isset($_POST['labelaction']) && $_POST['labelaction']=="savepreset") {
  if (!strlen($_POST['name'])) {
    echo "<b><big>Not saved: specity preset name!</big></b>";
  }
  else {
    //damn checkboxes dont' post their name when "off" :
    if (!isset($wantbarcode)) $wantbarcode=0;
    if (!isset($wantheadertext)) $wantheadertext=0;
    if (!isset($wantheaderimage)) $wantheaderimage=0;
    if (!isset($wantnotext)) $wantnotext=0;
    if (!isset($wantraligntext)) $wantraligntext=0;

    foreach($_POST as $k => $v) { 
		${$k} = $v; 
		if (strstr($k,"want") && $v=="on")  //checkboxes are "on" when checked, we want "1"
		$$k=1;
    }

    $sql="INSERT INTO labelpapers ".
    " (rows,cols,lwidth,lheight, vpitch, hpitch, tmargin, bmargin, lmargin, rmargin, name,  ".
    " border, padding, fontsize, headerfontsize, barcodesize, idfontsize, wantbarcode, wantheadertext, wantheaderimage,  ".
    " headertext,image,imagewidth,imageheight,papersize,qrtext,wantnotext,wantraligntext) ".
    " values ($rows,$cols,$lwidth,$lheight, $vpitch, $hpitch, $tmargin, $bmargin, $lmargin, $rmargin, '$name', ".
    " $border, $padding, $fontsize, $headerfontsize,$barcodesize, $idfontsize, $wantbarcode, $wantheadertext, $wantheaderimage, ".
    " '$headertext', '$image', '$imagewidth', '$imageheight', '$papersize','".htmlentities($qrtext, ENT_QUOTES)."','$wantnotext','$wantraligntext' )";
    $sth=db_execute($dbh,$sql);
  }
}

if (isset($_POST['designaction']) && $_POST['designaction']=="savedesign") {
  if (!strlen($_POST['designname'])) {
    echo "<b><big>Not saved: specity design name!</big></b>";
  }
  else {

    foreach($_POST as $k => $v) {
		${$k} = $v;
    }

    $sql="INSERT INTO labeldesigns ".
    " (designname,rowbarcode,row1text, row1value, row2text, row2value, row3text, row3value, row4text, row4value,  ".
    " row5text, row5value, row6text, row6value, row7text, row7value, row8text, row8value) ".
    " values ('$designname','$rowbarcode','$row1text', '$row1value', '$row2text', '$row2value', '$row3text', '$row3value', '$row4text', '$row4value', ".
    " '$row5text', '$row5value', '$row6text', '$row6value','$row7text', '$row7value', '$row8text', '$row8value' )";
    $sth=db_execute($dbh,$sql);
  }
}

if (!isset($initok)) {echo "do not run this script directly";exit;}

?>
<script type="text/javascript" SRC="js/jquery.fix.clone.js"></script>
<script>
function ldata(rows,cols,lwidth,lheight, vpitch, hpitch, tmargin, bmargin, lmargin, rmargin,name, 
               border,padding,fontsize, headerfontsize,barcodesize, idfontsize,wantbarcode,wantheadertext,wantheaderimage,
               headertext,image,imageheight,imagewidth,papersize,qrtext,wantnotext,wantraligntext)
{
  document.selitemsfrm.lwidth.value=lwidth;
  document.selitemsfrm.lheight.value=lheight;
  document.selitemsfrm.vpitch.value=vpitch;
  document.selitemsfrm.hpitch.value=hpitch;
  document.selitemsfrm.tmargin.value=tmargin;
  document.selitemsfrm.bmargin.value=bmargin;
  document.selitemsfrm.lmargin.value=lmargin;
  document.selitemsfrm.rmargin.value=rmargin;
  document.selitemsfrm.name.value=name;

  document.selitemsfrm.border.value=border;
  document.selitemsfrm.padding.value=padding;
  document.selitemsfrm.headerfontsize.value=headerfontsize;
  document.selitemsfrm.barcodesize.value=barcodesize;
  document.selitemsfrm.idfontsize.value=idfontsize;
  document.selitemsfrm.fontsize.value=fontsize;
  document.selitemsfrm.image.value=image;
  document.selitemsfrm.imagewidth.value=imagewidth;
  document.selitemsfrm.imageheight.value=imageheight;
  document.selitemsfrm.qrtext.value=qrtext;

  $("#wantbarcode").prop("checked", wantbarcode);
  $("#wantheadertext").prop("checked", wantheadertext);
  $("#wantheaderimage").prop("checked", wantheaderimage);
  $("#wantnotext").prop("checked", 1*wantnotext);
  $("#wantraligntext").prop("checked", 1*wantraligntext);

  document.selitemsfrm.headertext.value=headertext;
  document.selitemsfrm.rows.selectedIndex = rows-1;
  document.selitemsfrm.cols.selectedIndex = cols-1;

  $("#pn_"+papersize).attr("selected", "selected");;
  $('#theimage').attr('src',$('#iimage').val());

}

function ldesigndata(designname,rowbarcode,row1text,row1value,row2text,row2value,row3text,row3value,row4text,row4value,
               row5text,row5value,row6text,row6value,row7text,row7value,row8text,row8value)
{
  document.seldesignfrm.designname.value=designname;
  document.seldesignfrm.rowbarcode.value=rowbarcode;
  document.seldesignfrm.row1text.value=row1text;
  document.seldesignfrm.row1value.value=row1value;
  document.seldesignfrm.row2text.value=row2text;
  document.seldesignfrm.row2value.value=row2value;
  document.seldesignfrm.row3text.value=row3text;
  document.seldesignfrm.row3value.value=row3value;
  document.seldesignfrm.row4text.value=row4text;
  document.seldesignfrm.row4value.value=row4value;
  document.seldesignfrm.row5text.value=row5text;
  document.seldesignfrm.row5value.value=row5value;
  document.seldesignfrm.row6text.value=row6text;
  document.seldesignfrm.row6value.value=row6value;
  document.seldesignfrm.row7text.value=row7text;
  document.seldesignfrm.row7value.value=row7value;
  document.seldesignfrm.row8text.value=row8text;
  document.seldesignfrm.row8value.value=row8value;
}

$(document).ready(function() {

    $("#tabs").tabs();
    $("#tabs").show();

    $("#selitems option").clone().appendTo('#selitems2');

    $("#filter").keyup(function () {
        var filter = $(this).val(), count = 0;
	if (filter=='') { //empty filter, re-copy all from selitems2
	  $("#selitems option").remove();
	  $("#selitems2 option").clone().appendTo('#selitems');
	}
	else {

	  $("#selitems option").remove();
	    $("#selitems2 option").each(function () {
		if ($(this).text().search(new RegExp(filter, "i")) < 0) { //not found
		} else {
		    $(this).clone().appendTo('#selitems');
		    count++;
		}
	    });
	}//else
	$("#filter-count").text(count+ ' <?php te("items");?>');
    });




    //submit pdf link as post
    $('#getitemspdf').click(function(e) {
      e.preventDefault();
      if  (!$("#selitems :selected").length) {
        alert('Select items from the list first');
	return;
      }
      //Important:
      //Usage of js/jquery.fix.clone.js to correctly copy/clone select
      $("#selitemsfrm").attr("action", "php/printitemlabels_pdf.php");
      $("#seldesignfrm").attr("action", "php/printitemlabels_pdf.php");
      $('#selitemsfrm :input[isacopy]').remove();
      $('#seldesignfrm :input').clone().hide().attr('isacopy','y').appendTo('#selitemsfrm');
      $('#selitemsfrm').submit();
    });


    $('#savepreset').click(function(e) {
      $("#selitemsfrm").attr("action", "?action=printlabels");
      $("#frmlabelaction").val("savepreset");
      $('#selitemsfrm').submit();
    });

    $('#savedesign').click(function(e) {
      $("#seldesignfrm").attr("action", "?action=printlabels");
      $("#frmdesignaction").val("savedesign");
      $('#seldesignfrm').submit();
    });

    $('#iimage').keyup(function() {
      $('#theimage').attr('src',$('#iimage').val());
    });


    $( "#tabs" ).tabs();


});

</script>


<!-- secondary select for filtering -->
<select id='selitems2' name='selitems2[]' multiple size='1' style='display:none'> </select>
<?php
if (isset($_GET['delpaperid'])) {
  $sql="DELETE from labelpapers where id=".$_GET['delpaperid'];
  $sth=db_exec($dbh,$sql);
  echo "<script>document.location='$scriptname?action=$action'</script>\n";
  echo "<a href='$scriptdir?action=$action'>Go here</a>\n</body></html>"; 
  exit;
}

if (isset($_GET['deldesignid'])) {
  $sql="DELETE from labeldesigns where id=".$_GET['deldesignid'];
  $sth=db_exec($dbh,$sql);
  echo "<script>document.location='$scriptname?action=$action'</script>\n";
  echo "<a href='$scriptdir?action=$action'>Go here</a>\n</body></html>";
  exit;
}

//damn checkboxes dont' post their name when "off" :
if (!isset($wantbarcode)) $wantbarcode=0;
if (!isset($wantheadertext)) $wantheadertext=0;
if (!isset($wantheaderimage)) $wantheaderimage=0;

if (isset($_POST['name']))  {
  foreach($_POST as $k => $v) { 
    ${$k} = $v; 
    if (strstr($k,"want") && $v=="on")  //checkboxes are "on" when checked, we want "1"
      $$k=1;
  }
}

$sql="SELECT * from itemtypes";
$sth=db_execute($dbh,$sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $itypes[$r['id']]=$r;


$sql="SELECT id,title,type FROM agents";
$sth=db_execute($dbh,$sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $agents[$r['id']]=$r;



$sql="SELECT * from labelpapers";
$sth=$dbh->query($sql);
$alllabels=$sth->fetchAll(PDO::FETCH_ASSOC);
for ($i=0;$i<count($alllabels);$i++) {
  $labelpapers[$alllabels[$i]['id']]=$alllabels[$i];
}

if (!isset($_POST['name'])) {
  foreach(array_keys($alllabels[0]) as $key) {
    $$key=$alllabels[0][$key];
  }
}


if (isset($_GET['orderby'])) 
  $orderby=$_GET['orderby'];
else 
  $orderby='status';

?>

<h1><?php te("Print Labels");?></h1>
<div id='labelcontainer'>

<form method=post id='selitemsfrm' name='selitemsfrm'>
  <div class='labellist' style='float:left;'>
    <div id='tabs'>
	<ul>
		<li><a href="#tabs-1"><?php te("Items");?></a></li>
	</ul>

      <div id="tabs-1">
	<div style='float:left;text-align:left'>
	  <b><?php te("Order By");?>:
	  <a title='<?php te("order: status, item type, manufacturer,id");?>' href='<?php  echo "$fscriptname?action=$action"?>'><?php te("[type]");?></a>
	  <a title='<?php te("order: status, id, item type, manufacturer");?>' href='<?php  echo "$fscriptname?action=$action&amp;orderby=items.id"?>'><?php te("[id]");?></a>
	  <a title='<?php te("order: status, id descending, item type, manufacturer");?>' href='<?php  echo "$fscriptname?action=$action&amp;orderby=items.id+desc"?>'><?php te("[id desc]");?></a>
	  <a title='<?php te("order: status, model, item type, manufacturer");?>' href='<?php  echo "$fscriptname?action=$action&amp;orderby=model"?>'><?php te("[model]");?></a>
	  </b>
	</div>

	<div style='float:right;text-align:left'>
	<b><?php te("Filter");?></b>:<input title='<?php te("enter text to filter listed items");?>' id="filter" name="filter" size="20">
	<span id='filter-count'></span> 
	</div>
      <br>

      <div id='selcontainer'>

      <?php

      $sth=db_execute($dbh,"SELECT count(id) as count from items");
      $r=$sth->fetch(PDO::FETCH_ASSOC) ;
      $sth->closeCursor();
      $nitems=$r['count'];

      echo "<select id='selitems' class='monospaced' name='selitems[]' multiple=multiple size='$nitems'>\n";

      $sql="SELECT items.id,manufacturerid,model,status,sn,sn3,itemtypeid,label ".
	   " FROM items,itemtypes ".
	   " WHERE items.itemtypeid=itemtypes.id ".
	   " ORDER BY status,$orderby,itemtypes.typedesc, manufacturerid,items.id, sn, sn2, sn3";
      $sth=db_execute($dbh,$sql);

      $pstatus=0;
      while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
	  $idesc=$itypes[$r['itemtypeid']]['typedesc'];
	  $idesc=sprintf("%-20s",$idesc);
	  $idesc=str_replace(" ","&nbsp;",$idesc);
	  $id=sprintf("%04d",$r['id']);

	  if (((int)$r['status']==2)&& ($pstatus!=2)) {
	    echo "\n<option disabled style='background-color:red;color:black;font-weight:bold;text-align:center'>Defective:</option>";
	    $pstatus=(int)$r['status'];
	  }
	  elseif (((int)$r['status']==1) && ($pstatus!=1)) {
	    echo "\n<option disabled style='background-color:#00BB5F;color:black;font-weight:bold;text-align:center'>Stored:</option>";
	    $pstatus=(int)$r['status'];
	  }
	  elseif (((int)$r['status']==3) && ($pstatus!=3)) {
	    echo "\n<option disabled style='background-color:#cecece;color:black;font-weight:bold;text-align:center'>Obsolete:</option>";
	    $pstatus=(int)$r['status'];
	  }
	  $sn=strlen($r['sn'])>0?$r['sn']:$r['sn3'];
	  if (isset ($_POST['selitems']) && (in_array($id, $_POST['selitems']))) $s="selected";
	  else $s="";

	  if (strlen($r['label']))$label="-".$r['label'];else $label="";

	  echo "<option class='monospaced' $s value='{$r['id']}'>".
	       "$id-$idesc|$status {$agents[$r['manufacturerid']]['title']}-{$r['model']}-$sn$label</option>\n";


      }

	$sth->closeCursor();

      ?>

      </select>
      </div><!--selcontainer-->


      <br><input class='prepbtn' id='getitemspdf' type=submit value='Make Item Labels'>
      <input type='hidden' name='labelaction' id='frmlabelaction' value=''>
      <br>
      <ol style='text-align:left'>
      <li><?php te("Select items from the list above");?></li>
      <li><?php te("Select Label properties (manual or preset)");?></li>
      <li><?php te("Click 'Make Item Labels'");?></li>
      <li><?php te("Download &amp; print the resulting PDF");?></li>
      </ol>

      <?php
	echo t("<br>In the PDF printing dialog,");?>
      <ul><li><?php te("set <b>'Page Scaling'</b> to <b>'None'</b>");?></li>
	  <li><?php te("<b>uncheck</b> 'auto-rotate &amp; center'</b>");?></li>
      </ul>

    </div><!-- tabs-1-->

    <div id="tabs-2">
    </div><!-- tabs-2 -->

  </div><!--tabs-->
</div><!--/labellist-->

<div class='blue' style='float:left;margin-left:10px;'>

<table class='propstable' border=0>
<caption>Label properties:</caption>
<tr><th>Property</th><th>Value</th><th>Presets</th></tr>
<tr><td class='tdt'><label for=name>Preset Name:</label></td><td><input size=8 value='<?php echo $name?>' name=name></td>

<td style='vertical-align:top;' rowspan=19 align=left>
<?php 
//ldata(rows,cols,lwidth,lheight, vpitch, hpitch, tmargin, bmargin, lmargin, rmargin)
if (isset($labelpapers))
foreach ($labelpapers as $lp) {
  //echo $lp['id'];
  echo "\n<a href='javascript:ldata({$lp['rows']}, {$lp['cols']}, ".  
       "{$lp['lwidth']},{$lp['lheight']}, {$lp['vpitch']}, {$lp['hpitch']}, ".  
       "{$lp['tmargin']}, {$lp['bmargin']}, {$lp['lmargin']}, ".
       "{$lp['rmargin']},".
       "\"{$lp['name']}\",".
       "{$lp['border']},".
       "{$lp['padding']},".
       "{$lp['fontsize']},".
       "{$lp['headerfontsize']},".
       "{$lp['barcodesize']},".
       "{$lp['idfontsize']},".
       "{$lp['wantbarcode']},".
       "{$lp['wantheadertext']},".
       "{$lp['wantheaderimage']},".
       "\"{$lp['headertext']}\",".
       "\"{$lp['image']}\",".
       "\"{$lp['imageheight']}\",".
       "\"{$lp['imagewidth']}\",".
       "\"{$lp['papersize']}\",".
       "\"{$lp['qrtext']}\",".
       "\"{$lp['wantnotext']}\",".
       "\"{$lp['wantraligntext']}\"".
       ")'>{$lp['name']}</a>"; 

  echo " <a href='javascript:delconfirm(\"{$lp['id']}\",".
       "\"$scriptname?action=$action&amp;delpaperid={$lp['id']}\");'><img src='images/delete.png'></a><br>\n";
}

//echo "<a href='javascript:ldata(6, 2, 96,42.3, 42.3, 98.5, 21.5, 7.7, 2.7, 7.7)'>Avery 6106</a>"; 
echo "</td></tr>\n";

echo "<tr><td class='tdt'>".t("Paper Size").":</td>\n<td>";

//read paper names
$papernames=file("php/papernames.txt");
echo "<select id='papersize' name=papersize>\n";
foreach ($papernames as $papername) {
  $papername=trim($papername);
  if (isset($_POST['papersize']) && $_POST['papersize']=="$papername") $s=" SELECTED "; 
  else $s="";
  if ($s=="" && $papername=="A4") $s="SELECTED";
  echo "<option $s id='pn_$papername' value='$papername'>$papername</option>\n";
}
echo "\n</select>\n</td></tr>";

echo "<tr><td class='tdt'>".t("Rows").":</td><td>";
echo "<select name=rows>\n";
for ($i=1;$i<40;$i++) {
  if (isset($_POST['rows']) && $_POST['rows']=="$i") $s=" SELECTED "; 
  elseif (!isset($_POST['rows']) && $i=="$rows") $s=" SELECTED "; 
  else $s="";
  echo "\n<option $s value=$i>$i</option>";
}
echo "</select>\n</td></tr>";

echo "<tr><td class='tdt'>".t('Columns').":</td><td>";
echo "<select name=cols>\n";
for ($i=1;$i<10;$i++) {
  if (isset($_POST['cols']) && $_POST['cols']=="$i") $s=" SELECTED "; 
  elseif (!isset($_POST['cols']) && $i=="$cols") $s=" SELECTED "; 
  else $s="";
  echo "\n<option $s value=$i>$i</option>";
}
echo "</select>\n</td></tr>\n";

?>
<tr><td class='tdt'><label for=lwidth><?php te("Width");?>:</label></td><td><input size=4 value='<?php echo $lwidth?>' name=lwidth>mm</td></tr>
<tr><td class='tdt'><label for=lheight><?php te("Height");?>:</label></td><td><input size=4 value='<?php echo $lheight?>' name=lheight>mm</td></tr>
<tr><td class='tdt'><label for=vpitch><?php te("Vert. Pitch");?>:</label></td><td><input size=4 value='<?php echo $vpitch?>' name=vpitch>mm</td></tr>
<tr><td class='tdt'><label for=hpitch><?php te("Horz. Pitch");?>:</label></td><td><input size=4 value='<?php echo $hpitch?>' name=hpitch>mm</td></tr>
<tr><td class='tdt'><label for=tmargin><?php te("Top Margin");?>:</label></td><td><input size=4 value='<?php echo $tmargin?>' name=tmargin>mm</td></tr>
<tr><td class='tdt'><label for=bmargin><?php te("Bottom Margin");?>:</label></td><td><input size=4 value='<?php echo $bmargin?>' name=bmargin>mm</td></tr>
<tr><td class='tdt'><label for=lmargin><?php te("Left Margin");?>:</label></td><td><input size=4 value='<?php echo $lmargin?>' name=lmargin>mm</td></tr>
<tr><td class='tdt'><label for=rmargin><?php te("Right Margin");?>:</label></td><td><input size=4 value='<?php echo $rmargin?>' name=rmargin>mm</td></tr>


<tr><td class=tdt><label for=border><?php te("Border Color (0-255)");?>:</label></td><td  title='0:black, 255:white' ><input size=4 value='<?php echo $border?>' name=border></td></tr>
<tr><td class='tdt'><label for=padding><?php te("Text Padding");?>:</label></td><td><input size=4 value='<?php echo $padding?>' name=padding>mm</td></tr>

<tr><td class='tdt'><label for=fontsize><?php te("FontSize");?>:</label></td><td><input size=4 value='<?php echo $fontsize?>' name=fontsize>pt <small>(1pt=0.352<span style='text-decoration:overline'>7</span> mm)</small></td></tr>
<tr><td class='tdt'><label for=idfontsize><?php te("ID FontSize");?>:</label></td><td><input size=4 value='<?php echo $idfontsize?>' name='idfontsize'>pt</td></tr>


<tr><td class='tdt'><label for=headerfontsize><?php te("Header FontSize");?>:</label></td><td><input size=4 value='<?php echo $headerfontsize?>' name='headerfontsize'>mm</td>
<tr><td class='tdt'><label for=barcodesize><?php te("Barcode Size");?>:</label></td><td><input size=4 value='<?php echo $barcodesize?>' name='barcodesize'>mm</td>

<tr><td class='tdt'><label for=image><?php te("Header Image");?>:</label></td><td><input size=9 id='iimage' style='width:12em' value='<?php echo $image?>' name='image'>
   <img id='theimage' width=25 height=25 src='<?php echo $image; ?>'>
   </td>
<tr><td class='tdt'><label for=imagewidth><?php te("Image Size (WxH)");?>:</label></td><td>
    <input size=2 value='<?php echo $imagewidth?>' name='imagewidth'> X <input size=2 value='<?php echo $imageheight?>' name='imageheight'>mm</td>

<td style='text-align:center' rowspan=8 valign=top>
<input id='savepreset' value='Save as new Preset' name='savepreset' type=submit><br><br>
<img width=180 src='images/labelinfo.jpg'></td></tr>

<tr><td class='tdt'><label for=headertext><?php te("Header");?><br><small>_NL_ = newline</small>:</label></td><td><textarea wrap=soft rows=2 name='headertext' cols=20><?php echo $headertext?></textarea></td></tr>


<tr><td class='tdt'><label for=wantbarcode><?php te("QR Barcode");?>:</label></td>
     <td><input id='wantbarcode' type=checkbox <?php if($wantbarcode) echo "CHECKED"; ?> name=wantbarcode>
	 <input title='<?php te("Text to prepend in QR barcode ID. <br>e.g. http://www.example.com/itdb/ ?action=edititem&id=")?>' size=8 style='width:140px' value='<?php echo $qrtext?>' name=qrtext></td>
	</td></tr>
<tr><td class='tdt'><label for=wantheadertext><?php te("Header Text");?>:</label></td><td><input id='wantheadertext' type=checkbox <?php if($wantheadertext) echo "CHECKED"; ?> name=wantheadertext></td></tr>
<tr><td class='tdt'><label for=wantheaderimage><?php te("Header Image");?>:</label></td><td><input id='wantheaderimage' type=checkbox <?php if($wantheaderimage) echo "CHECKED"; ?> name=wantheaderimage></td></tr>

<tr><td class='tdt'><label for=wantnotext><?php te("No Text");?>:</label></td><td><input title='<?php te("Just print the barcode, no text")?>' id='wantnotext' type=checkbox <?php if($wantnotext) echo "CHECKED"; ?> name=wantnotext></td></tr>
<tr><td class='tdt'><label for=wantraligntext><?php te("Text to the right of barcode");?>:</label></td><td><input id='wantraligntext' type=checkbox <?php if($wantraligntext) echo "CHECKED"; ?> name=wantraligntext></td></tr>


<tr><td class='tdt'><label for=labelskip><?php te("Skip");?>:</label></td><td title='<?php te("use when the top labels have already been printed");?>' ><input size=4 value='<?php echo $labelskip?>' name=labelskip> <?php te("labels");?></td></tr>
</table>

</div>
</form>

</div><!-- container -->

<?php

$sql="SELECT * from labeldesigns";
$sth=$dbh->query($sql);
$alldesigns=$sth->fetchAll(PDO::FETCH_ASSOC);
for ($i=0;$i<count($alldesigns);$i++) {
  $labeldesigns[$alldesigns[$i]['id']]=$alldesigns[$i];
}

if (!isset($_POST['designname'])) {
  foreach(array_keys($alldesigns[0]) as $key) {
    $$key=$alldesigns[0][$key];
  }
}

?>
<form method=post id='seldesignfrm' name='seldesignfrm'>
<div class=blue style='float:left;margin-left:10px;'>
<table class='propstable' border=0>
<caption>Label design:</caption>
<th title="Change the design of your item label here. You can add free text and values based on item data to your label.">Property</th>
<th title="Change the design of your item label here. You can add free text and values based on item data to your label.">Value</th>
<tr>
    <td class='tdt'><design for=designname>Design Name:</design></td><td><input size=8 value='<?php echo $designname?>' name=designname></td></tr>
</tr>
<tr>
    <td class='tdt' title="Select above which row the QR Barcode will be placed."><design for=rowbarcode><?php te("QR Barcode");?>:</design></td>
    <td><select name='rowbarcode'>
      <option value='1' <?php if($rowbarcode==1) echo "SELECTED"; ?>><?php echo te('Row 1') ?></option>
      <option value='2' <?php if($rowbarcode==2) echo "SELECTED"; ?>><?php echo te('Row 2') ?></option>
      <option value='3' <?php if($rowbarcode==3) echo "SELECTED"; ?>><?php echo te('Row 3') ?></option>
      <option value='4' <?php if($rowbarcode==4) echo "SELECTED"; ?>><?php echo te('Row 4') ?></option>
      <option value='5' <?php if($rowbarcode==5) echo "SELECTED"; ?>><?php echo te('Row 5') ?></option>
      <option value='6' <?php if($rowbarcode==6) echo "SELECTED"; ?>><?php echo te('Row 6') ?></option>
      <option value='7' <?php if($rowbarcode==7) echo "SELECTED"; ?>><?php echo te('Row 7') ?></option>
      <option value='8' <?php if($rowbarcode==8) echo "SELECTED"; ?>><?php echo te('Row 8') ?></option>
      <option value='9' <?php if($rowbarcode==9) echo "SELECTED"; ?>><?php echo te('Below Row 8') ?></option>
    </select>
    </td>
</tr>
<?php

// Go thru all rows for label design
for ($i=1;$i<9;$i++)
{
    //For each row go thru all dropdown options and find out which is selected
    for ($j=0;$j<12;$j++)
    {
        if (${'row'.$i.'value'} == $j) ${'r'.$i.'_s'.$j}="SELECTED";
        else ${'r'.$i.'_s'.$j}='';
    }

    //Output variables for select dropdowns
    ${'design_options_'.$i}="<option value=''>".t(Select)."</option>".
      "<option value='0' ".${'r'.$i.'_s0'}.">".t(ID)."</option>".
      "<option value='1' ".${'r'.$i.'_s1'}.">".t('Header Text')."</option>".
      "<option value='2' ".${'r'.$i.'_s2'}.">".t(Label)."</option>".
      "<option value='3' ".${'r'.$i.'_s3'}.">".t(Serial)."</option>".
      "<option value='4' ".${'r'.$i.'_s4'}.">".t('Serial 2')."</option>".
      "<option value='5' ".${'r'.$i.'_s5'}.">".t('Service Tag')."</option>".
      "<option value='6' ".${'r'.$i.'_s6'}.">".t(Manufacturer)." ".t(Model)."</option>".
      "<option value='7' ".${'r'.$i.'_s7'}.">".t(Manufacturer)."</option>".
      "<option value='8' ".${'r'.$i.'_s8'}.">".t(Model)."</option>".
      "<option value='9' ".${'r'.$i.'_s9'}.">".t('DNS Name')."</option>".
      "<option value='10' ".${'r'.$i.'_s10'}.">".t(IPv4)."</option>";
      "<option value='11' ".${'r'.$i.'_s11'}.">".t(IPv6)."</option>";
}
?>
<tr>
    <td class='tdt'><design for=row1><?php te("Row 1");?>:</design></td>
    <td><input size=8 value='<?php echo $row1text?>' name=row1text>
    <select name='row1value'>
    <?php echo $design_options_1; ?>
    </select>
    </td>
</tr>
<tr>
    <td class='tdt'><design for=row2><?php te("Row 2");?>:</design></td>
    <td><input size=8 value='<?php echo $row2text?>' name=row2text>
    <select name='row2value'>
    <?php echo $design_options_2; ?>
    </select>
    </td>
</tr>
<tr>
    <td class='tdt'><design for=row3><?php te("Row 3");?>:</design></td>
    <td><input size=8 value='<?php echo $row3text?>' name=row3text>
    <select name='row3value'>
    <?php echo $design_options_3; ?>
    </select>
    </td>
</tr>
<tr>
    <td class='tdt'><design for=row4><?php te("Row 4");?>:</design></td>
    <td><input size=8 value='<?php echo $row4text?>' name=row4text>
    <select name='row4value'>
    <?php echo $design_options_4; ?>
    </select>
    </td>
</tr>
<tr>
    <td class='tdt'><design for=row5><?php te("Row 5");?>:</design></td>
    <td><input size=8 value='<?php echo $row5text?>' name=row5text>
    <select name='row5value'>
    <?php echo $design_options_5; ?>
    </select>
    </td>
</tr>
<tr>
    <td class='tdt'><design for=row6><?php te("Row 6");?>:</design></td>
    <td><input size=8 value='<?php echo $row6text?>' name=row6text>
    <select name='row6value'>
    <?php echo $design_options_6; ?>
    </select>
    </td>
</tr>
<tr>
    <td class='tdt'><design for=row7><?php te("Row 7");?>:</design></td>
    <td><input size=8 value='<?php echo $row7text?>' name=row7text>
    <select name='row7value'>
    <?php echo $design_options_7; ?>
    </select>
    </td>
</tr>
<tr>
    <td class='tdt'><design for=row8><?php te("Row 8");?>:</design></td>
    <td><input size=8 value='<?php echo $row8text?>' name=row8text>
    <select name='row8value'>
    <?php echo $design_options_8; ?>
    </select>
    </td>
</tr>
<tr>
    <td class='tdc' colspan=2><br><input id='savedesign' value='Save as new Design' name='savedesign' type=submit><br><br><br></td>
    <input type='hidden' name='designaction' id='frmdesignaction' value=''>
</tr>
<tr>
    <th colspan=2>Designs</th>
</tr>
    <tr><td style='vertical-align:top;' colspan=2 align=left>
<?php
//ldata(rows,cols,lwidth,lheight, vpitch, hpitch, tmargin, bmargin, lmargin, rmargin)
if (isset($labeldesigns))
foreach ($labeldesigns as $ld) {
  //echo $lp['id'];
  echo "\n<a href='javascript:ldesigndata(\"{$ld['designname']}\", ".
       "{$ld['rowbarcode']}, ".
       "\"{$ld['row1text']}\", \"{$ld['row1value']}\", ".
       "\"{$ld['row2text']}\", \"{$ld['row2value']}\", ".
       "\"{$ld['row3text']}\", \"{$ld['row3value']}\", ".
       "\"{$ld['row4text']}\", \"{$ld['row4value']}\", ".
       "\"{$ld['row5text']}\", \"{$ld['row5value']}\", ".
       "\"{$ld['row6text']}\", \"{$ld['row6value']}\", ".
       "\"{$ld['row7text']}\", \"{$ld['row7value']}\", ".
       "\"{$ld['row8text']}\", \"{$ld['row8value']}\"".
       ")'>{$ld['designname']}</a>";

  echo " <a href='javascript:delconfirm(\"{$ld['id']}\",".
       "\"$scriptname?action=$action&amp;deldesignid={$ld['id']}\");'><img src='images/delete.png'></a><br>\n";
}
?>
<br></td></tr>
<tr><th colspan=2>Example</th></tr>
<tr><td class='tdc' colspan=2><img width=180 src='images/labelinfo.jpg'></td></tr>
</table>
</div>
</form>

</body>
</html>
