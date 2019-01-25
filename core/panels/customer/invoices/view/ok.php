<?php if( $NTS_VIEW['paidAmount'] >= $NTS_VIEW['invoiceInfo']['amount'] ) : ?>
	<H2><?php echo M('Invoice'); ?> <?php echo $NTS_VIEW['invoiceInfo']['refno']; ?> <?php echo M('Fully Paid'); ?></H2>
<?php elseif( $NTS_VIEW['paidAmount'] > 0 ) : ?>
	<H2><?php echo M('Invoice'); ?> <?php echo $NTS_VIEW['invoiceInfo']['refno']; ?> <?php echo M('Partially Paid'); ?></H2>
<?php endif; ?>

<?php if( $NTS_VIEW['payments'] ) : ?>
<p>
<table>
<tr>
	<td style="padding: 0.5em 1em;"><?php echo M('Date'); ?></td>
	<td style="padding: 0.5em 1em;"><?php echo M('Amount'); ?></td>
</tr>

<?php foreach( $NTS_VIEW['payments'] as $p ) : ?>
<?php $t = new ntsTime( $p['paid_at'], $NTS_CURRENT_USER->getprop('_timezone') ); ?>
<tr>
	<td style="padding: 0.25em 1em;"><?php echo $t->formatFull(); ?></td>
	<td style="padding: 0.25em 1em;"><b><?php echo ntsCurrency::formatPrice($p['amount_gross']); ?></b></td>
</tr>
<?php endforeach;  ?>
</table>
<?php endif; ?>