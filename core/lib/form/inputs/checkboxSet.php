<?php
switch( $inputAction ){
	case 'display':
		if( ! isset($conf['value']) )
			$conf['value'] = array();
		if( ! is_array($conf['value']) )
			$conf['value'] = array( $conf['value'] );

		reset( $conf['options'] );
		foreach( $conf['options'] as $o ){
			if( isset($conf['attr']['separator_before']) )
				$input .= $conf['attr']['separator_before'];

			$checked = in_array($o[0], $conf['value']) ? true : false;
			$input .= $this->makeInput(
				'checkbox',
				array(
					'id'		=> $conf['id'] . '[]',
					'box_value'	=> $o[0],
					'value'		=> $checked,
					)
				);
			$input .= '' . $o[1] . ' ';

			if( isset($conf['attr']['separator_after']) )
				$input .= $conf['attr']['separator_after'];
			}
		break;

	case 'submit':
		$input = $req->getParam( $handle );
		if( ! $input )
			$input = array();
		break;

	case 'check_submit':
		$input = true;
		break;
	}
?>