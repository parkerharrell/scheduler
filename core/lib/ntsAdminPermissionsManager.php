<?php
class ntsAdminPermissionsManager {
	var $keys = array();

	function ntsAdminPermissionsManager(){
		$this->keys = array(
			array( 'admin/customers', M('Customers'), 1 ),
				array( 'admin/customers/browse',	M('View') ),
				array( 'admin/customers/edit', 		M('Edit') ),
				array( 'admin/customers/create',	M('Create') ),
				array( 'admin/customers/newsletter',	M('Newsletter') ),
				array( 'admin/customers/settings',	M('Settings') ),

			array( 'admin/resources', M('Bookable Resources'), 1 ),
				array( 'admin/resources/browse',	M('View') ),
				array( 'admin/resources/edit', 		M('Edit') ),
				array( 'admin/resources/create',	M('Create') ),

			array( 'admin/services', M('Services'), 1 ),
				array( 'admin/services/browse',	M('View') ),
				array( 'admin/services/edit',	M('Edit') ),
				array( 'admin/services/create',	M('Create') ),
				array( 'admin/services/cats',	M('Categories') ),
				array( 'admin/services/packs',	M('Appointment Packs') ),

			array( 'admin/locations', M('Locations'), 1 ),
				array( 'admin/locations/browse',	M('View') ),
				array( 'admin/locations/edit', 		M('Edit') ),
				array( 'admin/locations/create',	M('Create') ),

			array( 'admin/staff', M('Administrative Users'), 1 ),
				array( 'admin/staff/browse',	M('View') ),
				array( 'admin/staff/edit', 		M('Edit') ),
				array( 'admin/staff/create',	M('Create') ),

			array( 'admin/forms', M('Forms'), 1 ),
				array( 'admin/forms', M('Manage') ),

			array( 'admin/conf', M('Settings'), 1 ),
				array( 'admin/conf/email_settings', M('Email') ),
				array( 'admin/conf/email_templates', M('Notifications') ),
				array( 'admin/conf/reminders', M('Reminders') ),
				array( 'admin/conf/terminology', M('Terminology') ),
				array( 'admin/conf/datetime', M('Date and Time') ),
				array( 'admin/conf/currency', M('Currency') ),
				array( 'admin/conf/payment_gateways', M('Payment Gateways') ),
				array( 'admin/conf/languages', M('Languages') ),
				array( 'admin/conf/flow', M('Appointment Flow') ),
				array( 'admin/conf/themes', M('Themes') ),
				array( 'admin/conf/plugins', M('Plugins') ),
				array( 'admin/conf/misc', M('Misc') ),
				array( 'admin/conf/upgrade', M('Info') ),
				array( 'admin/conf/backup', M('Backup') ),
			);

	/* add panels from plugins */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		reset( $activePlugins );
		foreach( $activePlugins as $plg ){
			$panels = $plm->getPanels( $plg );
			reset( $panels );
			foreach( $panels as $p ){
				$this->keys[] = $p;
				}
			}
		}

	function getPanels(){
		$return = array();
		reset( $this->keys );
		foreach( $this->keys as $ka ){
			if( isset($ka[2]) && $ka[2] )
				continue;
			$return[] = $ka[0];
			}
		return $return;
		}

	function getPanelsDetailed(){
		return $this->keys;
		}

	// Singleton stuff
	function &getInstance(){
		return ntsLib::singletonFunction( 'ntsAdminPermissionsManager' );
		}
	}
?>