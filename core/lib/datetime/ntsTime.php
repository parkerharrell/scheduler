<?php
/* new object oriented style */
class ntsTime extends DateTime {
	var $timeFormat = 'H:i';
	var $dateFormat = 'd/m/Y';
	var $weekdays = array();
	var $weekdaysShort = array();
	var $monthNames = array();
	var $monthNamesReplace = array();
	var $timezone = '';

	function ntsTime( $time = 0, $tz = '' ){
//static $initCount;
//$initCount++;
//echo "<h2>init $initCount</h2>";
		if( strlen($time) == 0 )
			$ts = 0;
		if( ! $time )
			$time = time();
		if( is_array($time) )
			$time = $time[0];

		$strTime = '@' . $time;
		parent::__construct( $strTime );

		if( ! $tz ){
			$tz = NTS_COMPANY_TIMEZONE;
			}
		$this->setTimezone( $tz );

		$this->timeFormat = NTS_TIME_FORMAT;
		$this->dateFormat = NTS_DATE_FORMAT;

		$this->weekdays = array( M('Sunday'), M('Monday'), M('Tuesday'), M('Wednesday'), M('Thursday'), M('Friday'), M('Saturday') );
		$this->weekdaysShort = array( M('Sun'), M('Mon'), M('Tue'), M('Wed'), M('Thu'), M('Fri'), M('Sat') );
		$this->monthNames = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );

		$this->monthNamesReplace = array();
		reset( $this->monthNames );
		foreach( $this->monthNames as $mn ){
			$this->monthNamesReplace[] = M($mn);
			}
		}

	function setTimezone( $tz ){
		if( is_array($tz) )
			$tz = $tz[0];

//		if( preg_match('/^-?[\d\.]$/', $tz) ){
//			$currentTz = ($tz >= 0) ? '+' . $tz : $tz;
//			$tz = "Etc/GMT$currentTz";
//			echo "<br><br>Setting timezone as Etc/GMT$currentTz<br><br>";
//			}

		$this->timezone = $tz;
		$tz = new DateTimeZone($tz);
		parent::setTimezone( $tz );
		}

	function getLastDayOfMonth(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();

		$this->setDateTime( $thisYear, ($thisMonth + 1), 0, 0, 0, 0 );
		$return = $this->format( 'j' );
		return $return;
		}

	function getTimestamp(){
		if( function_exists('date_timestamp_get') ){
			return parent::getTimestamp();
			}
		else {
			$return = $this->format('U');
			return $return;
			}
		}

	function setTimestamp( $ts ){
		if( function_exists('date_timestamp_set') ){
			return parent::setTimestamp( $ts );
			}
		else {
			$currentTs = $this->getTimestamp();
			$delta = $ts - $currentTs;
			if( $delta > 0 )
				$this->modify( '+' . $delta . ' seconds' );
			elseif( $delta < 0 )
				$this->modify( '-' . (- $delta) . ' seconds' );

			$currentTs = $this->getTimestamp();
			$delta = $ts - $currentTs;
			if( $delta > 0 )
				$this->modify( '+' . $delta . ' seconds' );
			elseif( $delta < 0 )
				$this->modify( '-' . (- $delta) . ' seconds' );
			}
		}

	function splitDate( $string ){
		$year = substr( $string, 0, 4 );
		$month = substr( $string, 4, 2 );
		$day = substr( $string, 6, 4 );
		$return = array( $year, $month, $day );
		return $return;
		}

	function timestampFromDbDate( $date ){
		list( $year, $month, $day ) = ntsTime::splitDate( $date );
		$this->setDateTime( $year, $month, $day, 0, 0, 0 );
		$return = $this->getTimestamp();
		return $return;
		}

	function getParts(){
		$return = array( $this->format('Y'), $this->format('m'), $this->format('d'), $this->format('H'), $this->format('i') );
		return $return;
		}

	function getYear(){
		$return = $this->format('Y');
		return $return;
		}

	function getMonth(){
		$return = $this->format('m');
		return $return;
		}

	function getDay(){
		$return = $this->format('d');
		return $return;
		}

	function getStartDay(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$thisDay = $this->getDay();

		$this->setDateTime( $thisYear, $thisMonth, $thisDay, 0, 0, 0 );
		$return = $this->getTimestamp();
		return $return;
		}

	function getEndDay(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$thisDay = $this->getDay();

		$this->setDateTime( $thisYear, $thisMonth, ($thisDay + 1), 0, 0, 0 );
		$return = $this->getTimestamp();
		return $return;
		}

	function setStartMonth(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$this->setDateTime( $thisYear, $thisMonth, 1, 0, 0, 0 );
		}

	function setEndMonth(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$this->setDateTime( $thisYear, ($thisMonth + 1), 1, 0, 0, -1 );
		}

	function timezoneShift(){
		$return = 60 * 60 * $this->timezone;
		return $return;
		}

	function setDateTime( $year, $month, $day, $hour, $minute, $second ){
		$this->setDate( $year, $month, $day );
		$this->setTime( $hour, $minute, $second );
		}

	function setDateDb( $date ){
		list( $year, $month, $day ) = ntsTime::splitDate( $date );
		$this->setDateTime( $year, $month, $day, 0, 0, 0 );
		}

	function formatTime( $duration = 0, $displayTimezone = 0 ){
		$return = $this->format( $this->timeFormat );
		if( $duration ){
			$this->modify( '+' . $duration . ' seconds' );
			$return .= ' - ' . $this->format( $this->timeFormat );
			}

		if( $displayTimezone ){
			$return .= ' [' . ntsTime::timezoneTitle($this->timezone) . ']';
			}
		return $return;
		}

	function formatDate(){
		$return = $this->format( $this->dateFormat );
	// replace months 
		$return = str_replace( $this->monthNames, $this->monthNamesReplace, $return );
		return $return;
		}

	function formatDateParam( $year, $month, $day ){
		$return = sprintf("%04d%02d%02d", $year, $month, $day);
		return $return;
		}

	function formatDate_Db(){
		$dateFormat = 'Ymd';
		$return = $this->format( $dateFormat );
		return $return;
		}

	function formatTime_Db(){
		$dateFormat = 'Hi';
		$return = $this->format( $dateFormat );
		return $return;
		}

	function getWeekday(){
		$return = $this->format('w');
		return $return;
		}

	function formatWeekday(){
		$return = $this->weekdays[ $this->format('w') ];
		return $return;
		}

	function formatFull(){
		$return = $this->formatWeekdayShort() . ', ' . $this->formatDate() . ' ' . $this->formatTime();
		return $return;
		}

	function formatWeekdayShort(){
		$return = $this->weekdaysShort[ $this->format('w') ];
		return $return;
		}

	function timezoneTitle( $tz ){
		if( is_array($tz) )
			$tz = $tz[0];
		$tzobj = new DateTimeZone( $tz );
		$dtobj = new DateTime();
		$dtobj->setTimezone( $tzobj );
		$offset = $tzobj->getOffset($dtobj);

		$offsetString = 'GMT';
		$offsetString .= ($offset >= 0) ? '+' : '';
		$offsetString = $offsetString . ( $offset/(60 * 60) );

		$return = $tz . ' (' . $offsetString . ')';
		return $return;
		}

	function getTimezones(){
		$skipStarts = array('Brazil/', 'Canada/', 'Chile/', 'Etc/', 'Mexico/', 'US/');
		$return = array();
		$timezones = timezone_identifiers_list();
		reset( $timezones );
		foreach( $timezones as $tz ){
			if( strpos($tz, "/") === false )
				continue;
			$skipIt = false;
			reset( $skipStarts );
			foreach( $skipStarts as $skip ){
				if( substr($tz, 0, strlen($skip)) == $skip ){
					$skipIt = true;
					break;
					}
				}
			if( $skipIt )
				continue;

			$tzTitle = ntsTime::timezoneTitle( $tz );
			$return[] = array( $tz, $tzTitle );
			}
		return $return;
		}

	function formatPeriod( $ts ){
		$conf =& ntsConf::getInstance();
		$limitMeasure = $conf->get('limitTimeMeasure');

		switch( $limitMeasure ){
			case 'minute':
				$day = 0;
				$hour = 0;
				$minute = (int) ( $ts ) / 60;
				break;
			case 'hour':
				$day = 0;
				$hour = (int) ( ($ts)/(60 * 60));
				$minute = (int) ( $ts - (60 * 60)*$hour ) / 60;
				break;
			default:
				$day = (int) ($ts/(24 * 60 * 60));
				$hour = (int) ( ($ts - (24 * 60 * 60)*$day)/(60 * 60));
				$minute = (int) ( $ts - (24 * 60 * 60)*$day - (60 * 60)*$hour ) / 60;
				break;
			}

		$formatArray = array();
		if( $day > 0 ){
			if( $day > 1 )
				$formatArray[] = $day . ' ' . M('Days');
			else
				$formatArray[] = $day . ' ' . M('Day');
			}
		if( $hour > 0 ){
			if( $hour > 1 )
				$formatArray[] = $hour . ' ' . M('Hours');
			else
				$formatArray[] = $hour . ' ' . M('Hour');
			}
		if( $minute > 0 ){
			if( $minute > 1 )
				$formatArray[] = $minute . ' ' . M('Minutes');
			else
				$formatArray[] = $minute . ' ' . M('Minute');
			}

		$verbose = join( ' ', $formatArray );
		return $verbose;
		}
	}
?>