<?php
begin_block(T_("SEARCH"));
?>
	<form method="get" action="torrents-search.php" class="form-inline">
		<div class="input-group">
			<input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
			<span class="input-group-btn">
				<button type="submit" class="btn btn-primary"/><?php echo T_("SEARCH"); ?></button>
			</span>
		</div>
	</form>
	<?php
end_block();
?>
