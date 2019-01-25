<?php
$id = $NTS_VIEW['id'];
$customerAppointmentsCount = $NTS_VIEW['customerAppointmentsCount'];

$ff =& ntsFormFactory::getInstance();
$confirmForm =& $ff->makeForm( dirname(__FILE__) . '/confirmForm' );
?>
<?php if( is_array($id) ) : ?>
	<H2>Do you really want to delete these <?php echo count($id); ?> users?</H2>
	<p>
	<?php if ( $customerAppointmentsCount ) : ?>
	<p>
	Please note that there are also <?php echo $customerAppointmentsCount; ?> appointment(s) associated with this customers. They <b>will also be deleted</b> if your delete the customer accounts.
	<?php endif; ?>
<?php else : ?>
	<H2><?php echo M('Are you sure?'); ?></H2>
	<p>
	<?php if ( $customerAppointmentsCount ) : ?>
	<p>
	Please note that there are also <a href="<?php echo ntsLink::makeLink('-current-/../appointments'); ?>"><?php echo $customerAppointmentsCount; ?> appointment(s)</a> associated with this customer. They <b>will also be deleted</b> if your delete the customer account.
	<?php endif; ?>
<?php endif; ?>

<table>
<tr>
	<td>
	<?php
	$confirmForm->display();
	?>
	</td>
	<td>
	<A HREF="javascript:history.go(-1);"><?php echo M('Cancel'); ?></A>
	</td>
</tr>
</table>