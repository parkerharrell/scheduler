<?php
/********************************************************************
Author  :  	hitAppoint
Date    :	February 2010
Version :	4.0.0
Contact :	support@hitappoint.com
Description:  Displays hitAppoint
*********************************************************************
This file is part of hitApppoint Module
*********************************************************************/
defined('_JEXEC') or die('Restricted access');
include_once( dirname(__FILE__) . '/../../../components/com_hitappoint/_conf.php' );

$currentUser =& JFactory::getUser();
$currentUserId = $currentUser->id;

$file = HITAPPOINT_PATH . '/core/model/init.php';
include_once( $file );

$object = new ntsUser();
$object->setId( $currentUserId, false );
$object->setProp( '_role', array('admin')  );

$cm =& ntsCommandManager::getInstance();

/* assign all rights for this user */
$resourceSchedules = array();
$resourceApps = array();

$resources = ntsObjectFactory::getAll( 'resource' );
reset( $resources );
foreach( $resources as $r ){
	$rid = $r->getId();
	$resourceSchedules[ $rid ] = 'edit';
	$resourceApps[ $rid ] = 'manage';
	}

// assign to current user only
$object->setProp( '_resource_schedules', $resourceSchedules );
$object->setProp( '_resource_apps', $resourceApps );
$cm->runCommand( $object, 'update' );

// assign to all admins
/*
$db =& JFactory::getDBO();
$query = "SELECT id FROM #__users WHERE usertype='Super Administrator' or usertype='Administrator'";
$db->setQuery($query);
$column= $db->loadResultArray(0);
reset( $column );
foreach( $column as $userId ){
	$object = new ntsUser();
	$object->setId( $userId, false );
	$object->setProp( '_resource_schedules', $resourceSchedules );
	$object->setProp( '_resource_apps', $resourceApps );
	$cm->runCommand( $object, 'update' );
	}
*/
?>