<?php
require_once( dirname(__FILE__) . '/../common/grab.php' );

$recurTotal = $req->getParam( 'recur-total' );
$recurEvery = $req->getParam( 'recur-every' );

$calTs = $NTS_CURRENT_REQUEST[0]['time'];
$t = new ntsTime( $calTs, $NTS_CURRENT_USER->getTimezone() );

$firstDate = $t->formatDate_Db();
$dates2check = array( $firstDate );

$t = $NTS_VIEW['t'];
for( $i = 1; $i < $recurTotal; $i++ ){
	switch( $recurEvery ){
		case 'd':
			$string = '+' . $i . ' days';
			break;
		case '2d':
			$string = '+' . (2 * $i) . ' days';
			break;
		case 'w':
			$string = '+' . $i . ' weeks';
			break;
		case '2w':
			$string = '+' . (2 * $i) . ' weeks';
			break;
		case '3w':
			$string = '+' . (3 * $i) . ' weeks';
			break;
		case 'm':
			$string = '+' . $i . ' month';
			break;
		}

	$t = new ntsTime( $calTs, $NTS_CURRENT_USER->getTimezone() );
	$t->modify( $string );

	$nextDate = $t->formatDate_Db();
	$dates2check[] = $nextDate;
	}

$total = count( $dates2check );

/* set current request */
for( $i = 0; $i < $total; $i++ ){
	$NTS_CURRENT_REQUEST[$i]['service'] = $NTS_CURRENT_REQUEST[0]['service'];
	$NTS_CURRENT_REQUEST[$i]['location'] = $NTS_CURRENT_REQUEST[0]['location'];
	$NTS_CURRENT_REQUEST[$i]['resource'] = $NTS_CURRENT_REQUEST[0]['resource'];
	$NTS_CURRENT_REQUEST[$i]['seats'] = $NTS_CURRENT_REQUEST[0]['seats'];
	$NTS_CURRENT_REQUEST[$i]['ghost'] = null;
	if( $i ){
		$NTS_CURRENT_REQUEST[$i]['time'] = 0;
		}
	}

/* save ghost apps */
require( dirname(__FILE__) . '/../common/init-ghost.php' );

/* set selected location to session */
ntsView::setPersistentParams( 
	array(
		'cal' 		=> join( '-', $dates2check),
		'total' 	=> $total,
		'cri' 		=> ( $total > 1 ) ? 1 : 0,
		),
	$req, 'customer/appointments/request'
	);

require( dirname(__FILE__) . '/../common/dispatcher.php' );
exit;
?>