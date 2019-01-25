<?php
switch( $inputAction ){
	case 'display':
		if( ! isset($conf['value']) || (! is_array($conf['value'])) )
			$conf['value'] = array();

		$count = count( $conf['options']['titles'] );
		for( $i = 0; $i < $count; $i++ ){
			if( isset($conf['attr']['separator_before']) )
				$input .= $conf['attr']['separator_before'];

			$input .= '<b>' . $conf['options']['titles'][$i] . '</b><br>';
			foreach( $conf['options']['options'][$i] as $o ){
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
				}

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