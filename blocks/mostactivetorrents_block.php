<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
	begin_block(T_("MOST_ACTIVE"));

	$where = "WHERE banned = 'no' AND visible = 'yes'";
	//uncomment the following line to exclude external torrents
	//$where = "WHERE external !='yes' AND banned ='no' AND visible = 'yes'"  

	$expires = 600; // Cache time in seconds
	if (($rows = $TTCache->Get("mostactivetorrents_block", $expires)) === false) {
		$res = SQL_Query_exec("SELECT id, name, seeders, leechers FROM torrents $where ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT 10");

		$rows = array();
		while ($row = mysql_fetch_assoc($res))
			$rows[] = $row;

		$TTCache->Set("mostactivetorrents_block", $rows, $expires);
	}

	if ($rows) {
		foreach ($rows as $row) { 
				$char1 = 20; //cut length 
				$smallname = htmlspecialchars(CutName($row["name"], $char1)); ?>

				<div class="pull-left"><a href='torrents-details.php?id=<?php echo $row["id"]; ?>' title='<?php echo htmlspecialchars($row["name"]); ?>'><?php echo $smallname; ?></a></div>
				<div class="pull-right"><span class="label label-success">S: <?php echo number_format($row["seeders"]); ?></span> <span class="label label-warning">L: <?php echo number_format($row["leechers"]); ?></span></div>

		<?php }

} else { ?>

	<p><?php echo T_("NOTHING_FOUND"); ?></p>

<?php }
end_block();
}
?>