<?php
class ntsResource extends ntsObject {
	function ntsResource(){
		parent::ntsObject( 'resource' );
		}

	function getAdmins( $manageOnly = false ){
		$myId = $this->getId();
		$scheduleAdmins = array();
		$appsAdmins = array();

		$uif =& ntsUserIntegratorFactory::getInstance();
		$integrator =& $uif->getIntegrator();

		$admins = $integrator->getUsers( array('_role' => '="admin"') );
		if( $admins ){
			reset( $admins );
			foreach( $admins as $ai ){
				$admin = new ntsUser;
				$admin->setId( $ai['id'] );

				$schedules = $admin->getProp( '_resource_schedules' );
				reset( $schedules );
				foreach( $schedules as $resId => $access ){
					if( ($resId == $myId) && ($access != 'none') ){
						if( (! $manageOnly) || ($access == 'edit') )
							$scheduleAdmins[ $ai['id'] ] = $access;
						}
					}

				$apps = $admin->getProp( '_resource_apps' );
				reset( $apps );
				foreach( $apps as $resId => $access ){
					if( ($resId == $myId) && ($access != 'none') ){
						if( (! $manageOnly) || ($access == 'manage') )
							$appsAdmins[ $ai['id'] ] = $access;
						}
					}
				}
			}

		$return = array( $appsAdmins, $scheduleAdmins );
		return $return;
		}
	}
?>