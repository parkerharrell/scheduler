<?php
/* this is a not an actual command, it checks the customer and sees what's next */

// service
$service = ntsObjectFactory::get( 'service' ); 
$service->setId( $object->getProp( 'service_id' ) );

// customer groups
$myGroupsIds = array();

$customerId = $object->getProp( 'customer_id' );
$customer = new ntsUser();
$customer->setId( $customerId );
$restrictions = $customer->getProp('_restriction');

if( $restrictions ){
	$myGroupsIds[] = -1;
	}
else {
	$myGroupsIds[] = 0;
	}

$approvalRequired = true;
reset( $myGroupsIds );
foreach( $myGroupsIds as $groupId ){
	$permission = $service->getPermissionsForGroup( $groupId );
	if( $permission == 'auto_confirm' ){
		$approvalRequired = false;
		break;
		}
	}

if( $approvalRequired ){
	$this->runCommand( $object, 'require_approval' );
	}
else {
	$this->runCommand( $object, 'request' );
	}
?>