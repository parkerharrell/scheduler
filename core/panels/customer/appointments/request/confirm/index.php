<?php
global $NTS_CURRENT_REQUEST;
$conf = ntsConf::getInstance();

$ff =& ntsFormFactory::getInstance();

$formParams = array();
if( $NTS_VIEW['RESCHEDULE'] ){
	$formParams = $NTS_VIEW['RESCHEDULE']->getByArray();
	}

$allServiceIds = array();
$formParams['services'] = array();
$reqCount = count($NTS_CURRENT_REQUEST); 
for( $i = 0; $i < $reqCount; $i++ ){
	$thisServiceId = $NTS_CURRENT_REQUEST[$i]['service']->getId();
	if( ! in_array($thisServiceId, $allServiceIds) ){
		$formParams['services'][] = $NTS_CURRENT_REQUEST[$i]['service'];
		$allServiceIds[] = $NTS_CURRENT_REQUEST[$i]['service']->getId();
		}
	}
	
$form =& $ff->makeForm( dirname(__FILE__) . '/form', $formParams );
$totalPrice = 0;

$showResource = ( (! NTS_SINGLE_RESOURCE) ) ? true : false;
$showLocation = ( (! NTS_SINGLE_LOCATION) ) ? true : false;
$showSessionDuration = $conf->get('showSessionDuration');

if( $NTS_VIEW['RESCHEDULE'] )
	$showPrice = strlen(ntsCurrency::formatServicePrice($NTS_VIEW['RESCHEDULE']->getProp('price'))) ? true : false;
else
	$showPrice = strlen(ntsCurrency::formatServicePrice($NTS_CURRENT_REQUEST[0]['service']->getProp('price'))) ? true : false;
?>

<!-- APPOINTMENT REQUEST FLOW -->
<?php require( dirname(__FILE__) . '/../common/flow.php' ); ?>

<p>
<?php $form->display(); ?>
