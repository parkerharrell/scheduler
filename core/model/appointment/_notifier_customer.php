<?php
/* --- RETURN IF EMAIL DISABLED --- */
$conf =& ntsConf::getInstance();
if( $conf->get('emailDisabled') )
	return;

$customerId = $object->getProp( 'customer_id' );
$customer = new ntsUser();
$customer->setId( $customerId );

/* --- SEND MESSAGE IF EMAIL DEFINED --- */
$userEmail = trim( $customer->getProp('email') );
if( ! $userEmail )
	return;

$userLang = $customer->getProp('lang');
if( ! $userLang )
	$userLang = $defaultLanguage;

/* --- GET TEMPLATE --- */
$key = 'appointment-' . $mainActionName . '-customer';

/* --- SKIP IF THIS NOTIFICATION DISABLED --- */
$currentlyDisabled = $conf->get( 'disabledNotifications' );
if( in_array($key, $currentlyDisabled) ){
	return;
	}

$templateInfo = $etm->getTemplate( $userLang, $key );

/* --- SKIP IF NO TEMPLATE --- */
if( ! $templateInfo ){
	return;
	}

$tags = $om->makeTags_Appointment( $object, 'external' );

if( ! isset($params['reason']) )
	$params['reason'] = '';

$tags[0][] = '{REJECT_REASON}';
$tags[1][] = $params['reason'];
$tags[0][] = '{CANCEL_REASON}';
$tags[1][] = $params['reason'];

$tags[0][] = '{APPOINTMENT.REJECT_REASON}';
$tags[1][] = $params['reason'];
$tags[0][] = '{APPOINTMENT.CANCEL_REASON}';
$tags[1][] = $params['reason'];

if( $mainActionName == 'reschedule' ){
	$oldts = $params['oldStartsAt'];
	$t = new ntsTime( $oldts, $customer->getProp('_timezone') );
	$timeFormatted = $t->formatWeekdayShort() . ', ' . $t->formatDate() . ' ' . $t->formatTime();
	$tags[0][] = '{OLD_APPOINTMENT.STARTS_AT}';
	$tags[1][] = $timeFormatted;
	}

if( $mainActionName == 'invite' ){
	$tags[0][] = '{OTHER.EMAIL}';
	$tags[1][] = $params['otherEmail'];
	}

/* add .ics attachement */
$attachements = array();
if( in_array($key, $attachTo) ){
	include_once( NTS_APP_DIR . '/helpers/ical.php' );
	$ntsCal = new ntsIcal();
	$ntsCal->setTimezone( $customer->getTimezone() );
	$ntsCal->addAppointment( $object );
	$str = $ntsCal->printOut();

	$attachName = 'appointment-' . $object->getId() . '.ics';
	$attachements[] = array( $attachName, $str );

	$tags[0][] = '{APPOINTMENT.LINK_TO_ICAL}';
	$tags[1][] = 'cid:' . $attachName;
	}
else {
	$tags[0][] = '{APPOINTMENT.LINK_TO_ICAL}';
	$tags[1][] = '';
	}

/* replace tags */
$subject = str_replace( $tags[0], $tags[1], $templateInfo['subject'] );
$body = str_replace( $tags[0], $tags[1], $templateInfo['body'] );

/* --- SEND EMAIL --- */
$this->runCommand( $customer, 'email', array('body' => $body, 'subject' => $subject, 'attachements' => $attachements) );
?>