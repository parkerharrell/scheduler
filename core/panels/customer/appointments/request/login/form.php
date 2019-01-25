<div id="ntsLoginForm">
<table>
<?php if( ! NTS_EMAIL_AS_USERNAME ) : ?>
	<tr>
		<th><?php echo M('Username'); ?></th>
		<td>
		<?php
		echo $this->makeInput (
		/* type */
			'text',
		/* attributes */
			array(
				'id'		=> 'username',
				'attr'		=> array(
					'size'	=> 24,
					),
				)
			);
		?>
		</td>
	</tr>
<?php else : ?>
	<tr>
		<th><?php echo M('Email'); ?></th>
		<td>
		<?php
		echo $this->makeInput (
		/* type */
			'text',
		/* attributes */
			array(
				'id'		=> 'email',
				'attr'		=> array(
					'size'	=> 32,
					),
				)
			);
		?>
		</td>
	</tr>
<?php endif; ?>

<tr>
	<th><?php echo M('Password'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'password',
	/* attributes */
		array(
			'id'		=> 'password',
			'attr'		=> array(
				'size'	=> 24,
				),
			)
		);
	?>
	</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>
	<DIV CLASS="buttonBar">
	<?php echo $this->makePostParams('-current-', 'login' ); ?>
	<INPUT TYPE="submit" VALUE="<?php echo M('Login'); ?>">
	</DIV>
</td>
</tr>
</table>
<a href="<?php echo ntsLink::makeLink('anon/forgot_password' ); ?>"><?php echo M('Forgot Your Password'); ?>?</a> 
</div>

<?php if( defined('NTS_SKIP_COOKIE') && NTS_SKIP_COOKIE ) : ?>
	<input type="hidden" name="nts-skip-cookie" value="1">
<?php else : ?>
	<div id="ntsCookieAlert" style="display: none;">
	<b class="alert">Your browser's cookie functionality is turned off. Please turn it on.</b>
	<b>[<a href="http://www.google.com/support/accounts/bin/answer.py?answer=61416" target="_blank">?</a>]</b>
	</div>
	<script language="JavaScript" src="<?php echo ntsLink::makeLink('system/pull', '', array('what' => 'js', 'files' => 'loginCookie.js') ); ?>"></script>
<?php endif; ?>