<?php
//BEGIN FRAME
function begin_frame($caption = "-", $align = "justify"){
    global $THEME, $site_config;
    
    $blockId = 'f-' . sha1($caption);
    ?>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $caption ?><a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>"></a></h3>
        </div>
        <div class="panel-body slidingDiv<?php echo $blockId; ?>">
    <?php
}

//END FRAME
function end_frame() {
    global $THEME, $site_config;
    ?>
        </div>
    </div>
    <?php
}

//BEGIN BLOCK
function begin_block($caption = "-", $align = "justify"){
    global $THEME, $site_config;
    
    $blockId = 'b-' . sha1($caption);
    ?>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $caption ?><a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>"></a></h3>
        </div>
        <div class="panel-body slidingDiv<?php echo $blockId; ?>">
    <?php
}

//END BLOCK
function end_block(){
    global $THEME, $site_config;
    ?>
        </div>
    </div>
    <?php
}

function begin_table(){
    print("<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" class=\"ttable_headouter\" width=\"100%\"><tr><td><table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" class=\"ttable_headinner\" width=\"100%\">\n");
}

function end_table()  {
    print("</table></td></tr></table>\n");
}

function tr($x,$y,$noesc=0) {
    if ($noesc)
        $a = $y;
    else {
        $a = htmlspecialchars($y);
        $a = str_replace("\n", "<br />\n", $a);
    }
    print("<tr><td class=\"heading\" valign=\"top\" align=\"right\">$x</td><td valign=\"top\" align=\"left\">$a</td></tr>\n");
}
?>

