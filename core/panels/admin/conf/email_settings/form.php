<?php
/* tags */
$tm =& ntsEmailTemplateManager::getInstance();
$tags = $tm->getTags( 'common-header-footer' );
?>

<TABLE>
<tr>
	<th><?php echo M('Sender Email'); ?> *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'email-sent-from',
			'attr'		=> array(
				'size'	=> 42,
				),
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
	</TD>
</TR>

<tr>
	<th><?php echo M('Sender Name'); ?> *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'email-sent-from-name',
			'attr'		=> array(
				'size'	=> 32,
				),
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
	</TD>
</TR>	 	

<tr>
	<th><?php echo M('Email Test Mode'); ?></th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'email-debug',
			)
		);
	?>
	<br>
	<i><?php echo M('If set, email messages will be printed on screen rather than sent'); ?></i>
	</TD>
</TR>

<tr>
	<th><?php echo M('Email'); ?>: <?php echo M('Disable'); ?></th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'email-disabled',
			)
		);
	?>
	</TD>
</TR>
</TABLE>

<p>
<TABLE>
<tr>
	<th><?php echo M('Header For All Emails'); ?></th>
	<th><?php echo M('Tags'); ?></th>
</tr>
<tr>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'email-header',
			'attr'		=> array(
				'cols'	=> 48,
				'rows'	=> 3,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
	<td rowspan="3" style="vertical-align: top;">
		<?php foreach( $tags as $t ) : ?>
			<?php echo $t; ?><br>
		<?php endforeach; ?>
	</td>
</tr>

<tr>
	<th><?php echo M('Footer For All Emails'); ?> *</th>
</tr>
<tr>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'email-footer',
			'attr'		=> array(
				'cols'	=> 48,
				'rows'	=> 3,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>
</TABLE>

<h3><?php echo M('SMTP Settings'); ?></h3>
<i><?php echo M('Fill in if required by your web hosting. You may need to consult your web hosting administrator or help documentation.'); ?></i>

<TABLE>
<tr>
	<th><?php echo M('Host'); ?></th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'smtp-host',
			'attr'		=> array(
				'size'	=> 42,
				),
			),
	/* validators */
		array(
			)
		);
	?>
	</TD>
</TR>

<tr>
	<th><?php echo M('Username'); ?></th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'smtp-user',
			'attr'		=> array(
				'size'	=> 42,
				),
			),
	/* validators */
		array(
			)
		);
	?>
	</TD>
</TR>
<tr>
	<th><?php echo M('Password'); ?></th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'smtp-pass',
			'attr'		=> array(
				'size'	=> 42,
				),
			),
	/* validators */
		array(
			)
		);
	?>
	</TD>
</TR>
<tr>
	<th><?php echo M('Secure'); ?></th>
	<TD>
	<?php
	$secureOptions = array(
		array( '', M('None') ),
		array( 'tls', 'TLS' ),
		array( 'ssl', 'SSL' ),
		);
	
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'smtp-secure',
			'options'	=> $secureOptions,
			),
	/* validators */
		array(
			)
		);
	?>
	</TD>
</TR>
</TABLE>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update'); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Save'); ?>">
</DIV>