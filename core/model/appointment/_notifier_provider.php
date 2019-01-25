<?php
/* --- RETURN IF EMAIL DISABLED --- */
$conf =& ntsConf::getInstance();
if( $conf->get('emailDisabled') )
	return;

/* --- GET TEMPLATE --- */
$key = 'appointment-' . $mainActionName . '-provider';

/* --- SKIP IF THIS NOTIFICATION DISABLED --- */
$currentlyDisabled = $conf->get( 'disabledNotifications' );
if( in_array($key, $currentlyDisabled) ){
	return;
	}

/* --- SKIP IF NO TEMPLATE --- */
$userLang = $defaultLanguage;
$templateInfo = $etm->getTemplate( $userLang, $key );
if( ! $templateInfo ){
	return;
	}

/* find staff who manages this resource */
$resourceId = $object->getProp( 'resource_id' );
$resource = ntsObjectFactory::get( 'resource' );
$resource->setId( $resourceId );

list( $appsAdmins, $scheduleAdmins ) = $resource->getAdmins( true );
$providers = array();
$adminsIds = array_keys( $appsAdmins );
reset( $adminsIds );
foreach( $adminsIds as $admId ){
	$provider = new ntsUser;
	$provider->setId( $admId );
	$providers[] = $provider;
	}

/* parse templates */
$tags = $om->makeTags_Appointment( $object, 'internal' );
if( isset($params['reason']) ){
	$tags[0][] = '{REJECT_REASON}';
	$tags[1][] = $params['reason'];
	$tags[0][] = '{CANCEL_REASON}';
	$tags[1][] = $params['reason'];

	$tags[0][] = '{APPOINTMENT.REJECT_REASON}';
	$tags[1][] = $params['reason'];
	$tags[0][] = '{APPOINTMENT.CANCEL_REASON}';
	$tags[1][] = $params['reason'];
	}

if( $mainActionName == 'change' ){
	$oldts = $params['oldStartsAt'];
	$t = new ntsTime( $oldts );
	$timeFormatted = $t->formatWeekdayShort() . ', ' . $t->formatDate() . ' ' . $t->formatTime();
	$tags[0][] = '{OLD_APPOINTMENT.STARTS_AT}';
	$tags[1][] = $timeFormatted;
	}

/* quick links */
$authCode = $object->getProp( 'auth_code' );
$approveLink = ntsLink::makeLink( 'system/appointments/edit', 'approve', array('auth' => $authCode, 'id' => $object->getId()) );
$approveLink = '<a href="' . $approveLink . '">' . M('Approve') . '</a>';
$rejectLink = ntsLink::makeLink( 'system/appointments/edit', 'reject', array('auth' => $authCode, 'id' => $object->getId()) );
$rejectLink = '<a href="' . $rejectLink . '">' . M('Reject') . '</a>';

$tags[0][] = '{APPOINTMENT.QUICK_LINK_APPROVE}';
$tags[1][] = $approveLink;
$tags[0][] = '{APPOINTMENT.QUICK_LINK_REJECT}';
$tags[1][] = $rejectLink;

/* add .ics attachement */
$attachements = array();
if( in_array($key, $attachTo) ){
	include_once( NTS_APP_DIR . '/helpers/ical.php' );
	$ntsCal = new ntsIcal();
	$ntsCal->setTimezone( NTS_COMPANY_TIMEZONE );
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
reset( $providers );
foreach( $providers as $provider ){
	$this->runCommand( $provider, 'email', array('body' => $body, 'subject' => $subject, 'attachements' => $attachements) );
	}
?>