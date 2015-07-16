<?php function_exists('T_') or die; ?>
<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="<?php echo $site_config["CHARSET"]; ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="author" content="Patience" />
  <meta name="generator" content="TorrentTrader <?php echo $site_config['ttversion']; ?>" />
  <meta name="description" content="TorrentTrader is a feature packed and highly customisable PHP/MySQL Based BitTorrent tracker. Featuring intergrated forums, and plenty of administration options. Please visit www.torrenttrader.org for the support forums. " />
  <meta name="keywords" content="https://github.com/PatienceBK" />

  <title><?php echo $title; ?></title>

  <!--Favicon-->
  <link rel="apple-touch-icon" sizes="57x57" href="images/favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="images/favicon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="images/favicon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="images/favicon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="images/favicon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="images/favicon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="images/favicon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="images/favicon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="images/favicon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="images/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="images/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="images/favicon/favicon-16x16.png">
  <link rel="manifest" href="images/favicon/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="images/favicon/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="themes/<?php echo $THEME; ?>/css/bootstrap.min.css">
  
  <!-- Style CSS -->
  <link rel="stylesheet" href="themes/<?php echo $THEME; ?>/css/style.css" />

  <!-- Fonts -->
  <link rel="stylesheet" href="themes/<?php echo $THEME; ?>/css/font-awesome.min.css">

</head>
<body>
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand" href="<?php echo $site_config['SITEURL']; ?>"><?php echo $site_config['SITENAME']; ?></a>
      </div>
      <ul class="nav navbar-nav navbar-right">
      <?php
                if (!$CURUSER){
                  ?>

                    <li><a href="account-login.php"><?php echo T_("LOGIN"); ?></a></li><p class="navbar-text"><strong><?php echo T_("OR"); ?></strong></p><li><a href="account-signup.php"><?php echo T_("SIGNUP"); ?></a></li>

                  <?php
                }else{
                  ?>

                    <p class="navbar-text"><?php echo T_("LOGGED_IN_AS").": ".$CURUSER["username"]; ?></p>
                    <li><a href="account-logout.php"><?php echo T_("LOGOUT"); ?></a></li>

                    <?php
                    if ($CURUSER["control_panel"]=="yes") {
                    ?>

                        <li><a href='admincp.php'><?php echo T_("STAFFCP"); ?></a></li>

                    <?php
                    }
            
                    //check for new pm's
                    $res = SQL_Query_exec("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " and unread='yes' AND location IN ('in','both')");
                    $arr = mysql_fetch_row($res);
                    $unreadmail = $arr[0];
                    if ($unreadmail){
                      ?>

                        <li><a href='mailbox.php?inbox'><strong><span class="label label-warning"><?php echo $unreadmail; ?></span> <?php echo P_("NEWPM", $unreadmail); ?></strong></a></li>

                         <?php 
                    }else{
                      ?>

                        <li><a href='mailbox.php'><?php echo T_("YOUR_MESSAGES"); ?></a></li>

                         <?php 
                    }
                    //end check for pm's
                }
                ?>
              </ul>
    </div>
  </nav>
  <!-- Header -->
  <header>
      <div class="header-text"> 
        <div class="container">
            <h1><?php echo $site_config['SITENAME']; ?></h1>
            <p>TorrentTrader beautiful and completely rewritten in Bootstrap 3 using HTML5 and CSS3.</p>
        </div>
        
      </div>
        <!-- Infobar -->
        <div class='infobar'>
          
        </div>
        <!--// Infobar -->

  </header>
  <!--// Header -->

  <!-- Navigation -->
  <nav class="navbar navbar-default">
    <div class="container">

          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
          </div>

          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
              <li><a href="index.php"><?php echo T_("HOME");?></a></li>
              <li><a href="forums.php"><?php echo T_("FORUMS");?></a></li>
              <li><a href="torrents-upload.php"><?php echo T_("UPLOAD_TORRENT");?></a></li>
              <li><a href="torrents.php"><?php echo T_("BROWSE_TORRENTS");?></a></li>
              <li><a href="torrents-today.php"><?php echo T_("TODAYS_TORRENTS");?></a></li>
              <li><a href="torrents-search.php"><?php echo T_("SEARCH_TORRENTS");?></a></li>
            </ul>
          </div>

    </div>
  </nav>
  <!--// Navigation -->

  <!-- Content -->
  <div id='main'>
    <div class="row">
      <div class="col-lg-12">
        <!-- Left Column -->
        <?php if ($site_config["LEFTNAV"]){?>
          <div class="col-lg-2 col-sm-12">
		        <?php leftblocks();?>
          </div>  
        <?php } ?>
        <!--// Left Column -->

        <!-- Main Column -->
        <div class="col-lg-8 col-sm-12">
          
