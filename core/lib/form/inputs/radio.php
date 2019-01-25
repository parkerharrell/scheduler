<?php
switch( $inputAction ){
	case 'display':
//		_print_r( $conf );
		$input .= '<INPUT TYPE="radio" ID="' . $conf['id'] . '" NAME="' . $conf['id'] . '"';
		$input .= ' VALUE="' . htmlspecialchars( $conf['value'] ) . '"';
		$inputParams = $this->_makeInputParams( $conf['attr'] );
		if( $inputParams )
			$input .= ' ' . $inputParams;
		if( isset($conf['groupValue']) && ($conf['groupValue'] == $conf['value']) )
			$input .= ' CHECKED';
		if( isset($conf['readonly']) && $conf['readonly'] )
			$input .= ' READONLY DISABLED CLASS="readonly"';
		$input .= '>';
		break;

	case 'submit':
		$input = $req->getParam( $handle );
		break;

	case 'check_submit':
		$input = isset( $_POST[$handle] ) ? true : false;
		break;
	}
?>