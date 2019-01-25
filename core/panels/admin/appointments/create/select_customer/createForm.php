<?php
$class = 'customer';

$om =& objectMapper::getInstance();
$fields = $om->getFields( $class, 'internal', true );
reset( $fields );
?>
<table>
<?php foreach( $fields as $f ) : ?>
<?php
if( (! NTS_ENABLE_REGISTRATION) && $f[0] == 'username' )
	continue;
?>
<?php $c = $om->getControl( 'customer', $f[0], false ); ?>
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

	<?php if( NTS_ALLOW_NO_EMAIL && ($c[2]['id'] == 'email') && ($class == 'customer') ) : ?>
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
	<?php endif; ?>

	</td>
</tr>
<?php endforeach; ?>

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

<div id="ntsNotifyControl">
<?php
echo $this->makeInput (
/* type */
	'checkbox',
/* attributes */
	array(
		'id'		=> 'notify',
		'default'	=> 1,
		)
	);
?>
<?php echo M('Notify Customer On Account Creation'); ?>
</div>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'create' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Create'); ?>">
</DIV>

<?php if( NTS_ALLOW_NO_EMAIL && ($class == 'customer') ) : ?>
<SCRIPT LANGUAGE="JavaScript">
var noEmailCtl = "#<?php echo $this->getName(); ?>-noEmail";
var emailCtl = "#<?php echo $this->getName(); ?>-email";
var notifyCtl = "#ntsNotifyControl";

function ntsProcessInputs_1(){
	if ( jQuery(noEmailCtl).attr("checked") == true ){
		jQuery(emailCtl).hide();
		jQuery(notifyCtl).hide();
		}
	else {
		jQuery(emailCtl).show();
		jQuery(notifyCtl).show();
		}
	}

jQuery(noEmailCtl).bind( "click", ntsProcessInputs_1 );

ntsProcessInputs_1();
</script>

<?php endif; ?>