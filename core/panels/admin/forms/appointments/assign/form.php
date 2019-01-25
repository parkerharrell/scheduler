<?php
$chooseOptions = $this->getValue( 'forms' );
$serviceId = $this->getValue( 'serviceId' ); 

echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> '_form',
		'options'	=> $chooseOptions,
		)
	);
?>
<?php echo $this->makePostParams('-current-', 'update', array('service_id' => $serviceId) ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
