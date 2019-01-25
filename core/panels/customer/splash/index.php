<?php
$conf =& ntsConf::getInstance();
$firstTimeSplash = $conf->get('firstTimeSplash');
?>
<?php echo $firstTimeSplash; ?>
<p>
<a href="<?php echo ntsLink::makeLink('-current-', 'ok' ); ?>"><?php echo M('OK'); ?></a>