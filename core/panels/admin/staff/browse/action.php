<?php
$ntsdb =& dbWrapper::getInstance();

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

/* load users */
$where = array( '_role' => "='admin'" );

if( ! NTS_EMAIL_AS_USERNAME ){
	$order = array(
		array( 'username', 'ASC' ),
		);
	}
else {
	$order = array(
		array( 'email', 'ASC' ),
		);
	}

$users = $integrator->getUsers(
	$where,
	$order
	);

$NTS_VIEW['entries'] = array();
reset( $users );
foreach( $users as $u ){
	$user = new ntsUser();
	$user->setId( $u['id'] );
	$NTS_VIEW['entries'][] = $user;
	}
?>