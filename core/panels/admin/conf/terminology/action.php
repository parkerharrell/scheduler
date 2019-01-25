<?php
$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();

$params = array(
	'Bookable Resource',
	'Bookable Resources',
	'Service',
	'Services',
	'Appointment Pack',
	'Appointment Packs',
	'Customer',
	'Customers',
	'Location',
	'Locations',
	);

$default = array();
reset( $params );
$count = 1;
foreach( $params as $p ){
	$default[ $count ] = M($p);
	$count++;
	}
$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $default );

switch( $action ){
	case 'update':
		if( $form->validate($req) ){
			$formValues = $form->getValues();
			reset( $params );
			foreach( $formValues as $fk => $fv ){
				$fk = substr( $fk, strlen('term-') );
				$p = $params[ $fk - 1 ];

				$realPropName = 'text-' . $p;
				$newValue = $conf->set( $realPropName, $fv );
				$sql = $conf->getSaveSql( $realPropName, $newValue );
				$result = $ntsdb->runQuery( $sql );
				}

			if( $result ){
				ntsView::setAnnounce( M('Terminology') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to delivery options form */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				echo '<BR>Database error:<BR>' . $ntsdb->getError() . '<BR>';
				}
			}
		else {
		/* form not valid, continue to create form */
			}

		break;
	}
?>