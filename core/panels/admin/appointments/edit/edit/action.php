<?php
$cm =& ntsCommandManager::getInstance();
$ff =& ntsFormFactory::getInstance();

$id = $req->getParam( '_id' );
$NTS_VIEW['id'] = $id;

$object = ntsObjectFactory::get( 'appointment' );
$object->setId( $id );
$NTS_VIEW['object'] = $object;

$service = ntsObjectFactory::get( 'service' );
$service->setId( $object->getProp('service_id') );
$NTS_VIEW['service'] = $service;

$location = new ntsObject( 'location' );
$location->setId( $object->getProp('location_id') );
$NTS_VIEW['location'] = $location;

$resource = ntsObjectFactory::get( 'resource' );
$resource->setId( $object->getProp('resource_id') );
$NTS_VIEW['resource'] = $resource;

$customer = new ntsUser();
$customer->setId( $object->getProp('customer_id') );
$NTS_VIEW['customer'] = $customer;
?>