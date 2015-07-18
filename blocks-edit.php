<?php                     
//
//  TorrentTrader v2.x
//      $LastChangedDate: 2012-09-18 13:17:03 +0100 (Tue, 18 Sep 2012) $
//      $LastChangedBy: nikkbu $
//
//      Author: elegor
//      http://www.torrenttrader.org
//
//

require_once("backend/functions.php");
dbconn();
loggedinonly();

if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
    show_error_msg(T_("ERROR"), T_("_ACCESS_DEN_"), 1);
}
                         
if ($_GET["preview"]) {
	$site_config["LEFTNAV"] = $site_config["RIGHTNAV"] = $site_config["MIDDLENAV"] = false;
}

stdhead(T_("_BLC_MAN_"));

if($_GET["preview"]){
	$name = cleanstr($_GET["name"]);
	if (!file_exists("blocks/{$name}_block.php"))
		show_error_msg(T_("ERROR"), "Possible XSS attempt.", 1);

	echo "<a name=\"".$name."\"></a>";
	begin_frame(T_("_BLC_PREVIEW_"));
	
		echo "<br /><center><b>".T_("_BLC_USE_SITE_SET_")."</b></center><hr />";
		echo "<table border=\"0\" width=\"180\" align=\"center\"><tr><td>";
		include("blocks/".$name."_block.php");
		echo "</td></tr></table><hr />";
		echo "<center><a href=\"javascript: self.close();\">".T_("_CLS_WIN_")."</a></center>";
		
	end_frame();
	stdfoot();
	die();
}

begin_frame(T_("_BLC_MAN_"));

// == addnew

if(@count($_POST["addnew"])){
	foreach($_POST["addnew"] as $addthis){
		$i = $addthis;
		
		$addblock = $_POST["addblock_".$i];
		$wantedname = sqlesc($_POST["wantedname_".$i]);
		$name = sqlesc(str_replace("_block.php","",cleanstr($addblock)));
		$description = sqlesc($_POST["wanteddescription_".$i]);

		SQL_Query_exec("INSERT INTO blocks (named, name, description, position, enabled, sort) VALUES ($wantedname, $name, $description, 'left', 0, 0)")  or ((mysql_errno() == 1062) ? show_error_msg(T_("ERROR"),"Sorry, this block is in database already!",1) : show_error_msg(T_("ERROR"),"Database Query failed: " . mysql_error()));
		if(mysql_affected_rows() != 0){
			$success = "<center><font size=\"3\"><b>".T_("_SUCCESS_ADD_")."</b></font></center><br />";
		}else{
			$success = "<center><font size=\"3\"><b>".T_("_FAIL_ADD_")."</b></font></center><br />";
		}
	}
	echo $success;
}// end addnew

// == permanent delete
if(@count($_POST["deletepermanent"])){
	foreach($_POST["deletepermanent"] as $delpthis){
		unlink("blocks/".$delpthis);
		if(file_exists("blocks/".$delpthis))
			$delmessage="<center><font size=\"3\"><b>".T_("_FAIL_DEL_")."</b></font></center><br />";
		else
			$delmessage="<center><font size=\"3\"><b>".T_("_SUCCESS_DEL_")."</b></font></center><br />";
	}
	echo $delmessage;
}// end addnew

$nextleft=(mysql_num_rows(SQL_Query_exec("SELECT position FROM blocks WHERE position='left' AND enabled=1"))+1);
$nextmiddle=(mysql_num_rows(SQL_Query_exec("SELECT position FROM blocks WHERE position='middle' AND enabled=1"))+1);
$nextright=(mysql_num_rows(SQL_Query_exec("SELECT position FROM blocks WHERE position='right' AND enabled=1"))+1);

// upload block
if($_POST["upload"] == "true"){    
	$uplfailmessage = "";
	$uplsuccessmessage = "";
	if ($_FILES['blockupl']) {

		$blockfile = $_FILES['blockupl'];

		if ($blockfile["name"] == ""){
			$uplfailmessage .= '<div class="alert alert-danger" role="alert"><p class="text-center"><strong>'.T_("_SEND_NOTHING_");
		}
		if (($blockfile["size"] == 0) && ($blockfile["name"] != "")){ 
			$uplfailmessage .= '<div class="alert alert-danger" role="alert"><p class="text-center"><strong>'.T_("_SEND_EMPTY_");
		}
		if ((!preg_match('/^(.+)\.php$/si', $blockfile['name'], $fmatches)) && ($blockfile["name"] != "")){
			$uplfailmessage .= ' '.T_("_SEND_INVALID_");
		}
		if ((!preg_match('/^(.+)\_block.php$/si', $blockfile['name'], $fmatches)) && ($blockfile["name"] != "")){
			$uplfailmessage .= ' '.T_("_SEND_NO_BLOCK_").'</strong></p></div>';
		}

		$blockfilename = $blockfile['tmp_name'];
		if (@!is_uploaded_file($blockfilename)){
			$uplfailmessage .= " ".T_("_FAIL_UPL_").'</strong></p></div>';
		}
		
	}

	if(!$uplfailmessage){
		$blockfilename = $site_config['blocks_dir'] . "/" . $blockfile['name'];
		if($_POST["uploadonly"]){
			if(file_exists($blockfilename)){
				$uplfailmessage .='<div class="alert alert-warning" role="alert"><p class="text-center"><strong>'.T_("_BLC_EXIST_").'</strong> "'.$blockfile['name'].'"</strong></p></div>';
			}else{
				if(@!move_uploaded_file($blockfile["tmp_name"], $blockfilename)){
					$uplfailmessage .= "<center><font size=\"3\"><b>".T_("_CANNOT_MOVE_")." </b> \"".$blockfile['name']."\" <b>".T_("_TO_DEST_DIR_")."</b></font></center><br />".T_("_CONFIG_DEST_DIR_").": <b>\"".$site_config['blocks_dir']. "\"</b><br />".T_("_PLS_CHECK_")." <b>config.php</b> ".T_("_SURE_FULL_PATH_").". ".T_("_YOUR_CASE_").": <b>\"".$_SERVER['DOCUMENT_ROOT']."\"</b> + <b>\"/".T_("_SUB_DIR_")."\"</b> (".T_("_IF_ANY_").") ".T_("_AND_")." + <b>\"/blocks\"</b>.";
				}else{
					$uplsuccessmessage .= '<div class="alert alert-success" role="alert"><p class="text-center"><strong>'.T_("_SUCCESS_UPL_").'</strong></p></div>';
				}
			}
		}else{
			if(file_exists($blockfilename)){
				$uplfailmessage .= '<div class="alert alert-warning" role="alert"><p class="text-center"><strong>'.T_("_BLC_EXIST_").'</strong> "'.$blockfile['name'].'"</p></div>';
			}else{
				if(@!move_uploaded_file($blockfile["tmp_name"], $blockfilename)){
					$uplfailmessage .= "<center><font size=\"3\"><b>".T_("_CANNOT_MOVE_")." </b> \"".$blockfile['name']."\" <b>".T_("_TO_DEST_DIR_")."</b></font></center><br />".T_("_CONFIG_DEST_DIR_").": <b>\"".$site_config['blocks_dir']. "\"</b><br />".T_("_PLS_CHECK_")." <b>config.php</b> ".T_("_SURE_FULL_PATH_").". ".T_("_YOUR_CASE_").": <b>\"".$_SERVER['DOCUMENT_ROOT']."\"</b> + <b>\"/".T_("_SUB_DIR_")."\"</b> (".T_("_IF_ANY_").") ".T_("_AND_")." + <b>\"/blocks\"</b>.";
				}else{
					$named = ($_POST["wantedname"] ? $_POST["wantedname"] : str_replace("_block.php","",$blockfile['name']));
					$name  = str_replace("_block.php","",$blockfile['name']);
					$description = $_POST["description"];
					$position = $_POST["position"];
					$sort = ($_POST["enabledyes"] ? $uplsort : 0);
					$enabled = ($_POST["enabledyes"] ? 1 : 0);
					
                    SQL_Query_exec("INSERT INTO blocks (named, name, description, position, sort, enabled) VALUES (
                    ".sqlesc($named).", ".sqlesc($name).", ".sqlesc($description).", ".sqlesc($position).", ".sqlesc($sort).", ".sqlesc($enabled).")");

					if(mysql_affected_rows() != 0){
						$uplsuccessmessage .= '<div class="alert alert-success" role="alert"><p class="text-center"><strong>'.T_("_SUCCESS_UPL_ADD_").'</strong></p></div>';
					}else{
						$uplfailmessage .= '<div class="alert alert-danger" role="alert"><p class="text-center"><strong>'.T_("_FAIL_UPL_ADD_").'</strong></p></div>';
					}
					echo $uplsuccessmessage;
				}
			}
		}
	}
}// end upload block			

// == edit
if ($_REQUEST["edit"] == "true")
{
    # Prune Block Cache.
	$TTCache->Delete("blocks_left");
	$TTCache->Delete("blocks_middle");
	$TTCache->Delete("blocks_right");
    
	//resort left blocks
	function resortleft(){
		$sortleft = SQL_Query_exec("SELECT sort, id FROM blocks WHERE position='left' AND enabled=1 ORDER BY sort ASC");
		$i=1;
		while($sort = mysql_fetch_assoc($sortleft)){
			SQL_Query_exec("UPDATE blocks SET sort = $i WHERE id=".$sort["id"]);
			$i++;
		}
	}
	//resort middle blocks
	function resortmiddle(){
		$sortmiddle = SQL_Query_exec("SELECT sort, id FROM blocks WHERE position='middle' AND enabled=1 ORDER BY sort ASC");
		$i=1;
		while($sort = mysql_fetch_assoc($sortmiddle)){
			SQL_Query_exec("UPDATE blocks SET sort = $i WHERE id=".$sort["id"]);
			$i++;
		}
	}
	//resort right blocks
	function resortright(){
		$sortright = SQL_Query_exec("SELECT sort, id FROM blocks WHERE position='right' AND enabled=1 ORDER BY sort ASC");
		$i=1;
		while($sort = mysql_fetch_assoc($sortright)){
			SQL_Query_exec("UPDATE blocks SET sort = $i WHERE id=".$sort["id"]);
			$i++;
		}
	}

	// == delete

	if(@count($_POST["delete"])){
		foreach($_POST["delete"] as $delthis){
			SQL_Query_exec("DELETE FROM blocks WHERE id=".sqlesc($delthis));
		}
			resortleft();
			resortmiddle();
			resortright();
	}// == end delete

	// == move to left
	if(is_valid_id($_GET["left"])){
		SQL_Query_exec("UPDATE blocks SET position = 'left', sort = $nextleft WHERE id = " . $_GET["left"]);
		resortmiddle();
		resortright();
	}// end move to left
	
	// == move to center
	if(is_valid_id($_GET["middle"])){
		SQL_Query_exec("UPDATE blocks SET position = 'middle', sort = $nextmiddle WHERE id = " . $_GET["middle"]);
		resortleft();
		resortright();
	}// end move to center
	
	// == move to right
	if(is_valid_id($_GET["right"])){
		SQL_Query_exec("UPDATE blocks SET position = 'right', sort = $nextright WHERE enabled=1 AND id = " . $_GET["right"]);
		resortleft();
		resortmiddle();
	}// end move to right
	
	// == move upper
	if(is_valid_id($_GET["up"])){
		$cur = SQL_Query_exec("SELECT position, sort, id FROM blocks WHERE id = " . $_GET["up"]);
		$curent = mysql_fetch_assoc($cur);

        $sort = ( int ) $_GET["sort"];
        
		SQL_Query_exec("UPDATE blocks SET sort = ".$sort." WHERE sort = ".($sort-1)." AND id != " . $_GET["up"] . " AND position = " . sqlesc($_GET["position"]) . "");
		SQL_Query_exec("UPDATE blocks SET sort = ".($sort-1)." WHERE id = " . $_GET["up"]);
	}// end move to upper
	
	// == move lower
	if(is_valid_id($_GET["down"])){
		$cur = SQL_Query_exec("SELECT position, sort, id FROM blocks WHERE id = " . $_GET["down"]);
		$curent = mysql_fetch_assoc($cur);

        $sort = ( int ) $_GET["sort"];
        
		SQL_Query_exec("UPDATE blocks SET sort = ".($sort+1)." WHERE id = " . $_GET["down"]);
		SQL_Query_exec("UPDATE blocks SET sort = ".$sort." WHERE sort = ".($sort+1)." AND id != " . $_GET["down"] . " AND position = " . sqlesc($_GET["position"]) ."");
	}// end move lower
	
	// == update
	$res=SQL_Query_exec("SELECT * FROM blocks ORDER BY id");

	if(!$_GET["up"] && !$_GET["down"] && !$_GET["right"] && !$_GET["left"] && !$_GET["middle"]){
         
        $update = array();
        
		while($upd = mysql_fetch_assoc($res)){
			$id = $upd["id"];
			$update[] = "enabled = ".$_POST["enable_".$upd["id"]];
			$update[] = "named = '".mysql_real_escape_string($_POST["named_".$upd["id"]])."'";
			$update[] = "description = '".mysql_real_escape_string($_POST["description_".$upd["id"]])."'";
			
			if(($upd["enabled"] == 0) && ($upd["position"] == "left") && ($_POST["enable_".$upd["id"]] == 1))
				$update[] = "sort = ".$nextleft;
			elseif(($upd["enabled"] == 0) && ($upd["position"] == "middle") && ($_POST["enable_".$upd["id"]] == 1))
				$update[] = "sort = ".$nextmiddle;
			elseif(($upd["enabled"] == 0) && ($upd["position"] == "right") && ($_POST["enable_".$upd["id"]] == 1))
				$update[] = "sort = ".$nextright;
			
			elseif(($upd["enabled"] == 1) && ($upd["position"] == "left") && ($_POST["enable_".$upd["id"]] == 0))
				$update[] = "sort = 0";
			elseif(($upd["enabled"] == 1) && ($upd["position"] == "middle") && ($_POST["enable_".$upd["id"]] == 0))
				$update[] = "sort = 0";
			elseif(($upd["enabled"] == 1) && ($upd["position"] == "right") && ($_POST["enable_".$upd["id"]] == 0))
				$update[] = "sort = 0";
			else
				$update[] = "sort = ".$upd["sort"];
				
			SQL_Query_exec("UPDATE blocks SET ". implode(", ", $update). " WHERE id=$id") or show_error_msg(T_("ERROR"), "".T_("_FAIL_DB_QUERY_").": ".mysql_error());
		}
	}
	resortleft();
	resortmiddle();
	resortright();
}// == end edit ?>

<ol class="breadcrumb">
	<li><a href="index.php"><?php echo T_("HOME"); ?></a></li>
	<li><a href="admincp.php"><?php echo T_("ADMIN_CP"); ?></a></li>
	<li><?php echo T_("_BLC_MAN_"); ?></li>
</ol>
<div class="page-header">
	<h1><?php echo T_("_BLC_MAN_"); ?></h1>
</div>
<?php
// ---- <table> for blocks in database -----------------------------------------
$res = SQL_Query_exec("SELECT * FROM blocks ORDER BY enabled DESC, position, sort"); ?>

	<form name="blocks" method="post" action="blocks-edit.php">
	<input type="hidden" name="edit" value="true" />

	<table class="table table-bordered">
		<thead>
			<tr>
				<th><?php echo T_("_NAMED_")." (".T_("_FL_NM_IF_NO_SET_"); ?>)</th>
				<th><?php echo T_("_FILE_NAME_"); ?></th>
				<th><?php echo T_("DESCRIPTION")." (".T_("_MAX_")." 255 ".T_("_CHARS_"); ?>)</th>
				<th><?php echo T_("_POSITION_"); ?></th>
				<th><?php echo T_("_SORT_ORDER_"); ?></th>
				<th><?php echo T_("ENABLED"); ?></th>
				<th><?php echo T_("_DEL_"); ?></th>
				<!--<th><?php echo T_("YES"); ?></th>
				<th><?php echo T_("NO"); ?></th> -->
			</tr>
		</thead>
		<tbody>
<?php
while($blocks2 = mysql_fetch_assoc($res)){
	$down=$blocks["id"];
	if(!$setclass){
		$class="table_col2";$setclass=true;}
	else{
		$class="table_col1";$setclass=false;}
	switch($blocks2["position"]){
		case "left":
			$pos = T_("_LEFT_");
			break;
		case "middle":
			$pos = T_("_MIDDLE_");
			break;
		case "right":
			$pos = T_("_RIGHT_");
			break;
		}?>

			<tr>
				<td rowspan="2">
					<input type="text" name="named_<?php echo $blocks2["id"];?>" class="form-control" value="<?php echo $blocks2["named"] ? $blocks2["named"] : $blocks2["name"];?>" />
				</td>
				<td rowspan="2"><?php echo $blocks2["name"];?></td>
				<td rowspan="2">
					<textarea class="form-control" name="description_<?php echo $blocks2["id"];?>" rows="2"  maxlength="255" ><?php echo $blocks2["description"];?></textarea>
				</td>
				<td><span class="label label-success"><?php echo $pos;?></span></td>
				<td><span class="label label-success"><?php echo $blocks2["sort"];?></span></td>
				<td rowspan="2">
					<label class="radio-inline">
						<input type="radio" name="enable_<?php echo $blocks2["id"];?>"<?php echo ($blocks2["enabled"] ? " checked=\"checked\"" : "");?> value="1" /> <?php echo T_("YES"); ?> 
					</label>
					<label class="radio-inline">
						<input type="radio" name="enable_<?php echo $blocks2["id"];?>"<?php echo (!$blocks2["enabled"] ? " checked=\"checked\"" : "");?> value="0" /> <?php echo T_("NO"); ?> 
					</label>
				</td>
				<td rowspan="2">
					<input type="checkbox" name="delete[]" value="<?php echo $blocks2["id"];?>"/>
				</td>
			</tr>
			<tr>
				<td>
				<?php echo ((($blocks2["position"] != "left") && ($blocks2["enabled"] == 1)) ? "
						<a href=\"blocks-edit.php?edit=true&amp;position=left&amp;left=".$blocks2["id"]."\">
							<i class=\"fa fa-align-left\" alt=\""._MOVE_LEFT_."\"></i>
						</a>
					" : "
						<i class=\"fa fa-times\" ".($blocks2["enabled"] ? "alt=\"".T_("_AT_LEFT_")."\"" : "alt=\""._MUST_ENB_MOVE_."\"")." ".($blocks2["enabled"] ? "onclick=\"javascript: alert('".T_("_AT_LEFT_")."');\"" : "onclick=\"javascript: alert('".T_("_MUST_ENB_FIRST")."');\"")." ></i>");?>

					<?php echo ((($blocks2["position"] != "middle") && ($blocks2["enabled"] == 1)) ? "
						<a href=\"blocks-edit.php?edit=true&amp;position=middle&amp;middle=".$blocks2["id"]."\">
							<i class=\"fa fa-align-center\" alt=\""._MOVE_CENTER_."\" ></i>
						</a>
					" : "
						<i class=\"fa fa-times\" ".($blocks2["enabled"] ? "alt=\"".T_("_AT_CENTER_")."\"" : "alt=\""._MUST_ENB_MOVE_."\"")." ".($blocks2["enabled"] ? "onclick=\"javascript: alert('".T_("_AT_CENTER_")."');\"" : "onclick=\"javascript: alert('".T_("_MUST_ENB_FIRST")."');\"")."  ></i>");?>

					<?php echo ((($blocks2["position"] != "right") && ($blocks2["enabled"] == 1)) ? "
						<a href=\"blocks-edit.php?edit=true&amp;position=right&amp;right=".$blocks2["id"]."\">
							<i class=\"fa fa-align-right\" alt=\""._MOVE_RIGHT_."\" ></i>
						</a>
					" : "
						<i class=\"fa fa-times\" ".($blocks2["enabled"] ? "alt=\"".T_("_AT_RIGHT_")."\"" : "alt=\""._MUST_ENB_MOVE_."\"")." ".($blocks2["enabled"] ? "onclick=\"javascript: alert('".T_("_AT_RIGHT_")."');\"" : "onclick=\"javascript: alert('".T_("_MUST_ENB_FIRST")."');\"")." ></i>");?>
					</td>
					<td>
						<?php echo ((($blocks2["sort"]!= 1) && ($blocks2["enabled"] != 0)) ? "
						<a href=\"blocks-edit.php?edit=true&amp;position=".$blocks2["position"]."&amp;sort=".$blocks2["sort"]."&amp;up=".$blocks2["id"]."\">
							<i class=\"fa fa-chevron-up\" alt=\""._MOVE_UP_."\" ></i>
						</a>
					" : "
						<i class=\"fa fa-times\" alt=\"".($blocks2["enabled"] ? "".T_("_AT_TOP_")."" : ""._MUST_ENB_SORT_."")."\" ".($blocks2["enabled"] ? "onclick=\"javascript: alert('".T_("_AT_TOP_")."');\"" : "onclick=\"javascript: alert('".T_("_MUST_ENB_FIRST")."');\"")." ></i>");?>

					<?php echo (((($blocks2["sort"] != ($nextleft-1)) && ($blocks2["position"] == "left") || ($blocks2["sort"] != ($nextright-1)) && ($blocks2["position"] == "right") || ($blocks2["sort"] != ($nextmiddle-1)) && ($blocks2["position"] == "middle")) && ($blocks2["enabled"] != 0)) ? "
						<a href=\"blocks-edit.php?edit=true&amp;position=".$blocks2["position"]."&amp;sort=".$blocks2["sort"]."&amp;down=".$blocks2["id"]."\">
							<i class=\"fa fa-chevron-down\" alt=\""._MOVE_DOWN_."\" ></i>
						</a>
					" : "
						<i class=\"fa fa-times\" alt=\"".($blocks2["enabled"] ? "".T_("_AT_BOTTOM_")."" : ""._MUST_ENB_SORT_."")."\" ".($blocks2["enabled"] ? "onclick=\"javascript: alert('".T_("_AT_BOTTOM_")."');\"" : "onclick=\"javascript: alert('".T_("_MUST_ENB_FIRST")."');\"")." ></i>");?>
					</td>
			</tr>

<?php
}	
?>
		</tbody>
	</table>
	
	<button type="submit" class="btn btn-primary center-block" /><?php echo T_("_BTN_UPDT_");?></button>

</form>


<?php
$exist=SQL_Query_exec("SELECT name FROM blocks");
while($fileexist = mysql_fetch_assoc($exist)){
	$indb[] = $fileexist["name"]."_block.php";
}

if ($folder = opendir('blocks')) {
    while (false !== ($file = readdir($folder))) {
        if ($file != "." && $file != ".." && !in_array($file, $indb)) {
            if (preg_match("/_block.php/i", $file))
                $infolder[] = $file;
        }
    }
    closedir($folder);
}

if($infolder){
	?>
	<a name="anb"></a>
	<hr />
	<?php echo $success.$delmessage;?>

	<h4 class="text-center"><strong><?php echo T_("_BLC_AVAIL_");?></strong></h4><p class="text-center">(<?php echo T_("_IN_FOLDER_");?>)</p>

	<form name="addnewblock" method="post" action="blocks-edit.php#anb">       

		<table class="table table-bordered">
			<thead>
				<tr>
					<th><?php echo T_("_NAMED_");?> (<?php echo T_("_FL_NM_IF_NO_SET_");?>)</th>
					<th><?php echo T_("FILE");?></th>
					<th><?php echo T_("DESCRIPTION");?> (<?php echo T_("_MAX_")." 255 ".T_("_CHARS_");?>)</th>
					<th><?php echo T_("_ADD_");?></th>
					<th><?php echo T_("_DEL_");?></th>
				</tr>
			</thead>
			<tbody>
	<?php
			/* loop over the blocks directory and take file names witch are not in database. */
			if ($folder = opendir('blocks')) {
				$i=0;
				while (false !== ($file = readdir($folder))) {
					if ($file != "." && $file != ".." && !in_array($file, $indb)) {
						if (preg_match("/_block.php/i", $file)){
							if(!$setclass){
								$class="table_col2";$setclass=true;}
							else{
								$class="table_col1";$setclass=false;}
								?>
							<tr>
								<td>
									<input type="hidden" name="addblock_<?php echo $i;?>" value="<?php echo $file;?>" />
									<input type="text" name="wantedname_<?php echo $i;?>" class="form-control" value="<?php echo str_replace("_block.php","",$file);?>">
								</td>
								<td><?php echo $file;?></td>
								<td>
									<textarea class="form-control" name="wanteddescription_<?php echo $i;?>" maxlength="255" rows='2'></textarea>
								</td>
								<td>
									<div id="addn_<?php echo $i;?>" >
										<input type='checkbox' name='addnew[]' value="<?php echo $i;?>" onclick="javascript: if(dltp_<?php echo $i;?>.style.display=='none'){dltp_<?php echo $i;?>.style.display='block'}else{dltp_<?php echo $i;?>.style.display='none'};" />
									</div>
								</td>
								<td>
									<div id="dltp_<?php echo $i;?>" >
										<input type='checkbox' name='deletepermanent[]' value="<?php echo $file;?>" onclick="javascript: if(addn_<?php echo $i;?>.style.display=='none'){addn_<?php echo $i;?>.style.display='block'}else{addn_<?php echo $i;?>.style.display='none'}" />
									</div>
								</td>
							</tr>
							<?php
							$i++;
						}
					}
				}
			closedir($folder);
			}
			/* end loop over the blocks directory and take names. */
	?>
			</tbody>
		</table>
		<div class="text-center">
			<button type="submit" name="submit" class="btn btn-primary" /><?php echo T_("_BTN_DOIT_");?></button> 
			<button type="reset" class="btn btn-primary" /><?php echo T_("RESET");?></button>
		</div><br />

	</form>

	<div class="alert alert-danger text-center" role="alert"><strong><?php echo T_("_DLT_WIL_PER_")."</strong> ".T_("_NO_ADD_WAR_");?></div>

<?php
}
?>
<a name="upload"></a>
<hr />
<?php
if($_POST["upload"]){
	if($uplfailmessage){
		echo $uplfailmessage;
	}else{
	echo $uplsuccessmessage;
	}
}
?>
<h4 class="text-center"><strong><?php echo T_("_BLC_UPL_");?></strong></h4><br />
<form enctype="multipart/form-data"  action="blocks-edit.php#upload" method="post" class="form-horizontal">
	<input type="hidden" name="upload" value="true" />

	<div class="form-group">
		<label for="wantedname" class="col-sm-2 control-label"><?php echo T_("_NAMED_");?></label>
		<div class="col-sm-4">
			<input type="text" class="form-control" name="wantedname" id="wantedname" />
			<p class="help-block"><?php echo T_("_FL_NM_IF_NO_SET_");?></p>
		</div>
	</div>
	<div class="form-group">
		<label for="description" class="col-sm-2 control-label"><?php echo T_("DESCRIPTION");?></label>
		<div class="col-sm-4">
			<textarea name="description" class="form-control" id="description" rows="2" maxlength="255" ></textarea>
			<p class="help-block"><?php echo T_("_MAX_")." 255 ".T_("_CHARS_");?></p>
		</div>
	</div>

	<div class="form-group">
		<label for="blockupl" class="col-sm-2 control-label"><?php echo T_("FILE");?></label>
		<div class="col-sm-4">
			<input type="file" class="form-control" name="blockupl" id="blockupl" />
		</div>
	</div>
	<br />
<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
		<table class="table table-bordered">
  			<thead>
  				<tr>
    				<th><?php echo T_("_POSITION_");?></th>
    				<th><?php echo T_("_SORT_");?></th>
    				<th><?php echo T_("ENABLED");?></th>
    				<th><?php echo T_("_JUST_UPL_");?></th>
  				</tr>
  			</thead>
  			<tbody>
				<tr>
    				<td>
    					<label class="radio-inline">
    						<input type="radio" name="position" checked="checked" value="left" onclick="javascript: if(enabledyes.checked){uplsort.value = '<?php echo $nextleft;?>';}else{uplsort.value = '0';}" /><?php echo T_("L");?>
    					</label>
    					<label class="radio-inline">
    						<input type="radio" name="position" value="middle" onclick="javascript: if(enabledyes.checked){uplsort.value = '<?php echo $nextmiddle;?>';}else{uplsort.value = '0';}" /><?php echo T_("M");?>
    					</label>
    					<label class="radio-inline">
    						<input type="radio" name="position" value="right" onclick="javascript: if(enabledyes.checked){uplsort.value = '<?php echo $nextright;?>';}else{uplsort.value = '0';}" /><?php echo T_("R");?>
    					</label>
    				</td>
    				<td>
    					<div class="col-xs-4">
    						<input type="text" name="uplsort" class="form-control" value="0" onclick="javascript: alert('<?php echo T_("_CLICK_POS_");?>');" readonly/>
    					</div>
    				</td>
    				<td>
    					<input type="checkbox" name="enabledyes" onclick="javascript: uploadonly.disabled = enabledyes.checked; if(enabledyesnotice.style.display == 'block'){enabledyesnotice.style.display = 'none'}else{enabledyesnotice.style.display = 'block'}; if(!checked){uplsort.value = '0'}" />
    				</td>
    				<td>
    					<input type="checkbox" name="uploadonly" onclick="javascript: wantedname.disabled = enabledyes.disabled = description.disabled = pos.disabled = uploadonly.checked; if(uploadonlynotice.style.display == 'block'){uploadonlynotice.style.display = 'none'}else{uploadonlynotice.style.display = 'block'};"   />
    				</td>
  				</tr> 
  			</tbody>
  		</table>
  	</div>
  	</div>
  	</div>
  	</div>

    	<button type="submit" class="btn btn-primary center-block" /><?php echo T_("UPLOAD");?></button>

    	<div id="uploadonlynotice" style="display: none;">(<?php echo T_("_UPL_ONLY_");?>)</div>
    	<div id="enabledyesnotic" style="display: none;">(<?php echo T_("_UPL_ADD_");?>)</div>

</form>	

	<?php
end_frame();
stdfoot();

?>