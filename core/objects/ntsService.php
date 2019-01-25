<?php
class ntsService extends ntsObject {
	function ntsService(){
		parent::ntsObject( 'service' );
		}

	function getPaymentGateways(){
		$pgm =& ntsPaymentGatewaysManager::getInstance();
		$allGateways = $pgm->getActiveGateways();
		$disabledGateways = $this->getProp( '_disable_gateway' );

		$return = array();
		reset( $allGateways );
		foreach( $allGateways as $gw ){
			if( ! in_array($gw, $disabledGateways) )
				$return[] = $gw;
			}
		return $return;
		}

/* possible values - 'not_allowed, 'not_shown', 'allowed', 'auto_confirm' */
	function getPermissions(){
		$return = array(); 
		$rawPermissions = $this->getProp( '_permissions' );		

		reset( $rawPermissions );
		foreach( $rawPermissions as $ps ){
			list( $pk, $pv ) = explode( ':', $ps );
			$return[ $pk ] = $pv;
			}
		return $return;
		}

	function getPermissionsForGroup( $groupId ){
		$permissions = $this->getPermissions();
		$key = 'group' . $groupId;
		if( isset($permissions[$key]) )
			$return = $permissions[$key];
		else {
			echo "<br>Permissions for group id $groupId not defined!<br>";
			$return = 'not_allowed';
			}
		return $return;
		}
	}
?>