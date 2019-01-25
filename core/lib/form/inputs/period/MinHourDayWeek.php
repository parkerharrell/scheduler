<?php
/* should be sorted desc */
$multiplier = array(
	array( 'w', 7 * 24 * 60 * 60 ),
	array( 'd', 24 * 60 * 60 ),
	array( 'h', 60 * 60 ),
	array( 'm', 60 ),	
	);

switch( $inputAction ){
	case 'display':
		$id_Unit = $conf['id'] . '_unit';
		$id_Qty = $conf['id'] . '_qty';

	/* find multiplier first */
		$qty = 1;
		$unit = $multiplier[0][0];
		reset( $multiplier );
		foreach( $multiplier as $ma ){
			if( $conf['value'] >= $ma[1] ){
				$unit = $ma[0];
				$qty = $conf['value'] / $ma[1];
				if( is_int($qty) )
					break;
				}
			}

	// QTY CONTROL
		$qtyConf = array(
			'id'		=> $id_Qty,
			'value'		=> $qty,
			'attr'	=> array(
				'size'	=> 2,
				),
			);
		$input .= $this->makeInput(
			'text',
			$qtyConf
			);

	// UNIT CONTROL
		$unitOptions = array(
			array( 'm', M('Minutes') ),
			array( 'h', M('Hours') ),
			array( 'd', M('Days') ),
			array( 'w', M('Weeks') ),
			);

		$unitConf = array(
			'id'	 	=> $id_Unit,
			'value'		=> $unit,
			'options'	=> $unitOptions,
			);
		$input .= $this->makeInput(
			'select',
			$unitConf
			);
		break;

	case 'submit':
		$id_Unit = $handle . '_unit';
		$id_Qty = $handle . '_qty';

		$submittedValue_Unit = $req->getParam( $id_Unit );
		$submittedValue_Qty = $req->getParam( $id_Qty );

		switch ( $submittedValue_Unit ){
			case 'm':
				$multiplier = 60;
				break;
			case 'h':
				$multiplier = 60 * 60;
				break;
			case 'd':
				$multiplier = 24 * 60 * 60;
				break;
			case 'w':
				$multiplier = 7 * 24 * 60 * 60;
				break;
			default:
				$multiplier = 1;
				break;
			}

//		$input = $submittedValue_Qty . $submittedValue_Unit;
		$input = $multiplier * $submittedValue_Qty;
		break;

	case 'validate':
		$id_Unit = $handle . '_unit';
		$id_Qty = $handle . '_qty';

		$submittedValue_Unit = $req->getParam( $id_Unit );
		$submittedValue_Qty = $req->getParam( $id_Qty );

		$validatorAction = 'validate';
	/* validate qty */
		$checkValue = $submittedValue_Qty;
		/* not empty */
		$validatorFile = 'notEmpty.php';
		$realCodeFile = ntsLib::fileInCoreDirs( '/lib/form/validators/' . $validatorFile );

		require( $realCodeFile );
		if( $validationFailed ){
			$validationError = M('Required field');
			return;
			}

		/* integer */
		$validatorFile = 'integer.php';
		$realCodeFile = ntsLib::fileInCoreDirs( '/lib/form/validators/' . $validatorFile );
		require( $realCodeFile );
		if( $validationFailed ){
			$validationError = M('Numbers only');
			return;
			}
		break;

	case 'check_submit':
		$id_Unit = $handle . '_unit';
		$id_Qty = $handle . '_qty';

		$input = isset( $_POST[$id_Unit] ) ? true : false;
		break;
	}
?>