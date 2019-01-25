<?php
class haTimeManager {
	var $resource = null;
	var $location = null;
	var $service = null;

	var $services = array();

	var $checkOptions = array();
	var $currentCheck = 0;
	var $allowEarlierThanNow;
	var $limitFrame = 0;

	var $BLK_INDX = array();
	var $SLT_INDX = array();
	var $CACHE = array();
	var $dayMode = false;

	function haTimeManager(){
		$ntsConf =& ntsConf::getInstance();
		$showMonths = $ntsConf->get('monthsToShow');
		$this->limitFrame = 6 * 31 * 24 * 60 * 60;

		$this->allowEarlierThanNow = false;
		$this->dayMode = false;

		$services = ntsObjectFactory::getAll( 'service' );
		reset( $services );
		foreach( $services as $s ){
			$serviceId = $s->getId();
			$this->services[ $serviceId ] = $s->getByArray();
			}

		$this->BLK_INDX = array(
			'starts_at'		=> 0,
			'ends_at'		=> 1,
			'selectables'	=> 2,
			'seats'			=> 3,
			);

		$this->SLT_INDX = array(
			'resource_id'	=> 0,
			'location_id'	=> 1,
			'service_id'	=> 2,
			'duration'		=> 3,
			'seats'			=> 4,
			);

		$this->CACHE = array(
			'timeoffs'	=> array(),
			'apps'		=> array(),
			'blocks'	=> array(),
			);
		}

	function setResource( $prv ){
		$this->resource = $prv;
		}
	function setLocation( $loc ){
		$this->location = $loc;
		}
	function setService( $ser ){
		$this->service = $ser;
		}

/* return: true|false */
	function getSelectableTimes_Internal( $tsStart, $tsEnd, $seats = 1 ){
		return $this->getSelectableTimes( $tsStart, $tsEnd, $seats, true );
		}

	function getSelectableTimes( $tsStart = 0, $tsEnd = 0, $seats = 1, $allTime = false ){
		if( ! ($tsStart && $tsEnd ) ){
			list( $tsStart, $tsEnd ) = $this->getStartEnd();
			}

		if( $this->dayMode ){
			// build day index
			$DAY_INDEX = array();
			$DAY_INDEXER_SIZE = 24 * 60 * 60;
			$this->t->setTimestamp( $tsStart );
			$nextTs = $this->t->getTimestamp();
			$j = 0;
			while( $nextTs < $tsEnd ){
				$DAY_INDEX[ $j++ ] = array( $this->t->formatDate_Db(), $this->t->getStartDay() );
				$this->t->modify( '+1 day' );
				$nextTs = $this->t->getTimestamp();
				}
			}
	
		$INDEXER_SIZE = 100000;

		if( $allTime )
			$this->allowEarlierThanNow = true;

		$return = array();
		$now = time();

	/* service */
		$blocks = $this->getBlocks( $tsStart, $tsEnd );
		if( ! $blocks ){
			return $return;
			}

/* APPOINTMENTS */
		$appointments = $this->getAppointments( $tsStart, $tsEnd );
		$COUNT_APPS = count($appointments);
		$MAX_INDEX = 0;
		$INDEXER_START = 0;
		reset( $appointments );
		for( $i = 0; $i < $COUNT_APPS; $i++ ){
			if( ! $i )
				$INDEXER_START = $appointments[$i]['starts_at'] + $appointments[$i]['duration'] + $appointments[$i]['lead_out'];

		// make index
			$indexIndex = floor( ($appointments[$i]['starts_at'] + $appointments[$i]['duration'] + $appointments[$i]['lead_out'] - $INDEXER_START)/$INDEXER_SIZE );
			if( ! isset($INDEXER[$indexIndex]) ){
				$INDEXER[$indexIndex] = $i;
				}
			$MAX_INDEX = $indexIndex;
			}

		for( $ii = 0; $ii < $MAX_INDEX; $ii++ ){
			if( ! isset($INDEXER[$ii]) ){
				if( $ii == 0 )
					$INDEXER[$ii] = 0;
				else {
					$INDEXER[$ii] = $INDEXER[$ii - 1];
					}
				}
			}

/* TIMEOFFS */
		$timeoffs = $this->getTimeoffs( $tsStart, $tsEnd );

/* PROCESS */
	foreach( $blocks as $lrs => $ba ){
		list( $thisLocId, $thisResId, $thisServiceId ) = explode( '-', $lrs );
		foreach( $ba as $b ){
			$subBlocksCount = count($b);
			$blockSeats = $b[0][ $this->BLK_INDX['seats'] ];
			$blockStartsAt = $b[0][ $this->BLK_INDX['starts_at'] ];
			$blockEndsAt = $b[$subBlocksCount-1][ $this->BLK_INDX['ends_at'] ]; 

			$tss = array();
			for( $j = 0; $j < $subBlocksCount; $j++ ){
				$thisSelectables = $b[$j][$this->BLK_INDX['selectables']];
				if( is_array($thisSelectables) ){
					$tss = array_merge( $tss, $thisSelectables );
					}
				else {
					if( $thisSelectables > 0 ){
						$ts = $b[$j][ $this->BLK_INDX['starts_at'] ];
						while( $ts < $b[$j][ $this->BLK_INDX['ends_at'] ] ){
							$tss[] = $ts;
							$ts += $thisSelectables;
							}
						}
					}
				}

			reset( $tss );
			foreach( $tss as $ts ){
				if( $ts > $tsEnd ){
					break;
					}

				if( ! $allTime ){
				// check if within frame
					$minFromNow = $now + $this->services[$thisServiceId]['min_from_now'];
					$maxFromNow = $now + $this->services[$thisServiceId]['max_from_now'];
					if( $ts > $maxFromNow )
						break;
					if( $ts < $minFromNow )
						continue;
					}

			// check if within frame
				if( (! $this->allowEarlierThanNow) && ($ts < $now) ){
					continue;
					}

				if( $ts < $tsStart ){
					continue;
					}

				if( $this->dayMode ){
					$thisTsIndex = floor( ( $ts - $tsStart ) / $DAY_INDEXER_SIZE );
					if( $ts < $DAY_INDEX[$thisTsIndex][1] )
						$thisTsIndex = $thisTsIndex - 1;
					elseif( isset($DAY_INDEX[$thisTsIndex + 1][1]) && ($ts >= $DAY_INDEX[$thisTsIndex + 1][1]) )
						$thisTsIndex = $thisTsIndex + 1;
					$tsDate = $DAY_INDEX[$thisTsIndex][0];

					if( isset($return[$tsDate]) ){
						continue;
						}
					}

			// if fits in
				if( $ts <= $blockEndsAt ){
					$slot = array();

					$leadIn = $this->services[$thisServiceId]['lead_in'];
					if( ($ts - $leadIn) < $blockStartsAt )
						continue;

					$leadOut = $this->services[$thisServiceId]['lead_out'];
					$UNTIL_CLOSED = $this->services[$thisServiceId]['until_closed'];
					if( $UNTIL_CLOSED ){
						$duration = $blockEndsAt - $ts - $leadOut;
						}
					else {
						$duration = $this->services[$thisServiceId]['duration'];
						}

					if( $UNTIL_CLOSED && (! $duration ) ){
						// no need for zero duration
						}
					else {
					// if fits in
						if( ($ts + $duration + $leadOut) <= $blockEndsAt ){
							$slot = array(
								$thisResId,
								$thisLocId,
								$thisServiceId,
								$duration,
								$blockSeats,
								);
							}
						}
					}

				if( $slot ){
					$DELETE_SLOT = false;
					$slotServiceId = $slot[$this->SLT_INDX['service_id']];
					$slotDuration = $slot[$this->SLT_INDX['duration']];
					$slotLeadIn = $this->services[$slotServiceId]['lead_in'] ;
					$slotLeadOut = $this->services[$slotServiceId]['lead_out'];
					$slotResourceId = $slot[$this->SLT_INDX['resource_id']];

				// APPLY TIMEOFFS
					reset( $timeoffs );
					foreach( $timeoffs as $to ){
						if( $DELETE_SLOT )
							break;
					// if not this resource then continue
						if( $slotResourceId != $to['resource_id'] )
							continue;
							
						if( $to['starts_at'] >= ($ts + $slotDuration + $slotLeadOut) ){
							break;
							}
						if( $to['ends_at'] <= ($ts - $slotLeadIn) ){
							continue;
							}

						$DELETE_SLOT = true;
						}

					if( $DELETE_SLOT )
						continue;

				// APPLY APPOINTMENTS
					if( $COUNT_APPS ){
						$thisSlotIi = floor( ( $ts - $slotLeadIn - $INDEXER_START ) / $INDEXER_SIZE );
						if( $thisSlotIi < 0 )
							$thisSlotIi = 0;

						if( $thisSlotIi > $MAX_INDEX )
							$thisSlotIi = $MAX_INDEX;
						for( $i = $INDEXER[$thisSlotIi]; $i < $COUNT_APPS; $i++ ){
							if( $DELETE_SLOT )
								break;
							$a = $appointments[$i];

							if( ($a['starts_at'] - $a['lead_in']) >= ($ts + $slotDuration + $slotLeadOut) ){
								break;
								}
							if( ($a['starts_at'] + $a['duration'] + $a['lead_out']) <= ($ts - $slotLeadIn) ){	
								continue;
								}

							/* this resource, this service - delete everything but the start at this location */
							if( 
								( $slotResourceId == $a['resource_id'] ) &&
								( $slotServiceId == $a['service_id'] )
								)
								{
								if( ($slot[$this->SLT_INDX['location_id']] == $a['location_id']) ){
								/* this slot */
									if( (! $this->services[ $a['service_id'] ]['class_type']) || ($a['starts_at'] == $ts ) ){
										if( $slot[$this->SLT_INDX['seats']] >= $a['seats'] ){
											$slot[$this->SLT_INDX['seats']] = $slot[$this->SLT_INDX['seats']] - $a['seats'];
											}
										else {
											$slot[$this->SLT_INDX['seats']] = 0;
											}

										if( $slot[$this->SLT_INDX['seats']] <= 0 ){
											$DELETE_SLOT = true;
											}
										}
								/* other slot */
									else {
										$DELETE_SLOT = true;
										}
									}
								else {
									$DELETE_SLOT = true;
									}
								}
						/* this resource, other service - delete everything */
							elseif( 
								( $slotResourceId == $a['resource_id'] )
								)
								{
								$DELETE_SLOT = true;
								}
						/* any resource, any service - continue */
							else {
								continue;
								}
							}
						}

					if( ! $DELETE_SLOT ){
						if( $this->dayMode ){
							$return[ $tsDate ] = 1;
							}
						else {
							if( ! isset($return[$ts]) )
								$return[$ts] = array();
							$return[$ts][] = $slot;
							}
						}
					}
				}
			}
		}

/* ADDITIONALLY PROCESS BY PLUGINS */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		$additionalProcessFiles = array();
		reset( $activePlugins );
		foreach( $activePlugins as $plg ){
			$addFile = $plm->getPluginFolder( $plg ) . '/timeManager/getSelectableTimes.php';
			if( file_exists($addFile) )
				$additionalProcessFiles[] = $addFile;
			}
		reset( $additionalProcessFiles );
		foreach( $additionalProcessFiles as $apf ){
			require( $apf );
			}

/* RETURN */
		ksort( $return );
		return $return;
		}

	function check(){
		$now = time();
		list( $tsStart, $tsEnd ) = $this->getStartEnd();

		$return = array(
			'resources'	=> array(),
			'services'	=> array(),
			'locations'	=> array(),
			);

	// get schedules then prepare toCheck array
		$TO_CHECK = array(
			'resources'	=> array(),
			'services'	=> array(),
			'locations'	=> array(),
			);
		$schedules = $this->getSchedules();
		foreach( $schedules as $sch ){
			$TO_CHECK['resources'][ $sch->getProp('resource_id') ] = 1;

			if( $this->location ){
				$TO_CHECK['locations'][ $this->location->getId() ] = 1;
				}
			else {
				$locations = $sch->getProp( '_location' );
				foreach( $locations as $locId )
					$TO_CHECK['locations'][ $locId ] = 1;
				}
			
			if( $this->service ){
				$TO_CHECK['services'][ $this->service->getId() ] = 1;
				}
			else {
				$services = $sch->getProp( '_service' );
				foreach( $services as $servId ){
					if( 
						( ($now + $this->services[ $servId ]['max_from_now']) < $tsStart ) ||
						( ($now + $this->services[ $servId ]['min_from_now']) > $tsEnd )
						){
						continue;
						}
					$TO_CHECK['services'][ $servId ] = 1;
					}
				}
			}

	// NOW GO CHECK BY CHUNKS OF n DAYS
		$chunkSize = 3;
		$startCheck = $tsStart;
		while( $startCheck < $tsEnd ){
			if(
				(! $TO_CHECK['resources'] ) &&
				(! $TO_CHECK['locations'] ) &&
				(! $TO_CHECK['services'] )
				){
				break;
				}

			$endCheck = $startCheck + $chunkSize * 24 * 60 * 60;
			$times = $this->getSelectableTimes( $startCheck, $endCheck );
//_print_r( $times );			
			reset( $times );
			foreach( $times as $ts => $tsa ){
				if(
					(! $TO_CHECK['resources'] ) &&
					(! $TO_CHECK['locations'] ) &&
					(! $TO_CHECK['services'] )
					){
					break;
					}

				reset( $tsa );
				foreach( $tsa as $ta ){
					$thisResId = $ta[$this->SLT_INDX['resource_id']];
					$thisLocId = $ta[$this->SLT_INDX['location_id']];
					$thisServId = $ta[$this->SLT_INDX['service_id']];

					if( 
						isset($return['resources'][$thisResId]) &&
						isset($return['locations'][$thisLocId]) &&
						isset($return['services'][$thisServId])
						){
						continue;
						}

					if( (! isset($return['resources'][$thisResId])) || ($return['resources'][$thisResId] > $ts) ){
						$return['resources'][$thisResId] = $ts;
						unset($TO_CHECK['resources'][$thisResId]);
						}

					if( (! isset($return['locations'][$thisLocId])) || ($return['locations'][$thisLocId] > $ts) ){
						$return['locations'][$thisLocId] = $ts;
						unset($TO_CHECK['locations'][$thisLocId]);
						}

					if( (! isset($return['services'][$thisServId])) || ($return['services'][$thisServId] > $ts) ){
						$return['services'][$thisServId] = $ts;
						unset($TO_CHECK['services'][$thisServId]);
						}
					}
				}
			$startCheck = $endCheck + 1;
			}

		return $return;
		}

	function _getFrame(){
		$return = array( 0, 0 );

		$now = time();
		$START_TS = $now;

	// min from now
		if( $this->service ){
			$minFromNow = $this->service->getProp( 'min_from_now' );
			}
		else {
			$minFromNow = 0;
			reset( $this->services );
			foreach( $this->services as $sid => $sa ){
				if( ! $minFromNow )
					$minFromNow = $sa['min_from_now'];
				if( $sa['min_from_now'] < $minFromNow ){
					$minFromNow = $sa['min_from_now'];
					}
				}
			}
		$START_TS = $START_TS + $minFromNow;

	// max from now
		if( $this->service ){
			$maxFromNow = $this->service->getProp( 'max_from_now' );
			}
		else {
			$maxFromNow = 0;
			reset( $this->services );
			foreach( $this->services as $sid => $sa ){
				if( $sa['max_from_now'] > $maxFromNow ){
					$maxFromNow = $sa['max_from_now'];
					}
				}
			}
		$END_TS = $now + $maxFromNow;
		$return = array( $START_TS, $END_TS );
		return $return;
		}

	function getSchedules(){
		$return = array();

		$resourceId = ( $this->resource ) ? $this->resource->getId() : 0;
		$locationId = ( $this->location ) ? $this->location->getId() : 0;
		$serviceId = ( $this->service ) ? $this->service->getId() : 0;	
		
		list( $START_TS, $END_TS ) = $this->_getFrame();

		$ntsdb =& dbWrapper::getInstance();
		$t = new ntsTime( $START_TS );
		$systemDateStart = $t->formatDate_Db();

		$startWhere = $resourceId ? "{PRFX}schedules.resource_id = $resourceId AND " : "";

		$SCHEDULES = array();
	/* get schedules first */
		$sql =<<<EOT
		SELECT
			id
		FROM
			{PRFX}schedules
		WHERE
			$startWhere
			{PRFX}schedules.valid_to >= $systemDateStart
EOT;
		$result = $ntsdb->runQuery( $sql );
		$maxValidTo = 0;
		$minValidFrom = 0;

		while( $e = $result->fetch() ){
			$scheduleId = $e['id'];
			$schedule = ntsObjectFactory::get( 'schedule' );
			$schedule->setId( $scheduleId );
			$resId = $schedule->getProp('resource_id');

			$locations = $schedule->getProp( '_location' );
			if( $locationId && (! in_array($locationId, $locations) ) )
				continue;

			$services = $schedule->getProp( '_service' );
			if( $serviceId && (! in_array($serviceId, $services) ) )
				continue;

			$return[] = $schedule;
			}
		return $return;
		}

	function getStartEnd(){
		$return = array( 0, 0 );
		$t = new ntsTime();
		list( $START_TS, $END_TS ) = $this->_getFrame();

		$SCHEDULES = array();
		$schedules = $this->getSchedules();
		foreach( $schedules as $schedule ){
			$resId = $schedule->getProp( 'resource_id' );

			$validFrom = $schedule->getProp( 'valid_from' );
			$t->setDateDb( $validFrom );
			$thisValidFrom = $t->getTimestamp();

			$validTo = $schedule->getProp( 'valid_to' );
			$t->setDateDb( $validTo );
			$t->modify( '+1 day' );
			$thisValidTo = $t->getTimestamp();

			if( $thisValidFrom < $START_TS ){
				$thisValidFrom = $START_TS;
				}
			$SCHEDULES[] = array( $resId, $thisValidFrom, $thisValidTo );
			}

	// NO SCHEDULES FOUND
		if( ! $SCHEDULES ){
			return $return;
			}

	// get timeoffs
		$timeoffs = $this->getTimeoffs( $START_TS, $END_TS );

		reset( $timeoffs );
		foreach( $timeoffs as $to ){
			$countSchedules = count( $SCHEDULES );
			for( $i = 0; $i < $countSchedules; $i++ ){
				if( 
					( $to['resource_id'] != $SCHEDULES[$i][0] ) ||
					( $to['starts_at'] >= $SCHEDULES[$i][2] ) ||
					( $to['ends_at'] <= $SCHEDULES[$i][1] )
					){
					continue;
					}
				else {
					if( $to['starts_at'] <= $SCHEDULES[$i][1] ){
						if( $to['ends_at'] >= $SCHEDULES[$i][2] ){
							// complete fail;
							array_splice( $SCHEDULES, $i, 1 );
							}
						else {
							// update the start of schedule
							$SCHEDULES[$i][1] = $to['ends_at'];
							}
						}
					else {
						if( $to['ends_at'] >= $SCHEDULES[$i][2] ){
							// update the end of schedule
							$SCHEDULES[$i][2] = $to['starts_at'];
							}
						else {
							// split into parts
							$sch1 = array( $SCHEDULES[$i][0], $SCHEDULES[$i][1], $to['starts_at'] );
							$sch2 = array( $SCHEDULES[$i][0], $to['ends_at'], $SCHEDULES[$i][2] );
							array_splice( $SCHEDULES, $i, 1, array($sch1, $sch2) );
							}
						}
					}
				}
			}

		if( ! $SCHEDULES ){
			return $return;
			}

		// ok now we get our schedules 
		$minStart = $SCHEDULES[0][1];
		$maxEnd = $SCHEDULES[0][2];
		reset( $SCHEDULES );
		foreach( $SCHEDULES as $sch ){
			if( $sch[1] < $minStart ){
				$minStart = $sch[1];
				}
			if( $sch[2] > $maxEnd ){
				$maxEnd = $sch[2];
				}
			}
		
		if( $minStart > $START_TS ){
			$START_TS = $minStart;
			}
		if( $maxEnd < $END_TS ){
			$END_TS = $maxEnd;
			}
		// if too much needed then limit to limitFrame
		if( ($END_TS - $START_TS) > $this->limitFrame ){
			$END_TS = $START_TS + $this->limitFrame;
			}

		$return = array( $START_TS, $END_TS );
		return $return;
		}

	function getBlocks( $tsStart, $tsEnd ){
		$return = array();

		$resourceId = ( $this->resource ) ? $this->resource->getId() : 0;
		$locationId = ( $this->location ) ? $this->location->getId() : 0;
		$serviceId = ( $this->service ) ? $this->service->getId() : 0;	

		$cacheString = join( '-', array($resourceId, $locationId, $serviceId, $tsStart, $tsEnd) );
		if( isset($this->CACHE['blocks'][$cacheString]) ){
			$return = $this->CACHE['blocks'][$cacheString];
			return $return;
			}

		$ntsdb =& dbWrapper::getInstance();

	/* get start and end date in system db */
		$t = new ntsTime( $tsStart );
		$systemDateStart = $t->formatDate_Db();
		$t->setTimestamp( $tsEnd );
		$systemDateEnd = $t->formatDate_Db();

		$startWhere = $resourceId ? "{PRFX}schedules.resource_id = $resourceId AND " : "";

		global $NTS_SKIP_RESOURCES;
		if( $NTS_SKIP_RESOURCES ){
			$skipResourcesIds = join( ',', $NTS_SKIP_RESOURCES );
			$startWhere .= "{PRFX}schedules.resource_id NOT IN ($skipResourcesIds) AND ";
			}

		$blocks = array();
	/* get schedules first */
		$sql =<<<EOT
		SELECT
			id
		FROM
			{PRFX}schedules
		WHERE
			$startWhere
			{PRFX}schedules.valid_to >= $systemDateStart AND
			{PRFX}schedules.valid_from <= $systemDateEnd
EOT;
		$result = $ntsdb->runQuery( $sql );
		while( $e = $result->fetch() ){
			$scheduleId = $e['id'];
			$schedule = ntsObjectFactory::get( 'schedule' );
			$schedule->setId( $scheduleId );
			$resId = $schedule->getProp('resource_id');

			$locations = $schedule->getProp( '_location' );
			if( $locationId && (! in_array($locationId, $locations) ) )
				continue;

			$services = $schedule->getProp( '_service' );
			if( $serviceId && (! in_array($serviceId, $services) ) )
				continue;

		/* now get timeblocks of this schedule */
			$sql2 =<<<EOT
			SELECT
				starts_at,
				ends_at,
				selectable_every,
				selectable_fixed,
				applied_on
			FROM
				{PRFX}timeblocks
			WHERE
				schedule_id = $scheduleId
			ORDER BY
				starts_at ASC
EOT;
			$result2 = $ntsdb->runQuery( $sql2 );
			while( $e2 = $result2->fetch() ){
				$e2[ 'selectable_fixed' ] = strlen($e2['selectable_fixed']) ? unserialize($e2['selectable_fixed']) : array();
				$e2[ 'valid_from' ] = $schedule->getProp('valid_from');
				$e2[ 'valid_to' ] = $schedule->getProp('valid_to');
				$e2[ 'seats' ] = $schedule->getProp('capacity');
				$e2[ 'locations' ] = $locations;
				$e2[ 'services' ] = $services;
				$e2[ 'resource_id' ] = $resId;
				$blocks[] = $e2;
				}
			}

	/* walk every system date within range */
		$systemDate = $systemDateStart;
		list( $year, $month, $day ) = ntsTime::splitDate( $systemDate );
		$t->setDateTime( $year, $month, $day, 0, 0, 0 );
		$dayStartTs = $t->getTimestamp();
		$thisWeekday = $t->getWeekday();

		$LAST_BLOCKS = array();
		$RI = 0;
		while( $systemDate <= $systemDateEnd ){
		/* get blocks for date */
			reset( $blocks );
			foreach( $blocks as $b ) {
				if( ! ( ($b['valid_from'] <= $systemDate) && ($b['valid_to'] >= $systemDate) ) ){
					continue;
					}
				if( $b['applied_on'] != $thisWeekday )
					continue;

				/* sum block with date start */
				$b['full_starts_at'] = $dayStartTs + $b['starts_at'];
				$b['full_ends_at'] = $dayStartTs + $b['ends_at'];

				$thisSelectables = array( $b['full_starts_at'], $b['full_ends_at'] );
				/* full time for selectable fixed */
				if( $b['selectable_fixed'] ){
					$thisSelectables[ 2 ] = array();
					$fixedCount = count( $b['selectable_fixed'] );
					for( $ff = 0; $ff < $fixedCount; $ff++ ){
						$thisSelectables[ 2 ][$ff] = $dayStartTs + $b['selectable_fixed'][$ff];
						}
					}
				else {
					$thisSelectables[ 2 ] = $b['selectable_every'];
					}
				$thisSelectables[ 3 ] = $b['seats'];

				if( $locationId )
					$b['locations'] = array( $locationId );
				reset( $b['locations'] );
				foreach( $b['locations'] as $locId ){
					if( $serviceId )
						$b['services'] = array( $serviceId );
					reset( $b['services'] );
					foreach( $b['services'] as $serId ){
					// l-r-s
						$hashIndex = $locId . '-' . $b['resource_id'] . '-' . $serId;
						if( ! isset($return[$hashIndex]) )
							$return[$hashIndex] = array();

						// glue blocks?
						if( isset($LAST_BLOCKS[$hashIndex]) && ($LAST_BLOCKS[$hashIndex] == $b['full_starts_at']) ){
							$LI = count($return[$hashIndex]) - 1;
							$return[$hashIndex][$LI][] = $thisSelectables;
							$LAST_BLOCKS[$hashIndex] = $b['full_ends_at'];
							}
						// add new block
						else {
							$return[$hashIndex][] = array( $thisSelectables );
							$LAST_BLOCKS[$hashIndex] = $thisSelectables[1];
							}
						}
					}
				}

			list( $year, $month, $day ) = ntsTime::splitDate( $systemDate );

			$t->modify( '+1 day' );
			$dayStartTs = $t->getTimestamp();
			$systemDate = $t->formatDate_Db();
			$thisWeekday = $t->getWeekday();
			}

		$this->CACHE['blocks'][$cacheString] = $return;
		return $return;
		}

	function getTimeoffs( $tsStart, $tsEnd ){
		$return = array();

		$resourceId = ( $this->resource ) ? $this->resource->getId() : 0;

		$cacheString = join( '-', array($resourceId, $tsStart, $tsEnd) );
		if( isset($this->CACHE['timeoffs'][$cacheString]) ){
			$return = $this->CACHE['timeoffs'][$cacheString];
			return $return;
			}

		$ntsdb =& dbWrapper::getInstance();
			
	/* get start and end date in system db */
		$startWhere = $resourceId ? "{PRFX}timeoffs.resource_id = $resourceId AND " : '';

		$sql =<<<EOT
		SELECT
			resource_id, location_id, starts_at, ends_at
		FROM
			{PRFX}timeoffs
		WHERE
			$startWhere
			{PRFX}timeoffs.starts_at < $tsEnd AND
			{PRFX}timeoffs.ends_at > $tsStart
		ORDER BY
			{PRFX}timeoffs.starts_at ASC
EOT;

		$result = $ntsdb->runQuery( $sql );
		while( $e = $result->fetch() ){
			$return[] = $e;
			}

		$this->CACHE['timeoffs'][$cacheString] = $return;
		return $return;
		}

	function getAppointments( $tsStart, $tsEnd ){
		$return = array();

		$skip = array();
		global $NTS_SKIP_APPOINTMENTS;
		if( $NTS_SKIP_APPOINTMENTS )
			$skip = array_merge( $skip, $NTS_SKIP_APPOINTMENTS );

		$resourceId = ( $this->resource ) ? $this->resource->getId() : 0;
		$locationId = ( $this->location ) ? $this->location->getId() : 0;
		$serviceId = ( $this->service ) ? $this->service->getId() : 0;	

		$cacheString = join( '-', array($resourceId, $locationId, $serviceId, $tsStart, $tsEnd) );
		if( isset($this->CACHE['apps'][$cacheString]) ){
			$return = $this->CACHE['apps'][$cacheString];
			return $return;
			}

		$ntsdb =& dbWrapper::getInstance();
		$startWhereString = 'no_show = 0 AND cancelled = 0 AND ';

		$startWhere = array();
		if( $resourceId ){
			$startWhere[] = "resource_id = $resourceId";
			}
		if( $locationId ){
			$startWhere[] = "location_id = $locationId";
			}

		$startWhereString .= ($startWhere) ? '(' . join(' AND ' , $startWhere) . ') AND ' : '';

		$skipWhere = '';
		if( $skip ){
			$skipWhere = "{PRFX}appointments.id NOT IN(" . join( ',', $skip) . ") AND ";
			}

		$sql =<<<EOT
		SELECT
			{PRFX}appointments.*
		FROM
			{PRFX}appointments
		INNER JOIN
			{PRFX}services
		ON
			{PRFX}services.id = {PRFX}appointments.service_id
		WHERE
			$startWhereString
			$skipWhere
			({PRFX}appointments.starts_at - {PRFX}appointments.lead_in) < $tsEnd AND
			({PRFX}appointments.starts_at + {PRFX}appointments.duration + {PRFX}appointments.lead_out) > $tsStart
		ORDER BY
			({PRFX}appointments.starts_at - {PRFX}appointments.lead_in) ASC
EOT;
		$result = $ntsdb->runQuery( $sql );
		while( $e = $result->fetch() ){
			$return[] = $e;
			}

		$this->CACHE['apps'][$cacheString] = $return;
		return $return;
		}
	}
?>