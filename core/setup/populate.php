<?php
$cm =& ntsCommandManager::getInstance();

global $NTS_SETUP_ADMINS;
/* admin */
if( ! $NTS_SETUP_ADMINS ){
	$adminFname = $_POST['admin_fname'];
	$adminLname = $_POST['admin_lname'];
	$adminUsername = $_POST['admin_username'];
	$adminEmail = $_POST['admin_email'];
	$adminPassword = $_POST['admin_pass'];

	$admin = new ntsUser();
	$admin->setProp( 'username', $adminUsername );
	$admin->setProp( 'password', $adminPassword );
	$admin->setProp( 'email', $adminEmail );
	$admin->setProp( 'first_name', $adminFname );
	$admin->setProp( 'last_name', $adminLname );

	$cm->runCommand( $admin, 'create' );
	$adminId = $admin->getId();
	if( ! $cm->isOk() ){
		echo '<BR>Command error:<BR>' . $cm->printActionErrors() . '<BR>';
		exit;
		}
	$NTS_SETUP_ADMINS = array( $adminId );
	}

reset( $NTS_SETUP_ADMINS );
foreach( $NTS_SETUP_ADMINS as $admId ){
	$admin = new ntsUser;
	$admin->setId( $admId );
	$admin->setProp( '_role', array('admin') );
	$cm->runCommand( $admin, 'update' );
	}

/* email sent from */
$setting = $admin->getProp( 'email' );
$newValue = $conf->set( 'emailSentFrom', $setting );
$sql = $conf->getSaveSql( 'emailSentFrom', $newValue );
$result = $ntsdb->runQuery( $sql );

/* email sent from name */
$setting = $admin->getProp( 'first_name' ) .  ' ' . $admin->getProp( 'last_name' );
$newValue = $conf->set( 'emailSentFromName', $setting );
$sql = $conf->getSaveSql( 'emailSentFromName', $newValue );
$result = $ntsdb->runQuery( $sql );

/* DEFAULT FORMS - CUSTOMER */
$form = new ntsObject( 'form' );
$form->setProp( 'title', 'Customer Form' );
$form->setProp( 'class', 'customer' );
$form->setProp( 'details', '' );

$cm->runCommand( $form, 'create' );
if( ! $cm->isOk() ){
	echo '<BR>Command error:<BR>' . $cm->printActionErrors() . '<BR>';
	exit;
	}
// FORM CONTROLS
$order = 0;
$controls = array(
	array( 'username',		'Username', 	'text',	array('size' => 24), array( array('code' => 'notEmpty', 'error' => M('Required field')), array('code' => 'checkUsername', 'error' => 'This username is already in use', 'params' => array('skipMe'	=> 1) ) ) ),
	array( 'email',			'Email',		'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Required field'), array('code' => 'checkUserEmail', 'error' => 'This email is already in use', 'params' => array('skipMe'	=> 1) ) ) ),
	array( 'first_name',	'First Name',	'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Please enter the first name') ) ),
	array( 'last_name',		'Last Name',	'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Please enter the last name') ) ),
	);
reset( $controls );
$formId = $form->getId();
foreach( $controls as $c ){
	$order++;
	$object = new ntsObject( 'form_control' );
	$object->setProp( 'form_id', $formId );
	$object->setProp( 'ext_access', 'write' );
	$object->setProp( 'class', 'customer' );

	$object->setProp( 'name', $c[0] );
	$object->setProp( 'title', $c[1] );
	$object->setProp( 'type', $c[2] );
	$object->setProp( 'attr', $c[3] );
	$object->setProp( 'validators', $c[4] );
	$object->setProp( 'show_order', $order );
	$cm->runCommand( $object, 'create' );
	}

// payment gateways
$setting = array('offline');
$newValue = $conf->set( 'paymentGateways', $setting );
$sql = $conf->getSaveSql( 'paymentGateways', $newValue );
$result = $ntsdb->runQuery( $sql );

// SAVE THE INSTALLED VERSION
$newValue = $conf->set('currentVersion', NTS_APP_VERSION );
$sql = $conf->getSaveSql( 'currentVersion', $newValue );
$result = $ntsdb->runQuery( $sql );

$now = time();
$newValue = $conf->set('backupLastRun', $now );
$sql = $conf->getSaveSql( 'backupLastRun', $newValue );
$result = $ntsdb->runQuery( $sql );

/* create services */
$services = array();
$sessions = array();
$titles = array('My Service');
foreach( $titles as $t ){
	$object = new ntsObject( 'service' );
	$object->setByArray( array(
		'title'			=> $t,
		'description'	=> 'Description of ' . $t,
		'min_from_now'	=> 3 * 60 * 60,
		'max_from_now'	=> 8 * 7 * 24 * 60 * 60,
		'min_cancel'	=> 1 * 24 * 60 * 60,
		'allow_queue'	=> 0,
		'duration'	 	=> 30 * 60,
		'lead_in'		=> 0,
		'lead_out'		=> 0,
		'pack_only'		=> 0,
		'class_type'	=> 0,
		'price'			=> 25,
		)
		);
	$cm->runCommand( $object, 'create' );
	$serviceId = $object->getId();
	$services[] = $object;
	}

/* create resources */
// resources terminology
if( isset($_POST['resname_sing']) || isset($_POST['resname_plu']) ){
	$resnameSing = $_POST['resname_sing'];
	$resnamePlu = $_POST['resname_plu'];

	$newValue = $conf->set( 'text-Bookable Resource', $resnameSing );
	$sql = $conf->getSaveSql( 'text-Bookable Resource', $newValue );
	$result = $ntsdb->runQuery( $sql );

	$newValue = $conf->set( 'text-Bookable Resources', $resnamePlu );
	$sql = $conf->getSaveSql( 'text-Bookable Resources', $newValue );
	$result = $ntsdb->runQuery( $sql );
	}
else {
	$resnameSing = 'Bookable Resource';
	$resnamePlu = 'Bookable Resources';
	}

$resourceSchedules = array();
$resourceApps = array();

$resources = array();
$titles = array( 'My ' . $resnameSing );
foreach( $titles as $t ){
	$object = ntsObjectFactory::get( 'resource' );
	$object->setByArray( array(
		'title'			=> $t,
		'description'	=> 'Description of ' . $t,
		)
		);
	$cm->runCommand( $object, 'create' );
	$newObjId = $object->getId();
	$resources[] = $object;

/* assign admin to manage this resource */
	$resourceSchedules[ $newObjId ] = 'edit';
	$resourceApps[ $newObjId ] = 'manage';
	}

/* create locations */
$locations = array();
$titles = array('My Location');
foreach( $titles as $t ){
	$object = new ntsObject( 'location' );
	$object->setByArray( array(
		'title'			=> $t,
		'description'	=> 'Description of ' . $t,
		)
		);
	$cm->runCommand( $object, 'create' );
	$locations[] = $object;
	}

/* schedules */
$t = new ntsTime;
$startSchedule = $t->formatDate_Db();
list( $year, $month, $day ) = ntsTime::splitDate( $startSchedule );

$t->setDateTime( $year, $month + 3, $day, 0, 0, 0 );
$endSchedule = $t->formatDate_Db();

$object = new ntsObject( 'schedule' );
$object->setByArray( array(
	'resource_id'	=> $resources[0]->getId(),
	'title'			=> 'My Schedule',
	'valid_from'	=> $startSchedule,
	'valid_to'		=> $endSchedule,
	'capacity'		=> 1,
	'_service'		=> array( $services[0]->getId() ),
	'_location'		=> array( $locations[0]->getId() ),
	)
	);
$cm->runCommand( $object, 'create' );
$scheduleId = $object->getId();

/* timeblocks */
$selectable = array( 5*60, 10*60, 15*60, 20*60, 30*60, 60*60 );
for( $i = 1; $i <= 5; $i++ ){
	$object = new ntsObject( 'timeblock' );
	$object->setByArray( array(
		'schedule_id'		=> $scheduleId,
		'starts_at'			=> 9 * 60 * 60,
		'ends_at'			=> 13 * 60 * 60,
		'applied_on'		=> $i,
		'selectable_every'	=> 15*60,
		)
		);
	$cm->runCommand( $object, 'create' );

	$object = new ntsObject( 'timeblock' );
	$object->setByArray( array(
		'schedule_id'		=> $scheduleId,
		'starts_at'			=> 14 * 60 * 60,
		'ends_at'			=> 18 * 60 * 60,
		'applied_on'		=> $i,
		'selectable_every'	=> 15*60,
		)
		);
	$cm->runCommand( $object, 'create' );
	}

/* assign admin to manage this resource */
$admin->setProp( '_resource_schedules', $resourceSchedules );
$admin->setProp( '_resource_apps', $resourceApps );
$cm->runCommand( $admin, 'update' );
?>