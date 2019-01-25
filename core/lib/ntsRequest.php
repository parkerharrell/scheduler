<?php
class ntsRequest {
	var $sanitizer;
	var $reset;

	function ntsRequest(){
		$this->sanitizer = array();
		$this->reset = array();
		}

	function addSanitizer( $param, $re ){
		$this->sanitizer[ $param ] = $re;
		}

	function resetParam( $pName ){
		$this->reset[] = $pName;
		}

	function getParam( $pName ){
		if( in_array($pName, $this->reset) )
			return null;

		$return = '';
		if( isset($_REQUEST[$pName]) ){
			$return = $_REQUEST[$pName];
			if( get_magic_quotes_gpc() ){
				if( ! is_array($return) )
					$return = stripslashes( $return );
				}
			}

	/* now check in sanitizer */
		if( isset($this->sanitizer[$pName]) ){
			$re = $this->sanitizer[$pName];
			if( is_array($return) ){
				reset( $return );
				foreach( $return as $r ){
					if( ! preg_match($re, $r) ){
					/* sanitizer failed */
						echo "invalid value for '$pName' detected";
						exit;
						}
					}
				}
			else {
				if( ! preg_match($re, $return) ){
				/* sanitizer failed */
					echo "invalid value for '$pName' detected";
					exit;
					}
				}
			}

		return $return;
		}

	function getGetParams(){
		$return = array();
		reset( $_GET );
		foreach( $_GET as $k => $v ){
			if( $k == NTS_PARAM_PANEL )
				continue;
			if( get_magic_quotes_gpc() )
				$v = stripslashes( $v );
			$return[ $k ] = $v;
			}
		return $return;
		}
	}
?>