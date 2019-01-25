<!-- FOOTER -->
<div id="nts-footer">
<?php 
global $NTS_CURRENT_VERSION;
$currentYear = date('Y');
?>
&copy; 2010-<?php echo $currentYear; ?> <a href="<?php echo NTS_APP_URL; ?>"><b><?php echo NTS_APP_TITLE; ?></b></a> ver. <?php echo $NTS_CURRENT_VERSION; ?>

<br>
<?php ntsLib::printCurrentExecutionTime(); ?>
<br>
<?php
$ntsdb =& dbWrapper::getInstance();
echo $ntsdb->_queryCount . ' queries';
?>
</div>
