<?php
$userMobile = trim( $object->getProp('mobile_phone') );
if( ! $userMobile )
	return;

include_once( dirname(__FILE__) . '/../../lib/ntsSms.php' );
$body = $params['body'];

/* --- ADD RECIPIENT TAGS --- */
$om =& objectMapper::getInstance();
$fields = $om->getFields( 'user' );
$tags = array( array(), array() );

$allInfo = '';
foreach( $fields as $f ){
	$value = $object->getProp( $f[0] );
	if( isset($f[2]) && $f[2] == 'checkbox' ){
		$value = $value ? M('Yes') : M('No');
		}

	$tags[0][] = '{RECIPIENT.' . strtoupper($f[0]) . '}';
	$tags[1][] = $value;

/* build the -ALL- tag */
	$allInfo .= M($f[1]) . ': ' . $value . "\n";
	}
$tags[0][] = '{RECIPIENT.-ALL-}';
$tags[1][] = $allInfo;

/* --- PARSE RECIPIENT TAGS --- */
$body = str_replace( $tags[0], $tags[1], $body );

/* --- FINALLY SEND SMS --- */
$mailer = new ntsSms;
$mailer->setBody( $body );

$mailer->sendToOne( $userMobile );
if( $mailer->isError() ){
	$mailerError = $mailer->getError();
	ntsView::setAnnounce( 'SMS sending error, see log for more info', 'error' );
	}

/* --- CC TO ADMIN --- */
$ccToAdmin = $object->getProp( '_cc_admin' );
if( $ccToAdmin ){
	$uif =& ntsUserIntegratorFactory::getInstance();
	$integrator =& $uif->getIntegrator();

	$ccTo = array();
	/* --- FIND ADMINS --- */
	$admins = $integrator->getUsers( array('_role' => ' IN ("admin", "manager")') );
	if( $admins ){
		reset( $admins );
		foreach( $admins as $adminInfo ){
			$adminMobile = trim( $adminInfo['mobile_phone'] );
			if( ! $adminMobile )
				continue;
			if( $adminMobile == $userMobile )
				continue;
			$ccTo[] = $adminMobile;
			}
		}
	reset( $ccTo );
	foreach( $ccTo as $cc ){
		$mailer->sendToOne( $cc );
		if( $mailer->isError() ){
			$mailerError = $mailer->getError();
			ntsView::setAnnounce( 'SMS Error: ' . $mailerError, 'error' );
			}
		}
	}
?>