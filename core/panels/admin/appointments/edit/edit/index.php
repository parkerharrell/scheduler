<?php
global $NTS_CURRENT_USER, $NTS_READ_ONLY;

$displayActions = $NTS_CURRENT_USER->hasRole( array('admin') ) ? true : false;

$ntsdb =& dbWrapper::getInstance();
$t = new ntsTime();

$appDetails = $NTS_VIEW['object']->getByArray();
$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form', $appDetails );

$form->readonly = $NTS_READ_ONLY;

$endTimeDetails = $appDetails;
$endTimeDetails['end_time'] = $appDetails['starts_at'] + $appDetails['duration'];

$maxDuration = $appDetails['duration'] + 12 * 60 * 60;
$endTimeDetails['max_duration'] = $maxDuration; 

$formEndTime =& $ff->makeForm( dirname(__FILE__) . '/formEndTime', $endTimeDetails );
$formEndTime->readonly = $NTS_READ_ONLY;

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

<table>
<tr>
<td style="vertical-align: top;">

<?php
if( $NTS_VIEW['object']->getProp('cancelled') ){
	$class = 'alert';
	$message = M('Cancelled');
	}
else {
	if( $NTS_VIEW['object']->getProp('no_show') ){
		$class = 'alert';
		$message = M('No Show');
		}
	else {
		if( $NTS_VIEW['object']->getProp('approved') ){
			$class = 'ok';
			$message = M('Approved');
			}
		else {
			$class = 'alert';
			$message = M('Pending');
			}
		}
	}
?>
<b class="<?php echo $class; ?>"><?php echo $message; ?></b>

<?php if( $NTS_VIEW['object']->getProp('_invoice') ) : ?>
<?php
if( $paidAmount >= $invoice->getProp('amount') ){
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
<br><b class="<?php echo $class; ?>"><?php echo $message; ?></b><br>
<?php endif; ?>

<?php if( (! NTS_SINGLE_LOCATION) ) : ?>
	<h3 style="margin: 0.5em 0;"><?php echo ntsView::objectTitle($NTS_VIEW['location']); ?></h3>
<?php endif; ?>

<?php if( (! NTS_SINGLE_RESOURCE) ) : ?>
	<h4 style="margin: 0.5em 0;"><?php echo ntsView::objectTitle($NTS_VIEW['resource']); ?></h4>
<?php endif; ?>

<?php if( NTS_SINGLE_LOCATION && NTS_SINGLE_RESOURCE ) : ?>
<br>
<?php endif; ?>

<?php 
$t = new ntsTime( $NTS_VIEW['object']->getProp('starts_at') );
$timeView = $t->formatTime( 0 ); 
?>
<b>
<?php echo $t->formatWeekday(); ?>, <?php echo $t->formatDate(); ?><br>
<?php $formEndTime->display(); ?>
</b>

<br><br>
<?php echo nl2br( ntsView::appServiceView($NTS_VIEW['object']) ); ?>

<?php $form->display(); ?>

<?php
$t = new ntsTime( $NTS_VIEW['object']->getProp('created_at') );
$createdView = $t->formatWeekdayShort() . ', ' . $t->formatDate() . ' ' . $t->formatTime();
?>
<br>
<span style="font-size: 80%; font-style: italic;"><?php echo M('Created'); ?>: <?php echo $createdView; ?></span>

</td>

<!-- CUSTOMER INFO -->
<td style="vertical-align: top; border-left: #bbbbbb 1px solid;">
<h3 style="margin: 0;"><?php echo M('Customer'); ?></h3>

<?php
$restrictions = $NTS_VIEW['customer']->getProp('_restriction');
$statusOk = true;
if( $restrictions ){
	$statusOk = false;
	if( in_array('email_not_confirmed', $restrictions) )
		$status = M('Email Not Confirmed');
	elseif( in_array('not_approved', $restrictions) )
		$status = M('Not Approved');
	elseif( in_array('suspended', $restrictions) )
		$status = M('Suspended');
	else
		$status = M('N/A');
	$class =  "alert";
	}
else {
	$status = M('Active');
	$class =  "ok";
	}
?>
<?php if( ! $statusOk ) : ?>
	<p><b class="<?php echo $class; ?>"><?php echo $status; ?></b>
<?php endif; ?>

<p>
<?php if( ! NTS_EMAIL_AS_USERNAME ) : ?>
	<b>	
	<?php if( $displayActions ) : ?>
		<a target="_parent" href="<?php echo ntsLink::makeLink('admin/customers/edit', '', array('_id' => $NTS_VIEW['customer']->getId(), 'viewMode' => '') ); ?>"><?php echo $NTS_VIEW['customer']->getProp('username'); ?></a>
	<?php else : ?>
		<?php echo $NTS_VIEW['customer']->getProp('username'); ?>
	<?php endif; ?>
	</b>
	<?php echo $NTS_VIEW['customer']->getProp('email'); ?>
<?php else: ?>
	<b>	
	<?php if( $displayActions ) : ?>
		<a href="<?php echo ntsLink::makeLink('admin/customers/edit', '', array('_id' => $NTS_VIEW['customer']->getId()) ); ?>"><?php echo $NTS_VIEW['customer']->getProp('email'); ?></a>
	<?php else : ?>
		<?php echo $NTS_VIEW['customer']->getProp('email'); ?>
	<?php endif; ?>
	</b>
<?php endif; ?>
<br>
<b><?php echo $NTS_VIEW['customer']->getProp('first_name'); ?> <?php echo $NTS_VIEW['customer']->getProp('last_name'); ?></b>

<?php
$om =& objectMapper::getInstance();
$fields = $om->getFields( 'customer', 'external' );
$showFields = array();
$skip = array( 'username', 'email', 'first_name', 'last_name' );
foreach( $fields as $f ){
	if( ! in_array($f[0], $skip) )
		$showFields[] = $f;
	}
?>

<?php if( $showFields ) : ?>
<table>
<?php	foreach( $showFields as $f ) : ?>
<tr>
<?php
	$value = $NTS_VIEW['customer']->getProp( $f[0] );
	if( $f[2] == 'checkbox' )
		$value = $value ? M('Yes') : M('No');
?>
	<th><?php echo $f[1]; ?></th>
	<td><b><?php echo $value; ?></b></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

</td>
</tr>
</table>