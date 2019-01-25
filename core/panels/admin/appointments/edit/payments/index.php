<?php
$ff =& ntsFormFactory::getInstance();
?>
<?php if( $NTS_VIEW['invoice'] ) : ?>
	<?php if( $NTS_VIEW['paidAmount'] >= $NTS_VIEW['invoice']->getProp('amount') ) : ?>
		<H2 class="ok"><?php echo M('Invoice'); ?> <?php echo $NTS_VIEW['invoice']->getProp('refno'); ?> <?php echo M('Fully Paid'); ?></H2>
	<?php elseif( $NTS_VIEW['paidAmount'] > 0 ) : ?>
		<H2><?php echo M('Invoice'); ?> <?php echo $NTS_VIEW['invoice']->getProp('refno'); ?> <?php echo M('Partially Paid'); ?></H2>
	<?php endif; ?>

	<p>
	<?php echo M('Invoice Value'); ?>: <b><?php echo ntsCurrency::formatPrice($NTS_VIEW['invoice']->getProp('amount')); ?></b>
	<?php echo M('Paid Value'); ?>: <b><?php echo ntsCurrency::formatPrice($NTS_VIEW['paidAmount']); ?></b>

	<?php if( $NTS_VIEW['paidAmount'] < $NTS_VIEW['invoice']->getProp('amount') ) : ?>
		<?php echo M('Due Value'); ?>: <b class="alert"><?php echo ntsCurrency::formatPrice( $NTS_VIEW['invoice']->getProp('amount') - $NTS_VIEW['paidAmount'] ); ?></b>
	<?php endif; ?>

	<?php if( $NTS_VIEW['payments'] ) : ?>
	<p>
	<h3><?php echo M('Payments'); ?></h3>
	<table class="nts-listing">
	<tr class="listing-header">
		<th><?php echo M('Date'); ?></td>
		<th><?php echo M('Amount'); ?></td>
		<th><?php echo M('Paid Through'); ?></td>
		<th><?php echo M('Gateway Reference'); ?></td>
		<th><?php echo M('Gateway Response'); ?></td>
	</tr>

	<?php foreach( $NTS_VIEW['payments'] as $p ) : ?>
	<?php $t = new ntsTime( $p['paid_at'] ); ?>
	<tr>
		<td><?php echo $t->formatFull(); ?></td>
		<td><b><?php echo ntsCurrency::formatPrice($p['amount_gross']); ?></b></td>
		<td><?php echo $p['pgateway']; ?></td>
		<td><?php echo $p['pgateway_ref']; ?></td>
		<td><?php echo $p['pgateway_response']; ?></td>
	</tr>
	<?php endforeach;  ?>
	</table>
	<?php endif; ?>
	
	<?php if( $NTS_VIEW['paidAmount'] < $NTS_VIEW['invoice']->getProp('amount') ) : ?>
<p>
<?php
		$addPaymentFormFile = dirname( __FILE__ ) . '/addPaymentForm';
		$formParams = array('amount' => ($NTS_VIEW['invoice']->getProp('amount') - $NTS_VIEW['paidAmount']) );
		$form =& $ff->makeForm( $addPaymentFormFile, $formParams );
		$form->display();
?>
	<?php endif; ?>	
<?php else : ?>
	<?php echo M('None'); ?>
<?php endif; ?>
