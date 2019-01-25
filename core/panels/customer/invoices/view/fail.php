<H2><?php echo M('There has been a problem with your payment'); ?></H2>
<?php
$payments = $NTS_VIEW['payments'];
reset( $payments );

global $NTS_CURRENT_USER;
$t = new ntsTime();
//$t->setTimezone( $NTS_CURRENT_USER->getTimezone() );
?>
<?php foreach( $payments as $p ) : ?>
	<?php $t->setTimestamp( $p['paid_at'] ); ?>
	<p><?php echo $t->formatFull(); ?>
	<br><?php echo M('Gateway Response'); ?>: <b><?php echo $p['pgateway_response']; ?></b>
<?php endforeach; ?>
<p>
<a href="<?php echo ntsLink::makeLink('-current-/../pay', '', array('refno' => $NTS_VIEW['invoiceInfo']['refno']) ); ?>"><?php echo M('Please try again'); ?></a>
