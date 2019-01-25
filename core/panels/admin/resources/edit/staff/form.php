<?php
echo $this->makeInput (
/* type */
	'resourceAdmins',
/* attributes */
	array(
		'id'	=> 'staff',
		)
	);
?>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
</DIV>