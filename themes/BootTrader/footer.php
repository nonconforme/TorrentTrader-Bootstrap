<?php function_exists('T_') or die;

        if ($site_config["MIDDLENAV"]){
          middleblocks();
        } ?>
        </div>
        <!--// Main Column -->
          
        <!-- Right Column -->
        <?php if ($site_config["RIGHTNAV"]){ ?>
          <div class="col-lg-2 col-sm-12">
		        <?php rightblocks(); ?>
          </div>
        <?php } ?>
        <!--// Right Column -->
      </div>
    </div>
  </div>
<!--// Content -->
</div>

<!-- Footer -->
<footer>
  <hr />
  <ul class="list-unstyled text-center">
        <li><?php printf (T_("POWERED_BY_TT"), $site_config["ttversion"]); ?></li>
        <li><?php $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart']; ?></li>
        <li><?php printf(T_("PAGE_GENERATED_IN"), $totaltime); ?></li>
        <li><a href="https://www.torrenttrader.pw" target="_blank">www.torrenttrader.pw</a> -|- <a href='rss.php'><i class="fa fa-rss-square"></i> <?php echo T_("RSS_FEED"); ?></a> - <a href='rss.php?custom=1'><?php echo T_("FEED_INFO"); ?></a></li>
        <li>Theme By: Patience</li>
      </ul>

</footer>
<!--// Footer -->
  
  <!-- Bootstrap JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="themes/<?php echo $THEME; ?>/js/bootstrap.min.js"></script>

  <!-- JS -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" ></script> 
  <script src="backend/java_klappe.js"></script>

</body>
</html>
<?php ob_end_flush(); ?>
