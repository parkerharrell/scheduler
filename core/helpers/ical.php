<?php
if( ! class_exists('vcalendar') ){
	include_once( NTS_BASE_DIR . '/lib/datetime/iCalcreator.class.php' );
	}

class ntsIcal {
	var $appointments = array();
	var $timezone = 0;

	function ntsIcal(){
		$this->appointments = array();
		$this->setTimezone( NTS_COMPANY_TIMEZONE );
		}

	function addAppointment( $appId ){
		if( is_object($appId) )
			$appId = $appId->getId();
		$this->appointments[] = $appId;
		}

	function setTimezone( $tz ){
		$this->timezone = $tz;
		}

	function printOut(){
		$ntsdb =& dbWrapper::getInstance();
		$ntsdb->_enableCache = false;
	
		$cal = new vcalendar(); // initiate new CALENDAR
		$cal->setConfig( 'unique_id', NTS_ROOT_WEBDIR );
//		$cal->setProperty( 'method', 'publish' );
		$cal->setProperty( 'method', 'request' );
		$cal->setProperty( 'x-wr-timezone', $this->timezone );

		reset( $this->appointments );
		foreach( $this->appointments as $appId ){
			$a = ntsObjectFactory::get( 'appointment' );
			$a->setId( $appId );

			$serviceTitle = ntsView::appServiceView( $a );
			$appTitle = $serviceTitle;

			$location = new ntsObject('location');
			$location->setId( $a->getProp('location_id') );

			$customer = new ntsUser();
			$customer->setId( $a->getProp('customer_id') );
			$resource = ntsObjectFactory::get( 'resource' );
			$resource->setId( $a->getProp('resource_id') );

			$event = new vevent(); // initiate a new EVENT
			$event->setProperty( 'uid', 'app-' . $a->getId() . '-' . NTS_ROOT_WEBDIR );

//			$t = new ntsTime( $a->getProp('starts_at'), $this->timezone );
//			list( $year, $month, $day, $hour, $min ) = $t->getParts(); 
//			$event->setProperty( 'dtstart', $year, $month, $day, $hour, $min, 00, $this->timezone );  // 24 dec 2006 19.30

			$t = new ntsTime( $a->getProp('starts_at'), 'UTC' );
			list( $year, $month, $day, $hour, $min ) = $t->getParts(); 
			$event->setProperty( 'dtstart', $year, $month, $day, $hour, $min, 00, 'Z' );  // 24 dec 2006 19.30

			$t->modify( '+' . $a->getProp('duration') . ' seconds' );
			list( $year, $month, $day, $hour, $min ) = $t->getParts(); 
//			$event->setProperty( 'dtend', $year, $month, $day, $hour, $min, 00 );  // 24 dec 2006 19.30
			$event->setProperty( 'duration', 0,		0,		0,		0,		$a->getProp('duration') );

			$event->setProperty( 'summary', $serviceTitle );

			$appDescription = '';
			if( NTS_ENABLE_TIMEZONES >= 0 ){
				$appDescription .= M('Times shown in [b]{TIME_ZONE}[/b] time zone', array('TIME_ZONE' => ntsTime::timezoneTitle($this->timezone) ));
				}
			$appDescription .= "\n" . $appTitle;
			
			if( ! NTS_SINGLE_RESOURCE )
				$appDescription .= "\n" . M('Bookable Resource') . ': ' . $resource->getProp('title');
			$appDescription .= "\n" . M('Customer') . ': ' . $customer->getProp('first_name') . ' ' . $customer->getProp('last_name');

			$event->setProperty( 'description', $appDescription );
			$event->setProperty( 'location', ntsView::objectTitle($location) );

			$event->setProperty( 'attendee', $customer->getProp('first_name') . ' ' . $customer->getProp('last_name') . ' <' . $customer->getProp('email') . '>'  );
//			$event->setProperty( 'organizer', $resource->getProp('title') );

			$cal->addComponent( $event );
			ntsObjectFactory::clearCache( 'appointment', $appId );
			}

		$return = $cal->createCalendar();                   // generate and get output in string
		return $return;
		}
	}
?>