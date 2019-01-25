<?php
include_once( dirname(__FILE__) . '/ntsForm.php' );

class ntsFormFactory {
	function ntsFormFactory(){
		$this->forms = array();
		}

	function &makeForm( $formFile, $defaults = array(), $key = '' ){
		$index = ( $key ) ? $formFile . $key : $formFile;	
		if( ! isset($this->forms[$index]) ){
			$this->forms[$index] = new ntsForm( $formFile, $defaults );
			}
		return $this->forms[$index];
		}

	// Singleton stuff
	function &getInstance(){
		return ntsLib::singletonFunction( 'ntsFormFactory' );
		}
	}
?>