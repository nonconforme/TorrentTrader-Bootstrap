<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
	$limit = 25; // Only show 25 max

	$res = SQL_Query_exec("SELECT torrents.id, torrents.name, torrents.image1, torrents.image2, categories.name as cat_name, categories.parent_cat as cat_parent FROM torrents LEFT JOIN categories ON torrents.category=categories.id WHERE banned = 'no' AND (image1 != '' OR image2 != '') AND visible = 'yes' ORDER BY id DESC LIMIT $limit");
	if (mysql_num_rows($res)) {
		begin_block(T_("LATEST_POSTERS"));

		while ($row = mysql_fetch_assoc($res)) {
				$cat = htmlspecialchars("$row[cat_parent] - $row[cat_name]");
				$name = htmlspecialchars($row["name"]);

				if ($row["image1"]) { ?>
					<div class="col-lg-6"><a href="torrents-details.php?id=<?php echo $row["id"];?>" title="<?php echo $name ." / ". $cat;?>"><img src="uploads/images/<?php echo $row["image1"];?>" alt="<?php echo $name ." / ". $cat;?>" class="img-thumbnail" /></a></div>
				<?php } else { ?>
					<div class="col-lg-6"><a href="torrents-details.php?id=<?php echo $row["id"];?>" title="<?php echo $name ." / ". $cat;?>"><img src="uploads/images/<?php echo $row["image2"];?>" alt="<?php echo $name ." / ". $cat;?>" class="img-thumbnail" /></a></div>
				<?php }
		}

		end_block();
	}
}
?>
