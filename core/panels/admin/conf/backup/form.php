<TABLE>
<tr>
	<th><?php echo M('Remind Me To Backup'); ?></th>
	<td>
	<?php
	$selectOptions = array(
		array( 0, M('No') ),
		array( 7 * 24 * 60 * 60, '7 ' . M('Days') ),
		array( 14 * 24 * 60 * 60, '14 ' . M('Days') ),
		array( 30 * 24 * 60 * 60, '30 ' . M('Days') ),
		);

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'remindOfBackup',
			'options'	=> $selectOptions
			)
		);
	?>
	</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>
<?php echo $this->makePostParams('-current-', 'update'); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Save'); ?>">
</td>
</tr>
</table>