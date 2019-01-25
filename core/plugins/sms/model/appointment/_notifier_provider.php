<?php
$ntsConf =& ntsConf::getInstance();

/* --- GET TEMPLATE --- */
$key = 'appointment-' . $mainActionName . '-provider';

/* --- SKIP IF THIS NOTIFICATION DISABLED --- */
$currentlyDisabled = $plm->getPluginSetting($plugin, 'disabledNotifications');
if( ! $currentlyDisabled )
	$currentlyDisabled = array();
if( ! is_array($currentlyDisabled) )
	$currentlyDisabled = array( $currentlyDisabled );

if( in_array($key, $currentlyDisabled) ){
	return;
	}

/* --- SKIP IF NO TEMPLATE --- */
$userLang = $defaultLanguage;
$templateInfo = $stm->getTemplate( $userLang, $key );
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

$tags[0][] = '{APPOINTMENT.LINK_TO_ICAL}';
$tags[1][] = '';

/* replace tags */
$body = str_replace( $tags[0], $tags[1], $templateInfo['body'] );
$body = trim( $body );

/* --- SEND SMS --- */
reset( $providers );
foreach( $providers as $provider ){
	$userMobile = trim( $provider->getProp('mobile_phone') );
	if( $userMobile )
		$this->runCommand( $provider, 'sms', array('body' => $body) );
	}
?>