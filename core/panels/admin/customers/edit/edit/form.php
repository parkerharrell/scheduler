<?php
$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

$id = $this->getValue('id');
/* form params - used later for validation */
$this->setParams(
	array(
		'myId'	=> $id,
		)
	);
	
$object = $this->getValue('object');
$className = 'customer';

$om =& objectMapper::getInstance();
$fields = $om->getFields( $className, 'internal', true );
reset( $fields );

/* status */
$restrictions = $object->getProp( '_restriction' );
?>
<table>
<tr>
	<th><?php echo M('User Status'); ?></th>
	<td>
	<?php if( $restrictions ) : ?>
		<span class="alert">
		<?php if( in_array('email_not_confirmed', $restrictions) ) : ?>
			<?php echo M('Email Not Confirmed'); ?>
		<?php elseif( in_array('not_approved', $restrictions) ) : ?>
			<?php echo M('Not Approved'); ?>
		<?php elseif( in_array('suspended', $restrictions) ) : ?>
			<?php echo M('Suspended'); ?>
		<?php endif; ?>
		</span>
		&nbsp;
		<a class="ok" href="<?php echo ntsLink::makeLink('-current-', 'activate', array('id' => $id, 'return' => 'edit') ); ?>"><?php echo M('Activate'); ?></a>
	<?php else : ?>
		<span class="ok">
		<?php echo M('Active'); ?>
		</span>
		&nbsp;
		<a class="alert" href="<?php echo ntsLink::makeLink('-current-', 'suspend', array('id' => $id, 'return' => 'edit') ); ?>"><?php echo M('Suspend'); ?></a>
	<?php endif; ?>
	</td>
</tr>

<?php foreach( $fields as $f ) : ?>
<?php $c = $om->getControl( 'customer', $f[0], false ); ?>
<tr>
	<th><?php echo $c[0]; ?></th>
	<td>
	<?php
	if( defined('NTS_REMOTE_INTEGRATION') && (NTS_REMOTE_INTEGRATION == 'wordpress') && ($c[2]['id'] == 'username') ){
		$c[1] = 'label';
		}
	echo $this->makeInput (
		$c[1],
		$c[2],
		$c[3]
		);
	?>
	<?php if( NTS_ALLOW_NO_EMAIL && ($c[2]['id'] == 'email') ) : ?>
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
	<?php if( NTS_ALLOW_DUPLICATE_EMAILS && ($c[2]['id'] == 'email') ) : ?>
<?php // 	check if there're duplicates
			$checkEmail = $this->getValue('email');
			$countDuplicates = 0;
			if( strlen($checkEmail) ){
				$myWhere = array();
				$myWhere['email'] = " = \"$checkEmail\"";
				$myWhere['id'] = " <> $id";
				$countDuplicates = $integrator->countUsers( $myWhere );
				}
?>
		<?php if( $countDuplicates ) : ?>
			<br>Also <a href="<?php echo ntsLink::makeLink('admin/customers/browse', 'search', array('email' => $checkEmail) ); ?>"><?php echo $countDuplicates; ?> other user(s)</a> with this email
		<?php endif; ?>
	<?php endif; ?>
	</td>
</tr>
<?php endforeach; ?>

<?php if( NTS_ENABLE_TIMEZONES > 0 ) : ?>
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
			)
		);
	?>
	</td>
</tr>
<?php endif; ?>

</table>

<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update' ); ?>
<INPUT TYPE="submit" VALUE="Update">
</DIV>

<?php if( NTS_ALLOW_NO_EMAIL ) : ?>
<?php endif; ?>

<SCRIPT LANGUAGE="JavaScript">
var noEmailCtl = "#<?php echo $this->getName(); ?>-noEmail";
var emailCtl = "#<?php echo $this->getName(); ?>-email";

function ntsProcessInputs_1(){
	if ( jQuery(noEmailCtl).attr("checked") == true ){
		jQuery(emailCtl).hide();
		}
	else {
		jQuery(emailCtl).show();
		}
	}

jQuery(noEmailCtl).bind( "click", ntsProcessInputs_1 );

ntsProcessInputs_1();
</script>