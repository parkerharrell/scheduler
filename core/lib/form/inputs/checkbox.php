<?php
switch( $inputAction ){
	case 'display':
		$input .= '<INPUT TYPE="checkbox" ID="' . $conf['htmlId'] . '" NAME="' . $conf['id'] . '"';
		$inputParams = $this->_makeInputParams( $conf['attr'] );
		if( $inputParams )
			$input .= ' ' . $inputParams;
		if( $conf['value'] )
			$input .= ' CHECKED';
		if( isset($conf['box_value']) )
			$input .= ' VALUE="' . $conf['box_value'] . '"';
		if( isset($conf['readonly']) && $conf['readonly'] )
			$input .= ' READONLY DISABLED CLASS="readonly"';
		$input .= '>';
		break;

	case 'submit':
		$input = ( $req->getParam($handle) ) ? 1 : 0;
		break;

	case 'check_submit':
		$input = true;
		break;
	}
?>