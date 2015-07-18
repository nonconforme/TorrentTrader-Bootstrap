<?php
//USERS ONLINE
begin_block(T_("NEWEST_MEMBERS"));

$expire = 600; // time in seconds
if (($rows = $TTCache->Get("newestmember_block", $expire)) === false) {
	$res = SQL_Query_exec("SELECT id, username FROM users WHERE enabled = 'yes' AND status='confirmed' AND privacy != 'strong' ORDER BY id DESC LIMIT 5");
	$rows = array();

	while ($row = mysql_fetch_assoc($res))
		$rows[] = $row;

	$TTCache->Set("newestmember_block", $rows, $expire);
}

if (!$rows) {?>
	<p class="text-center"><?php echo T_("NOTHING_FOUND");?></p>
<?php } else { ?>
		<div class="list-group">
	<?php foreach ($rows as $row) { ?>
			<a href='account-details.php?id=<?php echo $row["id"];?>' class="list-group-item"><?php echo $row["username"];?></a>
	<?php } ?>
		</div>
<?php }

end_block();
?>