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
	
$t = new ntsTime( $startTs );
$startTs = $t->getStartDay();
$t = new ntsTime( $endTs );
$endTs = $t->getEndDay();

// get apps
// temporarily reset location
$saveLocation = $this->location;
$this->setLocation( null );
$appointments = $this->getAppointments( $startTs, $endTs );
$this->setLocation( $saveLocation );

reset( $appointments );
foreach( $appointments as $a ){
	$thisLocId = $a['location_id'];
	$thisResId = $a['resource_id'];
	$t = new ntsTime( $a['starts_at'] - $a['lead_in'] );
	$thisStart = $t->getStartDay();

	$t = new ntsTime( $a['starts_at'] + $a['duration'] + $a['lead_out'] );
	$thisEnd = $t->getEndDay();

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
				isset( $tArray[ $jj ][ $this->SLT_INDX['resource_id'] ] ) && 
				( $tArray[ $jj ][ $this->SLT_INDX['resource_id'] ] == $thisResId ) &&
				( $tArray[ $jj ][ $this->SLT_INDX['location_id'] ] != $thisLocId )
				)
				{
//				echo "UNSET: app resid = $thisResId, thisresid = " . $tArray[ $jj ][ $this->SLT_INDX['resource_id'] ] . '<br>';
//				echo "app loc id = $thisLocId, thislocid = " . $tArray[ $jj ][ $this->SLT_INDX['location_id'] ] . '<br><br>';
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