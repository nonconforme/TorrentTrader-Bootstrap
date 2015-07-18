<?php
if (!$CURUSER) {
	begin_block(T_("LOGIN"));
?>
<form method="post" action="account-login.php">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr><td>
		<table border="0" cellpadding="1" align="center">
			<tr>
			<td align="center"><font face="verdana" size="1"><b><?php echo T_("USERNAME"); ?>:</b></font></td>
			</tr><tr>
			<td align="center"><input type="text" size="12" name="username" /></td>
			</tr><tr>
			<td align="center"><font face="verdana" size="1"><b><?php echo T_("PASSWORD"); ?>:</b></font></td>
			</tr><tr>
			<td align="center"><input type="password" size="12" name="password"  /></td>
			</tr><tr>
			<td align="center"><input type="submit" value="<?php echo T_("LOGIN"); ?>" /></td>
			</tr>
		</table>
		</td>
		</tr>
	<tr>
<td align="center">[<a href="account-signup.php"><?php echo T_("SIGNUP");?></a>]<br />[<a href="account-recover.php"><?php echo T_("RECOVER_ACCOUNT");?></a>]</td> </tr>
	</table>
    </form> 
<?php
end_block();

} else {

begin_block($CURUSER["username"]);

	$avatar = htmlspecialchars($CURUSER["avatar"]);
	if (!$avatar)
		$avatar = "https://placehold.it/200x300";

	$userdownloaded = mksize($CURUSER["downloaded"]);
	$useruploaded = mksize($CURUSER["uploaded"]);
	$privacylevel = T_($CURUSER["privacy"]);

	if ($CURUSER["uploaded"] > 0 && $CURUSER["downloaded"] == 0)
		$userratio = '<span class="label label-success pull-right">Inf.</span>';
	elseif ($CURUSER["downloaded"] > 0)
		$userratio = '<span class="label label-info pull-right">'.number_format($CURUSER["uploaded"] / $CURUSER["downloaded"].'</span>', 2);
	else
		$userratio = '<span class="label label-info pull-right">---</span>'; ?>
	<img width="200" height="300" src="<?php echo $avatar;?>" alt="" class="thumbnail center-block" />
	<ul class="list-group">
		<li class="list-group-item"><?php echo T_("DOWNLOADED");?> : <span class="label label-danger pull-right"><?php echo $userdownloaded;?></span></li>
		<li class="list-group-item"><?php echo T_("UPLOADED");?>: <span class="label label-success pull-right"><?php echo $useruploaded;?></span></li>
		<li class="list-group-item"><?php echo T_("CLASS");?>: <div class="pull-right"><?php echo T_($CURUSER["level"]);?></div></li>
		<li class="list-group-item"><?php echo T_("ACCOUNT_PRIVACY_LVL");?>: <div class="pull-right"><?php echo $privacylevel;?></div></li>
		<li class="list-group-item"><?php echo T_("RATIO");?>: <?php echo $userratio;?></span></li>
	</ul>
	<div class="text-center">
		<a href="account.php" class="btn btn-primary"><?php echo T_("ACCOUNT"); ?></a>

		<?php if ($CURUSER["control_panel"]=="yes") { ?>

		<a href="admincp.php" class="btn btn-warning"><?php echo T_("STAFFCP");?></a>

		<?php } ?>
	</div>

<?php end_block();
} ?>