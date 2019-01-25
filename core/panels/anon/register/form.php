<?php
$om =& objectMapper::getInstance();
$fields = $om->getFields( 'customer', 'external', true );
reset( $fields );
?>
<table>
<?php foreach( $fields as $f ) : ?>
<?php
if( $f[0] == 'username' )
	continue;
?>
<?php $c = $om->getControl( 'customer', $f[0], false ); ?>
<?php
if( isset($f[4]) ){
	if( $f[4] == 'read' ){
		$c[2]['readonly'] = 1;
		}
	}
?>
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
<?php if( $c[2]['description'] ) : ?>
&nbsp;<i><?php echo $c[2]['description']; ?></i>
<?php endif; ?>
	</td>
</tr>

<?php if( NTS_ALLOW_NO_EMAIL && ($c[2]['id'] == 'email') ) : ?>
<tr>
	<th>&nbsp;</th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'noEmail',
			)
		);
	?><?php echo M('No Email?'); ?>
	</td>
</tr>
<?php endif; ?>

<?php endforeach; ?>

<?php if( NTS_ENABLE_TIMEZONES > 0 ) : ?>
	<tr>
		<th><?php echo M('My Timezone'); ?></th>
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
				'default'	=> NTS_COMPANY_TIMEZONE
				)
			);
		?>
		</td>
	</tr>
<?php endif; ?>
</table>

<h3><?php echo M('Login details'); ?></h3>

<table>
<?php if( ! NTS_EMAIL_AS_USERNAME ) : ?>
	<tr>
		<th><?php echo M('Desired Username'); ?> *</th>
		<td>
		<?php
		echo $this->makeInput (
		/* type */
			'text',
		/* attributes */
			array(
				'id'		=> 'username',
				'attr'		=> array(
					'size'	=> 16,
					),
				'default'	=> '',
				'required'	=> 1,
				),
		/* validators */
			array(
				array(
					'code'		=> 'notEmpty.php', 
					'error'		=> M('Required field'),
					),
				array(
					'code'		=> 'checkUsername.php', 
					'error'		=> M('Already in use'),
					'params'	=> array(
						'skipMe'	=> 1,
						)
					),
				)
			);
		?>
		</td>
	</tr>
<?php endif; ?>
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
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required field'),
				),
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
				'error'		=> M("Passwords don't match!"),
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
<?php echo $this->makePostParams('-current-', 'register' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Register'); ?>">
</DIV>