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
	// skip email check if no registration
	if(! NTS_ENABLE_REGISTRATION ){
		if( $f[0] == 'email' ){
			/* traverse validators */
			reset( $c[3] );
			$copyVali = $c[3];
			$c[3] = array();
			foreach( $copyVali as $vali ){
				if( preg_match('/checkUserEmail\.php$/', $vali['code']) ){
					continue;
					}
				$c[3][] = $vali;
				}
			}
		}

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

<?php if( NTS_ENABLE_REGISTRATION ) : ?>
	<tr>
		<td colspan="2">
		<h2><?php echo M('Login details'); ?></h2>
		</td>
	</tr>

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
					'error'		=> 'Required field',
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
<?php endif; ?>

<tr>
	<th>&nbsp;</th>
	<td>
<?php echo $this->makePostParams('-current-', 'register' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Continue'); ?>">
	</td>
</table>