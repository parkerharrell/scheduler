<TABLE>
<tr>
<td colspan="2">
<span class="alert"><?php echo M('Warning: your current data will be deleted by restoring from backup file'); ?></span>
</td>
</tr>

<tr>
	<th><?php echo M('Backup File'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'upload',
	/* attributes */
		array(
			'id'		=> 'file',
			)
		);
	?>
	</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>
<?php echo $this->makePostParams('-current-', 'upload'); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Restore'); ?>">
</td>
</table>