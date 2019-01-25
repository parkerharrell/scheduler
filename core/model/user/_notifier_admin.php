<?php
/* --- RETURN IF EMAIL DISABLED --- */
$conf =& ntsConf::getInstance();
if( $conf->get('emailDisabled') )
	return;

/* --- FIND ADMINS --- */
$admins = $integrator->getUsers( array('_role' => ' IN ("admin", "manager")') );
if( ! $admins )
	return;

$userLang = $object->getProp('lang');
if( ! $userLang )
	$userLang = $defaultLanguage;

/* --- GET TEMPLATE --- */
$key = 'user-' . $mainActionName . '-admin';

/* --- SKIP IF THIS NOTIFICATION DISABLED --- */
$currentlyDisabled = $conf->get( 'disabledNotifications' );
if( in_array($key, $currentlyDisabled) ){
	return;
	}

$templateInfo = $etm->getTemplate( $userLang, $key );

/* --- SKIP IF NO TEMPLATE --- */
if( ! $templateInfo )
	return;

/* --- PREPARE MESSAGE --- */
/* build tags */
$tags = $om->makeTags_Customer( $object, 'internal' );

/* replace tags */
$subject = str_replace( $tags[0], $tags[1], $templateInfo['subject'] );
$body = str_replace( $tags[0], $tags[1], $templateInfo['body'] );

/* --- SEND EMAIL --- */
reset( $admins );
foreach( $admins as $adminInfo ){
	$adminEmail = trim( $adminInfo['email'] );
	if( ! $adminEmail )
		return;
	$admin = new ntsUser();
	$admin->setByArray( $adminInfo );

/* check if admin has disabled access to customers panel */
	$disabledPanels = $admin->getProp('_disabled_panels');
	if(
		in_array('admin/customers/edit', $disabledPanels )
		){
		continue;
		}

	$this->runCommand( $admin, 'email', array('body' => $body, 'subject' => $subject) );	
	}
?>