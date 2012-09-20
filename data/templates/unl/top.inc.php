<?php

require_once 'UNL/Templates.php';

UNL_Templates::$options['version'] = UNL_Templates::VERSION3x1;
$GLOBALS['unlTemplatedPage'] = UNL_Templates::factory('fixed');

$GLOBALS['unlTemplatedPage']->doctitle = '<title>'. filter((isset($pagetitle) ? $pagetitle . ' | ' : '') . $GLOBALS['sitename']) . ' | University of Nebraska&ndash;Lincoln</title>';

// Start a buffer to capture <head>. Will be closed below.
ob_start();
?>

    <link rel="stylesheet" type="text/css" href="<?php echo $theme->resource('scuttle.css');?>" />
    <link rel="search" type="application/opensearchdescription+xml" href="<?php echo ROOT ?>api/opensearch.php" title="<?php echo htmlspecialchars($GLOBALS['sitename']) ?>"/>
<?php
if (isset($rsschannels)) {
    $size = count($rsschannels);
    for ($i = 0; $i < $size; $i++) {
        echo '    <link rel="alternate" type="application/rss+xml" title="'
            . htmlspecialchars($rsschannels[$i][0]) . '"'
            . ' href="'. htmlspecialchars($rsschannels[$i][1]) .'" />' . "\n";
    }
}
?>

<?php if (isset($loadjs)) :?>
    <script>var $ = jQuery = WDN.jQuery;</script>
<?php if (DEBUG_MODE) : ?>
    <script type="text/javascript" src="<?php echo ROOT_JS ?>jquery.jstree.js"></script>
<?php else: ?>
    <script type="text/javascript" src="<?php echo ROOT_JS ?>jquery.jstree.min.js"></script>
<?php endif ?>
    <script type="text/javascript" src="<?php echo ROOT ?>jsScuttle.php"></script>
<?php endif ?>


<?php
// Append and flush the buffer started above
$GLOBALS['unlTemplatedPage']->head = ob_get_clean();

$GLOBALS['unlTemplatedPage']->navlinks = '<ul>
                                          <li><a href="./">Home</a>
                                              <ul>
                                              <li><a href="./">Sub-link 1</a></li>
                                              <li><a href="./">Sub-link 2</a></li>
                                              </ul>
                                          </li>
                                          </ul>';
$GLOBALS['unlTemplatedPage']->breadcrumbs = '<ul>
                                             <li><a href="http://www.unl.edu/">UNL</a></li>
                                             <li><a href="' . ROOT . '">' . $GLOBALS['sitename'] . '</a></li>
                                             </ul>';
$GLOBALS['unlTemplatedPage']->titlegraphic = $GLOBALS['sitename'];
$GLOBALS['unlTemplatedPage']->pagetitle = '<h1>' . $GLOBALS['welcomeMessage'] . '</h1>';

// Start a buffer to capture maincontent. Will be closed in bottom.inc.php
ob_start();

$headerstyle = '';
if(isset($_GET['popup'])) {
    $headerstyle = ' class="popup"';
}
?>

<div id="header" <?php echo $headerstyle; ?>>
<?php
if(!isset($_GET['popup'])) {
    $this->includeTemplate('toolbar.inc');
}
?></div>

<?php
if(DEBUG_MODE) {
    echo '<p class="error">'. T_('Admins, your installation is in "Debug Mode" ($debugMode = true). To go in "Normal Mode" and hide debugging messages, change $debugMode to false into config.php.') ."</p>\n";
}
if (isset($error) && $error!='') {
    echo '<p class="error">'. $error ."</p>\n";
}
if (isset($msg) && $msg!='') {
    echo '<p class="success">'. $msg ."</p>\n";
}
if (isset($tipMsg) && $tipMsg!='') {
    echo '<p class="tipMsg">'. $tipMsg ."</p>\n";
}
