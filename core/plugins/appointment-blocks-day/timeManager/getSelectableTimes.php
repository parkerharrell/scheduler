<?php
if( ! $return )
	return;

if( $this->dayMode ){
	$dates = array_keys( $return );
	$startDate = $dates[ 0 ];
	if( count($dates) == 1 ){
		$endDate = $dates[ 0 ];
		}
	else {
		$endDate = $dates[ count($dates) - 1 ];
		}
	$t = new ntsTime();
	$t->setDateDb( $startDate );
	$startTs = $t->getStartDay();
	$t->setDateDb( $endDate );
	$endTs = $t->getEndDay();
	}
else {
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
	}

// get apps
// temporarily reset location
$appointments = $this->getAppointments( $startTs, $endTs );

reset( $appointments );
foreach( $appointments as $a ){
	$thisResId = $a['resource_id'];
	$t = new ntsTime( $a['starts_at'] - $a['lead_in'] );
	$thisStart = $t->getStartDay();
	$thisDate = $t->formatDate_Db();

	$t = new ntsTime( $a['starts_at'] + $a['duration'] + $a['lead_out'] );
	$thisEnd = $t->getEndDay();

	if( $this->dayMode ){
		unset( $return[$thisDate] );
		}
	else {
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
					( $tArray[ $jj ][ $this->SLT_INDX['resource_id'] ] == $thisResId )
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
	}
?>