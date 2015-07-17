<?php
//
//  TorrentTrader v2.x
//      $LastChangedDate: 2012-06-14 17:31:26 +0100 (Thu, 14 Jun 2012) $
//      $LastChangedBy: torrenttrader $
//
//      http://www.torrenttrader.org
//
//
require_once("backend/functions.php");
dbconn();

if ($site_config['SHOUTBOX']){

//DELETE MESSAGES
if (isset($_GET['del'])){

	if (is_numeric($_GET['del'])){
		$query = "SELECT * FROM shoutbox WHERE msgid=".$_GET['del'] ;
		$result = SQL_Query_exec($query);
	}else{
		echo "invalid msg id STOP TRYING TO INJECT SQL";
		exit;
	}

	$row = mysql_fetch_row($result);
		
	if ($row && ($CURUSER["edit_users"]=="yes" || $CURUSER['username'] == $row[1])) {
		$query = "DELETE FROM shoutbox WHERE msgid=".$_GET['del'] ;
		write_log("<b><font color='orange'>Shout Deleted: </font> Deleted by   ".$CURUSER['username']."</b>");
		SQL_Query_exec($query);	
	}
}

//INSERT MESSAGE
if (!empty($_POST['message']) && $CURUSER) {	
	$_POST['message'] = sqlesc($_POST['message']);
	$query = "SELECT COUNT(*) FROM shoutbox WHERE message=".$_POST['message']." AND user='".$CURUSER['username']."' AND UNIX_TIMESTAMP('".get_date_time()."')-UNIX_TIMESTAMP(date) < 30";
	$result = SQL_Query_exec($query);
	$row = mysql_fetch_row($result);

	if ($row[0] == '0') {
		$query = "INSERT INTO shoutbox (msgid, user, message, date, userid) VALUES (NULL, '".$CURUSER['username']."', ".$_POST['message'].", '".get_date_time()."', '".$CURUSER['id']."')";
		SQL_Query_exec($query);
	}
}

//GET CURRENT USERS THEME AND LANGUAGE
if ($CURUSER){
	$ss_a = @mysql_fetch_assoc(@SQL_Query_exec("select uri from stylesheets where id=" . $CURUSER["stylesheet"]));
	if ($ss_a)
		$THEME = $ss_a["uri"];
}else{//not logged in so get default theme/language
	$ss_a = mysql_fetch_assoc(SQL_Query_exec("select uri from stylesheets where id='" . $site_config['default_theme'] . "'"));
	if ($ss_a)
		$THEME = $ss_a["uri"];
}

if(!isset($_GET['history'])){ ?>

<html>
<head>
	<title><?php echo $site_config['SITENAME'].':'.T_("SHOUTBOX"); ?></title>

	<meta http-equiv="refresh" content="300" />
	<!-- Bootstrap CSS -->
  	<link rel="stylesheet" href="themes/<?php echo $THEME; ?>/css/bootstrap.min.css">
  
  	<!-- Style CSS -->
  	<link rel="stylesheet" href="themes/<?php echo $THEME; ?>/css/style.css" />

  	<!-- Fonts -->
  	<link rel="stylesheet" href="themes/<?php echo $THEME; ?>/css/font-awesome.min.css">
	<script src="backend/java_klappe.js"></script>
</head>
<body>

	<?php }else{
    
    if ($site_config["MEMBERSONLY"]) {
        loggedinonly();
    }
    
	stdhead();
	begin_frame(T_("SHOUTBOX_HISTORY"));

	$query = 'SELECT COUNT(*) FROM shoutbox';
	$result = SQL_Query_exec($query);
	$row = mysql_fetch_row($result); ?>
	<nav class="text-center">
  		<ul class="pagination">
	<?php $pages = round($row[0] / 100) + 1;
	$i = 1;
	while ($pages > 0){ ?>

			<li><a href='shoutbox.php?history=1&amp;page=<?php echo $i;?>'><?php echo $i;?></a></li>

	<?php $i++;
		$pages--;
	} ?>

		</ul>
	</nav>
	<dl class="dl-horizontal">
	<?php }



if (isset($_GET['history'])) {
	if (isset($_GET['page'])) {
		if($_GET['page'] > '1') {
			$lowerlimit = $_GET['page'] * 100 - 100;
			$upperlimit = $_GET['page'] * 100;
		}else{
			$lowerlimit = 0;
			$upperlimit = 100;
		}
	}else{
		$lowerlimit = 0;
		$upperlimit = 100;
	}	
	$query = 'SELECT * FROM shoutbox ORDER BY msgid DESC LIMIT '.$lowerlimit.','.$upperlimit;
}else{
	$query = 'SELECT * FROM shoutbox ORDER BY msgid DESC LIMIT 20';
}

$result = SQL_Query_exec($query);
$alt = false;

while ($row = mysql_fetch_assoc($result)) {
	if ($alt){	
		$alt = false;
	}else{
		$alt = true;
	} ?>

	<dt><?php echo date('jS M, g:ia', utc_to_tz_time($row['date'])); ?></dt>

<?php if ( ($CURUSER["edit_users"]=="yes") || ($CURUSER['username'] == $row['user']) ){ ?>
		
	<dd><a href='shoutbox.php?del=<?php echo $row['msgid']; ?>'><i class="fa fa-trash-o"></i></a>
	
	<?php } ?>

	<a href="account-details.php?id=<?php echo $row['userid']; ?>" target="_parent"><strong><?php echo $row['user']; ?>:</strong></a><?php echo nl2br(format_comment($row['message'])); ?></dd>

	<?php } ?>

</dl>

<?php 

if(!isset($_GET['history'])){ 
	echo "</body></html>";
}else{
	end_frame();
	stdfoot();
}


}//END IF $SHOUTBOX
else{
	echo T_("SHOUTBOX_DISABLED");
}
?>

