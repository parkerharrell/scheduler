<TABLE>
<tr>
	<th><?php echo M('Label'); ?> *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'label',
			'default'	=> 'Credit Card by E-xact Transactions',
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
	<th>Test Mode</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'test',
			)
		);
	?>
	</TD>
</TR>

<tr>
	<th>Payment Page ID *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'login_id',
			'default'	=> 'linda',
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
<td colspan="2" style="padding-top: 1em;">
<i style="text-decoration: underline;">
Get these values in <b>Payment Pages -&gt; Security</b> in your E-xact account control panel.
</i>
</td>
</tr>

<tr>
	<th>Transaction Key *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'transaction_key',
			'default'	=> 'a1b2c3d4e5f6',
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
	<th>Response Key  *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'md5hash',
			'default'	=> 'linda7627',
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
<td colspan="2" style="padding-top: 1em;">
<i style="text-decoration: underline;">
Enter this URL in <b>Payment Pages -&gt; Receipt Page -&gt; Authorize.Net Protocol - Relay Response Settings </b> in your E-xact merchant account control panel.
</i>
</td>
</tr>
<tr>
	<th>Relay Response URL</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'label',
	/* attributes */
		array(
			'id'		=> 'relay_response',
			'default'	=> ntsLink::makeLink( 'system/payment', '', array('gateway' => 'e-xact') ),
			)
		);
	?>
	</TD>
</TR>

</TABLE>
