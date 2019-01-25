<?php
switch( $inputAction ){
	case 'display':
		$input .= '<INPUT TYPE="hidden" ID="' . $conf['id'] . '" NAME="' . $conf['id'] . '"' . ' VALUE="' . $conf['value'] . '">';
		break;

	case 'submit':
		$input = $req->getParam( $handle );
		break;

	case 'check_submit':
		$input = isset( $_POST[$handle] ) ? true : false;
		break;
	}
?>