<?php
global $NTS_CURRENT_USER;
?>
<TABLE>
<tr>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'enableRegistration',
			)
		);
	?>
	</td>
	<th><?php echo M('Enable Registration'); ?>?</th>
</tr>

<tr>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'userAdminApproval',
			)
		);
	?>
	</TD>
	<th><?php echo M('Approval Required'); ?></th>
</TR>
<tr>
	<td></td>
	<td>
	<i><?php echo M('The administrator will need to manually approve new user accounts before they can access the system'); ?></i>
	</td>
</tr>

<tr>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'userLoginRequired',
			)
		);
	?>
	</td>
	<th><?php echo M('User Login Required?'); ?></th>
</tr>

<tr>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'userEmailConfirmation',
			)
		);
	?>
	</TD>
	<th><?php echo M('Email Confirmation Required'); ?>?</th>
</TR>
<tr>
	<td></td>
	<td>
	<i><?php echo M('Newly registered users will be asked to confirm their email by following a confirmation link in the email message'); ?></i>
	</td>
</tr>

<tr>
	<td>
	<?php
	$thisFieldReadonly = false;
	if( defined('NTS_REMOTE_INTEGRATION') )
		$thisFieldReadonly = true;
	$attr = array(
		'id'		=> 'emailAsUsername'
		);
	if( $thisFieldReadonly ){
		$attr['readonly'] = 1;
		$attr['value'] = 0;
		}
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		$attr
		);
	?>
	</td>
	<th><?php echo M('Use Email As Username'); ?></th>
</tr>
<tr>
	<td></td>
	<td>
	<i><?php echo M('When enabling this, be sure you remember your own email address'); ?>:</i> <b><?php echo $NTS_CURRENT_USER->getProp('email'); ?></b>
	</td>
</tr>

<tr>
	<td>
	<?php
	$thisFieldReadonly = false;
	if( defined('NTS_REMOTE_INTEGRATION') )
		$thisFieldReadonly = true;
	$attr = array(
		'id'		=> 'allowDuplicateEmails'
		);
	if( $thisFieldReadonly ){
		$attr['readonly'] = 1;
		$attr['value'] = 0;
		}
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		$attr
		);
	?>
	</td>
	<th><?php echo M('Allow Duplicate Emails'); ?></th>
</tr>

<tr>
	<td>
	<?php
	$thisFieldReadonly = false;
	$attr = array(
		'id'		=> 'allowNoEmail'
		);
	if( $thisFieldReadonly ){
		$attr['readonly'] = 1;
		$attr['value'] = 0;
		}
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		$attr
		);
	?>
	</td>
	<th><?php echo M('Allow No Email'); ?></th>
</tr>

<tr>
<td colspan="2">
<p>
<b><?php echo M('Timezone'); ?></b>: &nbsp;
<?php
$tzOptions = array(
	array( 1, M('Allow To Set Own Timezone') ),
	array( 0, M('Only View The Timezone') ),
	array( -1, M('Do Not Show The Timezone') ),
	);
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'	=> 'enableTimezones',
		'options'	=> $tzOptions,
		)
	);
?>
</td>
</tr>

<tr>
<td colspan="2">
<b><?php echo M('First Time Visitors Splash Screen'); ?></b>
</td>
</tr>

<tr>
<td colspan="2">
<?php
echo $this->makeInput (
/* type */
	'textarea',
/* attributes */
	array(
		'id'	=> 'firstTimeSplash',
		'attr'	=> array(
			'cols'	=> 48,
			'rows'	=> 6,
			),
		)
	);
?>
</td>
</tr>

</TABLE>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update'); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Save'); ?>">
</DIV>

<SCRIPT LANGUAGE="JavaScript">
var emailAsUsernameCtl = "#<?php echo $this->getName(); ?>-emailAsUsername";
var userEmailConfirmationCtl = "#<?php echo $this->getName(); ?>-userEmailConfirmation";
var allowNoEmailCtl = "#<?php echo $this->getName(); ?>-allowNoEmail";
var allowDuplicateEmailsCtl = "#<?php echo $this->getName(); ?>-allowDuplicateEmails";

function ntsProcessInputs_1(){
	if ( 
		( jQuery(emailAsUsernameCtl).attr("checked") == true ) ||
		( jQuery(userEmailConfirmationCtl).attr("checked") == true )
		){
		jQuery(allowNoEmailCtl).attr('checked', false);
		jQuery(allowNoEmailCtl).attr('disabled', true);
		jQuery(allowDuplicateEmailsCtl).attr('checked', false);
		jQuery(allowDuplicateEmailsCtl).attr('disabled', true);
		}
	else {
		jQuery(allowNoEmailCtl).removeAttr('disabled');
		jQuery(allowDuplicateEmailsCtl).removeAttr('disabled');
		}
	}

function ntsProcessInputs_2(){
	if ( jQuery(allowNoEmailCtl).attr("checked") == true ){
		jQuery(emailAsUsernameCtl).attr('checked', false);
		jQuery(emailAsUsernameCtl).attr('disabled', true);
		jQuery(userEmailConfirmationCtl).attr('checked', false);
		jQuery(userEmailConfirmationCtl).attr('disabled', true);
		}
	else {
		jQuery(emailAsUsernameCtl).removeAttr('disabled');
		jQuery(userEmailConfirmationCtl).removeAttr('disabled');
		}
	}

function ntsProcessInputs_3(){
	if ( jQuery(allowDuplicateEmailsCtl).attr("checked") == true ){
		jQuery(emailAsUsernameCtl).attr('checked', false);
		jQuery(emailAsUsernameCtl).attr('disabled', true);
		}
	else {
		jQuery(emailAsUsernameCtl).removeAttr('disabled');
		}
	}

jQuery(emailAsUsernameCtl).bind( "click", ntsProcessInputs_1 );
jQuery(userEmailConfirmationCtl).bind( "click", ntsProcessInputs_1 );
jQuery(allowNoEmailCtl).bind( "click", ntsProcessInputs_2 );
jQuery(allowDuplicateEmailsCtl).bind( "click", ntsProcessInputs_3 );

ntsProcessInputs_1();
ntsProcessInputs_2();
</script>