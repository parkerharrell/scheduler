<?php
$ntsdb =& dbWrapper::getInstance();
$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

$conf =& ntsConf::getInstance();
$commonHeader = $conf->get('emailCommonHeader');
$commonFooter = $conf->get('emailCommonFooter');
?>
<table>
<?php
// count providers and customers

?>
<tr>
	<th><?php echo M('Send To'); ?></th>
	<td>
	
	<?php
	/* count */
	$customersCount = $integrator->countUsers( array('_role' => '="customer"') );
	$providersCount = $integrator->countUsers( array('_role' => "='admin'") );

	$sendToOptions = array();
	$sendToOptions[] = array( 'customers', M('Customers') . ' [' . $customersCount . ']' );
	$sendToOptions[] = array( 'providers', M('Administrative Users') . ' [' . $providersCount . ']' );
	?>
<?php
	echo $this->makeInput (
	/* type */
		'radio',
	/* attributes */
		array(
			'id'		=> 'send_to',
			'value'		=> 'customer',
			'default'	=> 'customer',
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Please choose whom to send your newsletter to'),
				),
			)
		);
	?> <?php echo M('Customers'); ?> [<?php echo $customersCount; ?>]

<?php
	echo $this->makeInput (
	/* type */
		'radio',
	/* attributes */
		array(
			'id'		=> 'send_to',
			'value'		=> 'admin',
			'default'	=> 'customer',
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Please choose whom to send your newsletter to'),
				),
			)
		);
	?> <?php echo M('Administrative Users'); ?> [<?php echo $providersCount; ?>]
	</td>
</tr>

<tr>
	<th><?php echo M('Subject'); ?> *</th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'subject',
			'attr'		=> array(
				'size'	=> 42,
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
	<th><?php echo M('Message'); ?> *</th>
	<td>
	<a href="<?php echo ntsLink::makeLink('admin/conf/email_settings'); ?>"><?php echo M('Header'); ?>: <?php echo M('Edit'); ?></a>
	<br>
	<?php echo $commonHeader; ?>
	<br>
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'text',
			'attr'		=> array(
				'cols'	=> 48,
				'rows'	=> 8,
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
	<br>
	<?php echo nl2br($commonFooter); ?>
	<br>
	<a href="<?php echo ntsLink::makeLink('admin/conf/email_settings'); ?>"><?php echo M('Footer'); ?>: <?php echo M('Edit'); ?></a>
	</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>
<?php echo $this->makePostParams('-current-', 'send' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Send'); ?>">
</td>
</tr>
</table>
