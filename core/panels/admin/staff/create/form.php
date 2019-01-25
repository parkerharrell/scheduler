<h3><?php echo M('Contact Information'); ?></h3>

<?php
$class = 'user';

$om =& objectMapper::getInstance();
$fields = $om->getFields( $class, 'internal', true );
reset( $fields );
?>
<table>
<?php foreach( $fields as $f ) : ?>
<?php $c = $om->getControl( $class, $f[0], false ); ?>
<tr>
	<th><?php echo $c[0]; ?></th>
	<td>
	<?php
	echo $this->makeInput (
		$c[1],
		$c[2],
		$c[3]
		);
	?>
	</td>
</tr>
<?php endforeach; ?>

<tr>
	<th><?php echo M('Timezone'); ?></th>
	<td>
	<?php
	$timezoneOptions = ntsTime::getTimezones();
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> '_timezone',
			'options'	=> $timezoneOptions,
			'default'	=> NTS_COMPANY_TIMEZONE,
			)
		);
	?>
	</td>
</tr>

</table>

<h3><?php echo M('Password'); ?></h3>
<p>
<?php echo M('Leave these blank to autogenerate a random password'); ?>
<table>

<tr>
	<th><?php echo M('Password'); ?> *</th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'password',
	/* attributes */
		array(
			'id'		=> 'password',
			'attr'		=> array(
				'size'	=> 16,
				),
			'default'	=> '',
			'required'	=> 1,
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Confirm Password'); ?> *</th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'password',
	/* attributes */
		array(
			'id'		=> 'password2',
			'attr'		=> array(
				'size'	=> 16,
				),
			'default'	=> '',
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'confirmPassword.php', 
				'error'		=> "Passwords don't match!",
				'params'	=> array(
					'mainPasswordField' => 'password',
					),
				),
			)
		);
	?>
	</td>
</tr>
</table>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'create' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Create'); ?>">
</DIV>