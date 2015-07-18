<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
	begin_block(T_("SEEDERS_WANTED"));

	$external = "external = 'no'";
	// Uncomment below to include external torrents
	$external = 1;

	$expires = 600; // Cache time in seconds
	if (($rows = $TTCache->Get("seedwanted_block", $expires)) === false) {
		$res = SQL_Query_exec("SELECT id, name, seeders, leechers FROM torrents WHERE seeders = 0 AND leechers > 0 AND banned = 'no' AND $external ORDER BY leechers DESC LIMIT 5");
		$rows = array();

		while ($row = mysql_fetch_assoc($res)) {
			$rows[] = $row;
		}

		$TTCache->Set("seedwanted_block", $rows, $expires);
	}


	if (!$rows) { ?>
		<p class="text-center"><?php echo T_("NOTHING_FOUND");?></p>
	<?php } else {
		foreach ($rows as $row) { 
			$char1 = 20; //cut length 
			$smallname = htmlspecialchars(CutName($row["name"], $char1)); ?>

			<div class="pull-left"><a href="torrents-details.php?id=<?php echo $row["id"]; ?>" title="<?php echo htmlspecialchars($row["name"]); ?>"><?php echo $smallname; ?></a></div>
			<div class="pull-right"><span class="label label-waring"><?php echo T_("LEECHERS"); ?>: <?php echo number_format($row["leechers"]); ?></span></div>
		<?php }
	}
	end_block();
}
?>