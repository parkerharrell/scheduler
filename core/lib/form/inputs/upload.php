<?php
switch( $inputAction ){
	case 'display':
		$input .= '<INPUT TYPE="file" ID="' . $conf['id'] . '" NAME="' . $conf['id'] . '"';
		$inputParams = $this->_makeInputParams( $conf['attr'] );
		if( $inputParams )
			$input .= ' ' . $inputParams;
		if( isset($conf['readonly']) && $conf['readonly'] )
			$input .= ' READONLY DISABLED CLASS="readonly"';
		$input .= '>';
		break;

	case 'submit':
	// returns the upload file temp name
		if( is_uploaded_file($_FILES[ $handle ]['tmp_name']) ){
			$input = $_FILES[ $handle ]['tmp_name'];

			$tmpName = $_FILES[$handle]['tmp_name'];
			$submittedName = $_FILES[$handle]['name'];
			$size = $_FILES[$handle]['size'];
			
			$input = $_FILES[$handle];
			}
		else {
			$input = false;
			}
		break;

	case 'check_submit':
		if( isset($_FILES[$handle]) )
			$input = true;
		else
			$input = false;
		break;
	}
?>