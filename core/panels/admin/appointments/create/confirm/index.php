<!-- APPOINTMENT REQUEST FLOW -->
<?php require( dirname(__FILE__) . '/../common/flow.php' ); ?>

<?php
$ff =& ntsFormFactory::getInstance();
$formParams = array();
if( $NTS_VIEW['RESCHEDULE'] ){
	$object = $NTS_VIEW['RESCHEDULE']['obj'];
	$formParams = $object->getByArray();
	}

$formParams['service_id'] = $service->getId();

$form =& $ff->makeForm( dirname(__FILE__) . '/form', $formParams );
$form->display();
?>