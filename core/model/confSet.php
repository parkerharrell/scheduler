<?php
switch( $name ){
	case 'appointmentFlow':
		if( ! $value )
			$return = '';
		else {
			$value2 = array();
			reset( $value );
			foreach( $value as $v ){
				$value2[] = join( ':', $v );
				}
			$return = join( '|', $value2 );
			}
		break;

	case 'emailDebug':
		$return = ( $value ) ? 1 : 0;
		break;

	case 'priceFormat':
		$return = join( '||', $value );
		break;

	case 'languages':
		if( ! $value )
			$return = 'en-builtin';
		else {
			$return = join( '||', $value );
			}
		break;

	case 'paymentGateways':
		if( ! $value )
			$return = '';
		else {
			$return = join( '||', $value );
			}
		break;

	case 'plugins':
		if( ! $value )
			$return = '';
		else {
			$return = join( '||', $value );
			}
		break;
	}
?>