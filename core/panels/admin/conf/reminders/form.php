<TABLE>
<tr>
	<th><?php echo M('Send Reminder Before'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'period/MinHourDayWeek',
	/* attributes */
		array(
			'id'		=> 'remindBefore',
			)
		);
	?> <?php echo M('Hours'); ?>
	</td>
</tr>

<tr>
<th>&nbsp;</th>
<td>
<?php echo $this->makePostParams('-current-', 'update'); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Save'); ?>">
</td>
</tr>
</table>