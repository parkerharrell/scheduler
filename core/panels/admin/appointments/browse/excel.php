<?php
$fields = array(
	array( 'status', M('Status') ),
	array( 'starts_at', M('When') ),
	array( 'service', M('Service') ),
	array( 'price', M('Price') ),
	array( 'location', M('Location') ),
	array( 'resource', M('Bookable Resource') ),
	array( 'customer', M('Customer') ),
	array( 'phone', M('Phone Number') ),
	array( 'altphone', M('Alternate Phone') ),
	array( 'presellar', M('Preseller') ),
	array( 'notes', M('Notes') ),
	);

/* add customer custom fields if any */
$om =& objectMapper::getInstance();
$customerFields = $om->getFields( 'customer', 'internal', true );
$skipCustomerFields = array( 'username', 'first_name', 'last_name' );

reset( $customerFields );
foreach( $customerFields as $cf ){
	if( in_array($cf[0], $skipCustomerFields) ){
		continue;
		}
	$fields[] = array( 'customer.' . $cf[0], $cf[1] );
	}

$headers = array();
reset( $fields );
foreach( $fields as $f )
	$headers[] = $f[1];
echo ntsLib::buildCsv( array_values($headers) );
echo "\n";

reset( $NTS_VIEW['entries'] );
foreach( $NTS_VIEW['entries'] as $a ){
	$output = array();
	if( $a->getProp('cancelled') ){
		$output['status'] = M('Cancelled');
		}
	else {
		if( $a->getProp('no_show') )
			$output['status'] = M('No Show');
		else
			$output['status'] = $a->getProp('approved') ? M('Approved') : M('Pending');
		}

	$t = new ntsTime( $a->getProp('starts_at') );
	$startsAt = $t->formatWeekdayShort() . ', ' . $t->formatDate() . ' ' . $t->formatTime();
	$output['starts_at'] = $startsAt;

	$serviceView = ntsView::appServiceView( $a );
	$serviceView = str_replace( "\n", " ", $serviceView );
	$output['service'] = $serviceView;

	$thisPrice = $a->getProp('price');
	$priceView = ntsCurrency::formatServicePrice($thisPrice);
	$output['price'] = $priceView;

	$location = new ntsObject('location');
	$location->setId( $a->getProp('location_id') );
	$output['location'] = ntsView::objectTitle($location);

	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $a->getProp('resource_id') );
	$output['resource'] = ntsView::objectTitle($resource);

	$customer = new ntsUser();
	$customer->setId( $a->getProp('customer_id') );
	$output['customer'] = $customer->getProp('first_name') . ' ' . $customer->getProp('last_name');
	### Customized by RAH - 4/29/11 - output phone numbers and preseller and notes (6/29/11) on excel output
	### Added array elements at top of this file to include phone and alternate phone and presellar
	$output['phone'] = $customer->getProp('phone');
	$output['altphone'] = $customer->getProp('altphone');
	$output['presellar'] = $customer->getProp('presellar_initials');
	$output['notes'] = $customer->getProp('notes');

	reset( $customerFields );
	foreach( $customerFields as $cf ){
		if( in_array($cf[0], $skipCustomerFields) ){
			continue;
			}
		$output['customer.' . $cf[0]] = $customer->getProp($cf[0]);
		}

	$outLines = array();
	reset( $fields );
	foreach( $fields as $f ){
		$outLines[] = $output[ $f[0] ];
		}
	echo ntsLib::buildCsv( $outLines );
	echo "\n";
	}
?>
