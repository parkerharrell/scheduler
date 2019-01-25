<?php
include_once( NTS_BASE_DIR . '/lib/ntsObjectMapper.php' );

class objectMapper extends ntsObjectMapper {
	function objectMapper(){
		$conf =& ntsConf::getInstance();

		parent::ntsObjectMapper();
	/* registerProp: (className, pName, isCore, isArray, default) */

		$this->registerClass( 'user', 'users' );
		if( ! NTS_EMAIL_AS_USERNAME )
			$this->registerProp( 'user',	'username' );
		$this->registerProp( 'user',	'email' );
		$this->registerProp( 'user',	'password' );
		$this->registerProp( 'user',	'first_name' );
		$this->registerProp( 'user',	'last_name' );
		$this->registerProp( 'user',	'lang' );
		$this->registerProp( 'user',	'created' );
	/* meta */
		$this->registerProp( 'user',	'_role',				false,	1,	array('customer') );
		$this->registerProp( 'user',	'_disabled_panels',		false,	1,	array() );
		$this->registerProp( 'user',	'_resource_schedules',	false,	2,	array() ); // array( '2' => 'edit', '1' => 'view', '4' => 'none' );
		$this->registerProp( 'user',	'_resource_apps',		false,	2,	array() );

		$this->registerProp( 'user',	'_restriction',	false,	1,	array() );
		$this->registerProp( 'user',	'_timezone',	false,	0,	$conf->get('companyTimezone') );
		$this->registerProp( 'user',	'_auth_code',	false,	0,	'' );

		$this->registerClass( 'form', 'forms' );
		$this->registerProp( 'form',	'title' );
		$this->registerProp( 'form',	'class' );
		$this->registerProp( 'form',	'details' );

		$this->registerClass( 'form_control', 'form_controls' );
		$this->registerProp( 'form_control',	'form_id' );
		$this->registerProp( 'form_control',	'name' );
		$this->registerProp( 'form_control',	'type' );
		$this->registerProp( 'form_control',	'title' );
		$this->registerProp( 'form_control',	'description' );
		$this->registerProp( 'form_control',	'show_order' );
		$this->registerProp( 'form_control',	'ext_access' );
		$this->registerProp( 'form_control',	'attr' );
		$this->registerProp( 'form_control',	'validators' );
		$this->registerProp( 'form_control',	'default_value' );

		$this->registerClass( 'service', 'services' );
		$this->registerProp( 'service',	'title' );
		$this->registerProp( 'service',	'description' );
		$this->registerProp( 'service',	'recur_total', true, 0, 1 );
		$this->registerProp( 'service',	'recur_options', true, 0, 'd-2d-w' );
		$this->registerProp( 'service',	'min_from_now' );
		$this->registerProp( 'service',	'max_from_now' );
		$this->registerProp( 'service',	'min_cancel' );
		$this->registerProp( 'service',	'show_order' );
		$this->registerProp( 'service',	'pack_only', true, 0, 0 );
		$this->registerProp( 'service',	'class_type', true, 0, 0 );
		$this->registerProp( 'service',	'duration' );
		$this->registerProp( 'service',	'until_closed', true, 0, 0 );
		$this->registerProp( 'service',	'lead_in' );
		$this->registerProp( 'service',	'lead_out' );
		$this->registerProp( 'service',	'price' );
		$this->registerProp( 'service',	'return_url' );
//		$this->registerProp( 'service',	'appointment_types', false,	true, array() );
		$this->registerProp( 'service',	'allow_queue', true, 0, 0 );
		$this->registerProp( 'service',	'_permissions', false,	1,	array( 'group-1:allowed', 'group0:auto_confirm' ) );
		$this->registerProp( 'service',	'_form', false,	false,	0 );
		$this->registerProp( 'service',	'_service_cat', false,	1,	array() );
		$this->registerProp( 'service',	'_disable_gateway', false,	1,	array() );

		$this->registerClass( 'service_cat', 'service_cats' );
		$this->registerProp( 'service_cat',	'title' );
		$this->registerProp( 'service_cat',	'description' );
		$this->registerProp( 'service_cat',	'show_order' );

		$this->registerClass( 'location', 'locations' );
		$this->registerProp( 'location',	'title' );
		$this->registerProp( 'location',	'description' );
		$this->registerProp( 'location',	'show_order' );

		$this->registerClass( 'resource', 'resources' );
		$this->registerProp( 'resource',	'title' );
		$this->registerProp( 'resource',	'description' );
		$this->registerProp( 'resource',	'show_order' );
		$this->registerProp( 'resource',	'_restriction',	false,	1,	array() );

		$this->registerClass( 'schedule', 'schedules' );
		$this->registerProp( 'schedule',	'resource_id' );
		$this->registerProp( 'schedule',	'title' );
		$this->registerProp( 'schedule',	'valid_from' );
		$this->registerProp( 'schedule',	'valid_to' );
		$this->registerProp( 'schedule',	'capacity', true, 0, 1 );
		$this->registerProp( 'schedule',	'_location',	false,	1,	array() );
		$this->registerProp( 'schedule',	'_service',		false,	1,	array() );

		$this->registerClass( 'timeblock', 'timeblocks' );
		$this->registerProp( 'timeblock',	'starts_at' );
		$this->registerProp( 'timeblock',	'ends_at' );
		$this->registerProp( 'timeblock',	'schedule_id' );
		$this->registerProp( 'timeblock',	'selectable_every' );
		$this->registerProp( 'timeblock',	'selectable_fixed', true, 1, array() );
		$this->registerProp( 'timeblock',	'applied_on' );

		$this->registerClass( 'timeoff', 'timeoffs' );
		$this->registerProp( 'timeoff',	'location_id' );
		$this->registerProp( 'timeoff',	'resource_id' );
		$this->registerProp( 'timeoff',	'starts_at' );
		$this->registerProp( 'timeoff',	'ends_at' );
		$this->registerProp( 'timeoff',	'description' );

		$this->registerClass( 'appointment', 'appointments' );
		$this->registerProp( 'appointment',	'service_id' );
		$this->registerProp( 'appointment',	'resource_id' );
		$this->registerProp( 'appointment',	'customer_id' );
		$this->registerProp( 'appointment',	'location_id' );
		$this->registerProp( 'appointment',	'seats', true, 0, 1 );
		$this->registerProp( 'appointment',	'created_at' );
		$this->registerProp( 'appointment',	'starts_at' );
		$this->registerProp( 'appointment',	'duration' );
		$this->registerProp( 'appointment',	'lead_in' );
		$this->registerProp( 'appointment',	'lead_out' );
		$this->registerProp( 'appointment',	'until_closed', true, 0, 0 );
		$this->registerProp( 'appointment',	'approved' );
		$this->registerProp( 'appointment',	'no_show' );
		$this->registerProp( 'appointment',	'auth_code' );
		$this->registerProp( 'appointment',	'need_reminder', true, 0, 0 );
		$this->registerProp( 'appointment',	'price' );
		$this->registerProp( 'appointment',	'is_ghost', true, 0, 0 );
		$this->registerProp( 'appointment',	'cancelled', true, 0, 0 );
		$this->registerProp( 'appointment',	'ghost_last_access', true, 0, 0 );
		$this->registerProp( 'appointment',	'_invoice', false, 0, 0 );
		$this->registerProp( 'appointment',	'_pack', false, 0, 0 );

		$this->registerClass( 'invoice', 'invoices' );
		$this->registerProp( 'invoice',	'refno' );
		$this->registerProp( 'invoice',	'amount' );
		$this->registerProp( 'invoice',	'currency' );
		$this->registerProp( 'invoice',	'created_at' );

		$this->registerClass( 'payment', 'payments' );
		$this->registerProp( 'payment',	'invoice_id' );
		$this->registerProp( 'payment',	'paid_at' );
		$this->registerProp( 'payment',	'amount_gross' );
		$this->registerProp( 'payment',	'amount_net' );
		$this->registerProp( 'payment',	'currency' );
		$this->registerProp( 'payment',	'pgateway' );
		$this->registerProp( 'payment',	'pgateway_ref' );
		$this->registerProp( 'payment',	'pgateway_response' );

		$this->registerClass( 'pack', 'packs' );
		$this->registerProp( 'pack',	'title' );
		$this->registerProp( 'pack',	'description' );
		$this->registerProp( 'pack',	'show_order' );
		$this->registerProp( 'pack',	'discount' );
		$this->registerProp( 'pack',	'services' );
		$this->registerProp( 'pack',	'_disable_gateway', false,	1,	array() );
		}

	function makeTags_Appointment( $object, $access = 'external' ){
		$conf =& ntsConf::getInstance();
		$showSessionDuration = $conf->get('showSessionDuration');
		$enableTimezones = $conf->get('enableTimezones');

		$allInfo = '';

		/* time */
		$customerId = $object->getProp( 'customer_id' );
		$customer = new ntsUser();
		$customer->setId( $customerId );

		$resourceId = $object->getProp( 'resource_id' );
		$resource = ntsObjectFactory::get( 'resource' );
		$resource->setId( $resourceId );

		$ts = $object->getProp('starts_at');
		$t = new ntsTime( $ts );
		if( $access == 'external' )
			$t->setTimezone( $customer->getProp('_timezone') );

		$showTimezone = ( $enableTimezones == -1 ) ? 0 : 1;
		### Customized by RAH 5/23/11 - remove end time of appointment ###
		$timeFormatted = $t->formatDate() . ' at ' . $t->formatTime((($showSessionDuration) ? $object->getProp('duration') : false), $showTimezone);
		$tags[0][] = '{APPOINTMENT.STARTS_AT}';
		$tags[1][] = $timeFormatted;
		$allInfo .= $timeFormatted . "\n";

		/* service */
		$serviceView = ntsView::appServiceView( $object );

		$tags[0][] = '{APPOINTMENT.SERVICE}';
		$tags[1][] = $serviceView;
		$allInfo .= $serviceView . "\n";

	/* add service description */
		$service = new ntsObject( 'service' );
		$service->setId( $object->getProp('service_id') );
		$tags[0][] = '{APPOINTMENT.SERVICE.DESCRIPTION}';
		$tags[1][] = $service->getProp('description');

		$tags[0][] = '{APPOINTMENT.SEATS}';
		$tags[1][] = $object->getProp('seats');

		$priceView = ntsCurrency::formatServicePrice($object->getProp('price'));
		if( ! strlen($priceView) ){
			$priceView = M('N/A');
			}
		$tags[0][] = '{APPOINTMENT.PRICE}';
		$tags[1][] = $priceView;

	/* location */
		$locationId = $object->getProp( 'location_id' );
		$location = new ntsObject( 'location' );
		$location->setId( $locationId );
		$locationTitle = ntsView::objectTitle($location);
		$tags[0][] = '{APPOINTMENT.LOCATION}';
		$tags[1][] = $locationTitle;

		### Customized by RAH 5/11/11 - Allow location description to print on emails to show location description ###
		$tags[0][] = '{APPOINTMENT.LOCATION.DESCRIPTION}';
		$tags[1][] = $location->getProp('description');

		if( ! NTS_SINGLE_LOCATION ){
			$allInfo .= M('Location') . ': ' . $locationTitle . "\n";
			}

		/* resource */
		$resourceTitle = $resource->getProp('title');
		$tags[0][] = '{APPOINTMENT.RESOURCE}';
		$tags[1][] = $resourceTitle;
		$tags[0][] = '{APPOINTMENT.RESOURCE.DESCRIPTION}';
		$tags[1][] = $resource->getProp('description');

		$allInfo .= M('Bookable Resource') . ': ' . $resourceTitle . "\n";

		/* customer */
		if( $access == 'external' ){
			$fields = $this->getFields( 'customer', 'external' );
			}
		else {
			$fields = $this->getFields( 'customer', 'internal' );
			}

		$customerId = $object->getProp( 'customer_id' );
		$customer = new ntsUser();
		$customer->setId( $customerId );

		$allCustomerInfo = '';
		foreach( $fields as $f ){
			$value = $customer->getProp( $f[0] );
			if( $f[2] == 'checkbox' ){
				$value = $value ? M('Yes') : M('No');
				}

			$tags[0][] = '{APPOINTMENT.CUSTOMER.' . strtoupper($f[0]) . '}';
			$tags[1][] = $value;

			$allCustomerInfo .= M($f[1]) . ': ' . $value . "\n";
			}
		$tags[0][] = '{APPOINTMENT.CUSTOMER.-ALL-}';
		$tags[1][] = $allCustomerInfo;

		/* custom fields */
		$om =& objectMapper::getInstance();
		$otherDetails = array(
			'service_id'	=> $object->getProp('service_id'),
			);

		$fields = $om->getFields( 'appointment', $access, false, $otherDetails );
		reset( $fields );
		foreach( $fields as $fArray ){
			$value = $object->getProp($fArray[0]);
			if( $fArray[2] == 'checkbox' ){
				$value = $value ? M('Yes') : M('No');
				}

			$c = $this->getControl( 'appointment', $fArray[0], false );
			if( $c[2]['description'] ){
				$value .= ' (' . $c[2]['description'] . ')';
				}

			$allInfo .= $fArray[1] . ': ' . $value . "\n";
			}

		$tags[0][] = '{APPOINTMENT.-ALL-}';
		$tags[1][] = $allInfo;

		return $tags;
		}

	// Singleton stuff
	function &getInstance(){
		return ntsLib::singletonFunction( 'objectMapper' );
		}
	}
?>

