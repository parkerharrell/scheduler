<?php
$matrix = array(
'appointments'	=> array(
	'customer'	=> array(
		array( 'appointment-require_approval-customer',	M('Appointment') . ': ' . M('Approval Required') ),
		array( 'appointment-approve-customer',			M('Appointment') . ': ' . M('Approved') ),
		array( 'appointment-request-customer',			M('Appointment') . ': ' . M('Request (Automatically Approved)') ),
		array( 'appointment-reject-customer', 			M('Appointment') . ': ' . M('Reject') . ': ' . M('OK') ),
		array( 'appointment-cancel-customer',			M('Appointment') . ': ' . M('Cancelled') ),
		array( 'appointment-change-customer',			M('Appointment') . ': ' . M('Change') . ': ' . M('OK') ),
		array( 'appointment-noshow-customer',			M('Appointment') . ': ' . M('No Show') ),
		array( 'appointment-remind-customer',			M('Appointment') . ': ' . M('Reminder') ),
		),
	'provider'	=> array(
		array( 'appointment-require_approval-provider',	M('Appointment') . ': ' . M('Approval Required') ),
		array( 'appointment-approve-provider',			M('Appointment') . ': ' . M('Approved') ),
		array( 'appointment-request-provider',			M('Appointment') . ': ' . M('Request (Automatically Approved)') ),
		array( 'appointment-reject-provider', 			M('Appointment') . ': ' . M('Reject') . ': ' . M('OK') ),
		array( 'appointment-cancel-provider',			M('Appointment') . ': ' . M('Cancelled') ),
		array( 'appointment-change-provider',			M('Appointment') . ': ' . M('Change') . ': ' . M('OK') ),
		array( 'appointment-noshow-provider',			M('Appointment') . ': ' . M('No Show') ),
		array( 'appointment-remind-provider',			M('Appointment') . ': ' . M('Reminder') ),
		),
	),
'user'	=> array(
	'user'	=> array(
		array( 'user-require_email_confirmation-user',	M('Email Confirmation Required') ),
		array( 'user-require_approval-user',			M('Waiting For Approval') ),
		array( 'user-activate-user',					M('User') . ': ' . M('Activate') . ': ' . M('OK') ),
		array( 'user-reset_password-user', 				M('Password Reset') ),
		),
	'admin'	=> array(
		array( 'user-require_approval-admin',	M('Approval Required') ),
		array( 'user-activate-admin',			M('User') . ': ' . M('Activate') . ': ' . M('OK') ),
		),
	),
);
?>