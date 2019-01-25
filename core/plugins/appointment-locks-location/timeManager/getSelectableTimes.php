<?php
if( ! $return )
	return;

reset( $return );
$timestamps = array_keys( $return );

$startTs = $timestamps[ 0 ];
if( count($timestamps) == 1 ){
	$endTs = $timestamps[ 0 ];
	}
else {
	$endTs = $timestamps[ count($timestamps) - 1 ];
	}

// get apps
// temporarily reset location
$saveLocation = $this->location;
$this->setLocation( null );

$saveResource = $this->resource;
$this->setResource( null );

$appointments = $this->getAppointments( $startTs, $endTs );

$this->setLocation( $saveLocation );
$this->setResource( $saveResource );

reset( $appointments );
foreach( $appointments as $a ){
	$thisLocId = $a['location_id'];

	$thisStart = $a['starts_at'] - $a['lead_in'];
	$thisEnd = $a['starts_at'] + $a['duration'] + $a['lead_out'];

	reset( $return );
	foreach( $return as $ts => $tArray ){
		if( ($ts + $duration + $leadOut) <= $thisStart ){
			continue;
			}
		if( ($ts - $leadIn) >= $thisEnd ){
			continue;
			}
			
		$tArrayCount = count( $tArray );
		for( $jj = ($tArrayCount - 1); $jj >=0; $jj-- ){
			if( 
				( $tArray[ $jj ][ $this->SLT_INDX['location_id'] ] == $thisLocId )
				)
				{
				unset( $tArray[$jj] );
				}
			}

		if( $tArray )
			$return[ $ts ] = $tArray;
		else
			unset( $return[ $ts ] );
		}
	}
?>