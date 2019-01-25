<?php
switch( $inputAction ){
	case 'display':
		if( $conf['value'] ){
			$conf['value'] = join( "\n", $conf['value'] );
			}
		else
			$conf['value'] = '';
		break;
	}

require( dirname(__FILE__) . '/textarea.php' );

switch( $inputAction ){
	case 'submit':
		$options = explode( "\n", $input );
		$input = array();
		reset( $options );
		foreach( $options as $o ){
			$o = trim( $o );
			$input[] = $o;
			}
		break;
	}
?>