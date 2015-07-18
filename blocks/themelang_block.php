<?php
if ($CURUSER){
	begin_block(T_("THEME")." / ".T_("LANGUAGE"));

	$ss_r = SQL_Query_exec("SELECT * from stylesheets");
	$ss_sa = array();

	while ($ss_a = mysql_fetch_assoc($ss_r)){
		$ss_id = $ss_a["id"];
		$ss_name = $ss_a["name"];
		$ss_sa[$ss_name] = $ss_id;
	}

	ksort($ss_sa);
	reset($ss_sa);
    
	while (list($ss_name, $ss_id) = each($ss_sa)){
		if ($ss_id == $CURUSER["stylesheet"]) $ss = " selected='selected'"; else $ss = "";
		$stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>";
	}

	$lang_r = SQL_Query_exec("SELECT * from languages");
	$lang_sa = array();

	while ($lang_a = mysql_fetch_assoc($lang_r)){
		$lang_id = $lang_a["id"];
		$lang_name = $lang_a["name"];
		$lang_sa[$lang_name] = $lang_id;
	}

	ksort($lang_sa);
	reset($lang_sa);

	while (list($lang_name, $lang_id) = each($lang_sa)){
		if ($lang_id == $CURUSER["language"]) $lang = " selected='selected'"; else $lang = "";
		$languages .= "<option value='$lang_id'$lang>$lang_name</option>";
	}

?>
 
 <form method="post" action="take-theme.php" class="form-horizontal">
 	<div class="form-group">
		<label class="col-sm-4 control-label"><?php echo T_("THEME"); ?></label>
		<div class="col-sm-8">
			<select name="stylesheet" class="form-control"><?php echo $stylesheets; ?></select>
  		</div>
  	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?php echo T_("LANGUAGE"); ?></label>
		<div class="col-sm-8">
			<select name="language" class="form-control"><?php echo $languages; ?></select></td>
  		</div>
  	</div>
	<button type="submit" class="btn btn-primary center-block" value="" /><?php echo T_("APPLY"); ?></button>

  </form>  

<?php
end_block();
}
?>