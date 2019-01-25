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
			'default'	=> 'Credit Card by Authorize.Net',
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
<td colspan="2" style="padding-top: 1em;">
<i style="text-decoration: underline;">
Get these values in <b>Account -&gt; General Security Settings -&gt;  API Login ID and Transaction Key</b> in your Authorize.Net merchant account control panel.
</i>
</td>
</tr>

<tr>
	<th>API Login ID *</th>
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
<td colspan="2" style="padding-top: 1em;">
<i style="text-decoration: underline;">
Configure this value in <b>Account -&gt; General Security Settings -&gt; MD5-Hash</b> in your Authorize.Net merchant account control panel.
</i>
</td>
</tr>

<tr>
	<th>MD5 Hash *</th>
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
Enter this URL in <b>Account -&gt; Transaction Response Settings -&gt; Relay Response</b> in your Authorize.Net merchant account control panel.
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
			'default'	=> ntsLink::makeLink( 'system/payment', '', array('gateway' => 'authorizenet') ),
			)
		);
	?>
	</TD>
</TR>

</TABLE>
