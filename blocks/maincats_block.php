<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
begin_block(T_("BROWSE_TORRENTS"));
	$catsquery = SQL_Query_exec("SELECT distinct parent_cat FROM categories ORDER BY parent_cat"); ?>

	<div class="list-group">
		<a href="torrents.php" class="list-group-item"><i class="fa fa-folder-open"></i> <?php echo T_("SHOW_ALL"); ?></a>

	<?php while($catsrow = mysql_fetch_assoc($catsquery)){ ?>

		<a href="torrents.php?parent_cat=<?php echo urlencode($catsrow["parent_cat"]); ?>" class="list-group-item"><i class="fa fa-folder-open"></i> <?php echo $catsrow["parent_cat"]; ?></a>

	<?php } ?>

	</div>
<?php
end_block();
}
?>
