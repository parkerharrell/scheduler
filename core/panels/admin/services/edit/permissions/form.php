<?php
$permissionOptions = array(
	array( 'not_allowed',	M('Not Allowed') ),
	array( 'not_shown',	M('Not Shown') ),
	array( 'allowed',		M('Company Confirmation Required') ),
	array( 'auto_confirm',	M('Auto Confirmed') ),
	);
$groups = array(
	array( -1, M('Non Registered Users') ),
	array( 0, M('Registered Users') )
	);
?>
<table class="nts-listing">
<tr class="listing-header">
	<th>&nbsp;</th>
	<th><?php echo M('Request Permissions'); ?></th>
</tr>

<?php foreach( $groups as $g ) : ?>
<tr>
	<td><?php echo $g[1]; ?></td>
	<td>
<?php
	$ctlId = 'group' . $g[0];
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> $ctlId,
			'options'	=> $permissionOptions,
			)
		);
?>
	</td>
</tr>
<?php endforeach; ?>

<tr>
<td>&nbsp;</td>
<td>
<?php echo $this->makePostParams('-current-', 'update', array('id' => $this->getValue('id')) ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
</td>
</table>
