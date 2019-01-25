<?php
class ntsUser extends ntsObject {
	function ntsUser(){
		parent::ntsObject( 'user' );
		}

	function setId( $id, $load = true ){
		if( $id == -111 ){
			$this->id = $id;
			$this->setProp( '_role', array('admin') );
			$this->setProp( 'username', '-superadmin-' );
			$this->setProp( 'first_name', '-superadmin-' );

			global $NTS_CURRENT_VERSION_NUMBER;
			if( $NTS_CURRENT_VERSION_NUMBER >= 4500 ){
			// resource schedules
				$resApps = array();
				$resSchedules = array();
				$allResourcesIds = ntsObjectFactory::getAllIds( 'resource' );
				reset( $allResourcesIds );
				foreach( $allResourcesIds as $resId ){
					$resApps[ $resId ] = 'edit';
					$resSchedules[ $resId ] = 'edit';
					}
				$this->setProp( '_resource_apps', $resApps );
				$this->setProp( '_resource_schedules', $resSchedules );
				}
			return;
			}
		parent::setId( $id, $load );
		}

	function getProp( $pName ){
		$return = parent::getProp( $pName );

		switch( $pName ){
			case '_resource_apps':
			case '_resource_schedules':
				if( ! is_array($return) )
					$return = array();
				foreach( $return as $resId => $accLevel ){
					if( $accLevel == 'none' ){
						unset( $return[$resId] );
						}
					}
			break;
			}
		return $return;
		}

	function hasRole( $role ){
		if( ! is_array($role) )
			$role = array( $role );
		$myRoles = $this->getProp( '_role' );
		$return = array_intersect( $myRoles, $role ) ? true : false;
		return $return;
		}

	function getTimezone(){
		$return = $this->getProp('_timezone');
		if( $this->getId() == 0 ){
			if( isset($_SESSION['nts_timezone']) ){
				if( NTS_ENABLE_TIMEZONES > 0 )
					$return = $_SESSION['nts_timezone'];
				else
					unset( $_SESSION['nts_timezone'] );
				}
			}
		return $return;
		}

	function getPanelPermissions(){
		$return = array();
		$apn =& ntsAdminPermissionsManager::getInstance();
		$allPanels = $apn->getPanels();

		$disabledPanels = $this->getProp( '_disabled_panels' );
		foreach( $allPanels as $p ){
			if( ! in_array($p, $disabledPanels) )
				$return[] = $p;
			}
		return $return;
		}

	function isPanelDisabled( $checkPanel ){
		$return = false;
		$disabledPanels = $this->getProp( '_disabled_panels' );

		global $NTS_SKIP_PANELS;
		if( $NTS_SKIP_PANELS ){
			$disabledPanels = array_merge( $disabledPanels, $NTS_SKIP_PANELS );
			}

		reset( $disabledPanels );
		foreach( $disabledPanels as $dp ){
			if( substr($checkPanel, 0, strlen($dp)) == $dp ){
				// not allowed
				$return = true;
				break;
				}
			}
		return $return;
		}
	}
?>