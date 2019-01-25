<?php
global $object, $NTS_CURRENT_USER;
$ntsdb =& dbWrapper::getInstance();

$conf =& ntsConf::getInstance();
$showSessionDuration = $conf->get('showSessionDuration');
?>
<?php if( $NTS_VIEW['isRequest'] ) : ?>
<?php
	$afterRequestInfo = ( $NTS_VIEW['object'][0]->getProp('approved') ) ? 'accepted' : 'waitingApproval';
	$infoFile = dirname(__FILE__) . '/' . $afterRequestInfo . '.php';
	require( $infoFile );
?>
<p>
<?php
// check where to return
$returnUrl = '';
if( count($NTS_VIEW['object']) == 1 ){
	$service = ntsObjectFactory::get( 'service' );
	$service->setId( $NTS_VIEW['object'][0]->getProp('service_id') );
	$returnUrl = $service->getProp( 'return_url' );
	}
if( ! $returnUrl ){
	$confReturnUrl = $conf->get( 'returnAfterRequest' );
	$returnUrl = strlen($confReturnUrl) ? $confReturnUrl : ntsLink::makeLink();
	}
?>
	<?php
	###Customized by RAH 6/6/11 - Added target="_parent" to Continue link
	?>
	<a href="<?php echo $returnUrl; ?>" target="_parent"><?php echo M('Continue'); ?></a>
<?php return; ?>

<?php endif; ?>
<?php
$appDetails = $NTS_VIEW['object']->getByArray();
$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form', $appDetails );
?>
<h2><?php echo M('Appointment Details'); ?></h2>

<?php
$now = time();
$service = ntsObjectFactory::get( 'service' );
$service->setId( $NTS_VIEW['object']->getProp('service_id') );
?>
<p>
<?php if( (! $NTS_VIEW['object']->getProp('cancelled')) && (! $NTS_VIEW['object']->getProp('no_show'))) : ?>
	<?php $minCancel = $service->getProp('min_cancel'); ?>
	<?php if( ($now + $minCancel) > $NTS_VIEW['object']->getProp('starts_at') ) : ?>
		<?php echo M('You cannot cancel or reschedule this appointment now'); ?>
	<?php else : ?>
		<?php if( ! $NTS_VIEW['object']->getProp('no_show') ) : ?>
			<a href="<?php echo ntsLink::makeLink('-current-/../edit/cancel', '', array('_id' => $NTS_VIEW['object']->getId(), 'return' => 'all') ); ?>"><?php echo M('Cancel'); ?></a>
			<a href="<?php echo ntsLink::makeLink('customer/appointments/request/select_time', '', array('reschedule' => $NTS_VIEW['object']->getId()) ); ?>"><?php echo M('Reschedule'); ?></a>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
<p>
<table>
<tr>
	<th><?php echo M('Status'); ?></th>
<?php
if( $NTS_VIEW['object']->getProp('cancelled') ){
	$class = 'alert';
	$message = M('Cancelled');
	}
elseif( $NTS_VIEW['object']->getProp('no_show') ){
	$class = 'alert';
	$message = M('No Show');
	}
elseif( $NTS_VIEW['object']->getProp('approved') ){
	$class = 'ok';
	$message = M('Approved');
	}
else {
	$class = 'alert';
	$message = M('Pending');
	}
?>
	<td>
	<b class="<?php echo $class; ?>"><?php echo $message; ?></b>
	</td>
</tr>

<?php if( $NTS_VIEW['object']->getProp('_invoice') ) : ?>
<tr>
	<th><?php echo M('Payment'); ?></th>
<?php
	$invoiceId = $NTS_VIEW['object']->getProp('_invoice');
	$invoice = new ntsObject( 'invoice' );
	$invoice->setId( $invoiceId );

	/* payments for this invoice */
	$paidAmount = 0;
	$sql =<<<EOT
SELECT
	*
FROM
	{PRFX}payments
WHERE
	invoice_id = $invoiceId
ORDER BY
	paid_at DESC
EOT;

	$result = $ntsdb->runQuery( $sql );
	while( $p = $result->fetch() ){
		$paidAmount += $p['amount_gross'];
		}
?>
<?php	if( $paidAmount >= $invoice->getProp('amount') ){
		$class = 'ok';
		$message = M('Fully Paid');
		}
	elseif( $paidAmount > 0 ){
		$class = 'warning';
		$message = M('Partially Paid') . ' [' . ntsCurrency::formatPrice($paidAmount) . '/' . ntsCurrency::formatPrice($invoice->getProp('amount')) . ']';
		}
	else {
		$class = 'alert';
		$message = M('Not Paid') . ' [' . ntsCurrency::formatPrice($invoice->getProp('amount')) . ']';
		}
?>
	<td>
	<b class="<?php echo $class; ?>"><?php echo $message; ?></b>
	</td>
</tr>
<?php endif; ?>

<tr>
	<th><?php echo M('Date and Time'); ?></th>
	<td>
		<?php
			$t = new ntsTime( $NTS_VIEW['object']->getProp('starts_at'), $NTS_CURRENT_USER->getTimezone() );
			?>
		<b>
		<?php echo $t->formatWeekday(); ?>, <?php echo $t->formatDate(); ?><br>
		<?php
			$viewTime = $t->formatTime( $NTS_VIEW['object']->getProp('duration') );
			?>
		<?php echo $viewTime; ?>
		</b>
	</td>
</tr>

<tr>
	<th><?php echo M('Service'); ?></th>
	<td>
	<?php
	$serviceView = ntsView::appServiceView( $NTS_VIEW['object'] );
	echo nl2br( $serviceView );
	?>
	</td>
</tr>

<?php if( (! NTS_SINGLE_LOCATION) ) : ?>
<tr>
	<th><?php echo M('Location'); ?></th>
	<td>
		<b><?php echo ntsView::objectTitle($NTS_VIEW['location']); ?></b>
	</td>
</tr>
<?php endif; ?>

<?php if( (! NTS_SINGLE_RESOURCE) ) : ?>
<tr>
	<th><?php echo M('Bookable Resource'); ?></th>
	<td>
		<b><?php echo ntsView::objectTitle($NTS_VIEW['resource']); ?></b>
	</td>
</tr>
<?php endif; ?>

<?php if( ! $NTS_VIEW['isRequest'] ) : ?>
	<?php $form->display(); ?>
<?php else: ?>
<?php
	$otherDetails = array(
		'service_id'	=> $NTS_VIEW['object']->getProp('service_id'),
		);
	$om =& objectMapper::getInstance();
	$fields = $om->getFields( 'appointment', 'external', false, $otherDetails );
	reset( $fields );
?>
<?php	foreach( $fields as $f ) : ?>
	<tr>
	<th><?php echo $f[1]; ?></th>
	<td><?php echo  $NTS_VIEW['object']->getProp($f[0]); ?></td>
	</tr>
<?php	endforeach; ?>
<?php endif; ?>
</table>
