<?php
$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();

switch( $action ){
	case 'update':
		$ff =& ntsFormFactory::getInstance();

		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate($req) ){
			$formValues = $form->getValues();

			$newValues = array(
				'smtpSecure'		=> $formValues['smtp-secure'],
				'smtpHost'			=> $formValues['smtp-host'],
				'smtpUser'			=> $formValues['smtp-user'],
				'smtpPass'			=> $formValues['smtp-pass'],
				'emailSentFrom'		=> $formValues['email-sent-from'],
				'emailSentFromName'	=> $formValues['email-sent-from-name'],
				'emailDebug'		=> $formValues['email-debug'],
				'emailDisabled'		=> $formValues['email-disabled'],
				'emailCommonHeader'	=> $formValues['email-header'],
				'emailCommonFooter'	=> $formValues['email-footer'],
				);

			reset( $newValues );
			foreach( $newValues as $k => $v ){
				$newValue = $conf->set( $k, $v );
				$sql = $conf->getSaveSql( $k, $newValue );
				$result = $ntsdb->runQuery( $sql );
				}

			if( $result ){
				ntsView::setAnnounce( M('Settings') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to delivery options form */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				echo '<BR>Database error:<BR>' . $ntsdb->getError() . '<BR>';
				}
			}
		else {
		/* form not valid, continue to create form */
			}

		break;
	default:
		$smtpSecure = $conf->get('smtpSecure');
		$smtpHost = $conf->get('smtpHost');
		$smtpUser = $conf->get('smtpUser');
		$smtpPass = $conf->get('smtpPass');
		$sentFrom = $conf->get('emailSentFrom');
		$sentFromName = $conf->get('emailSentFromName');
		$commonHeader = $conf->get('emailCommonHeader');
		$commonFooter = $conf->get('emailCommonFooter');
		$debug = $conf->get('emailDebug');
		$disabled = $conf->get('emailDisabled');

		$default = array(
			'smtp-secure'		=> $smtpSecure,
			'smtp-host'			=> $smtpHost,
			'smtp-user'			=> $smtpUser,
			'smtp-pass'			=> $smtpPass,
			'email-sent-from'		=> $sentFrom,
			'email-sent-from-name'	=> $sentFromName,
			'email-debug'			=> $debug,
			'email-header'			=> $commonHeader,
			'email-footer'			=> $commonFooter,
			'email-disabled'		=> $disabled,
			);

		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, $default );
		break;
	}
?>