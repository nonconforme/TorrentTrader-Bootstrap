<?php
begin_block(T_("NAVIGATION")); ?>

<div class="list-group">
	<a href='index.php' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("HOME"); ?></a>
<?php
if ($CURUSER["view_torrents"]=="yes" || !$site_config["MEMBERSONLY"])
{ ?>
	<a href='torrents.php' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("BROWSE_TORRENTS"); ?></a>
	<a href='torrents-today.php' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("TODAYS_TORRENTS"); ?></a>
	<a href='torrents-search.php' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("SEARCH"); ?></a>
	<a href='torrents-needseed.php' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("TORRENT_NEED_SEED"); ?></a>
<?php }
if ($CURUSER["edit_torrents"]=="yes")
{ ?>
	<a href='torrents-import.php' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("MASS_TORRENT_IMPORT"); ?></a>
<?php }
if ($CURUSER && $CURUSER["view_users"]=="yes")
{ ?>
	<a href='teams-view.php' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("TEAMS"); ?></a>
	<a href='memberlist.php' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("MEMBERS"); ?></a>
<?php } ?>
	<a href='rules.php' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("SITE_RULES"); ?></a>
	<a href='faq.php' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("FAQ"); ?></a>
<?php if ($CURUSER && $CURUSER["view_users"]=="yes")
{ ?>
	<a href='staff.php' class="list-group-item"><i class="fa fa-chevron-right"></i> <?php echo T_("STAFF"); ?></a>
<?php } ?>
</div>
<?php
end_block();
?>
