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

/* remove the current user as the admin in hitAppoint */

/* remove remote integration */
$conf =& ntsConf::getInstance();
$ntsdb =& dbWrapper::getInstance();
$newValue = $conf->set('remoteIntegration', '' );
$sql = $conf->getSaveSql( 'remoteIntegration', $newValue );
$result = $ntsdb->runQuery( $sql );
?>