<?php
global $NTS_CURRENT_REQUEST, $NTS_CURRENT_REQUEST_INDEX;
$ff =& ntsFormFactory::getInstance();
$formParams = array(
	'service'	=> $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['service'],
	);
$recurForm =& $ff->makeForm( dirname(__FILE__) . '/formRecur', $formParams );
$form =& $ff->makeForm( dirname(__FILE__) . '/form' );
?>

<!-- APPOINTMENT REQUEST FLOW -->
<?php require( dirname(__FILE__) . '/../common/flow.php' ); ?>

<h2><?php echo M('Single Or Recurring Appointments?'); ?></h2>

<table>
<tr>
<td>&nbsp;</td>
<td>
<?php $form->display(); ?>
</td>
</tr>


<tr>
<td colspan="2"><h3><?php echo M('Or Recurring Appointments?'); ?></h3></td>
</tr>

<?php $recurForm->display(); ?>

</table>
