<?php
switch( $validatorAction ){
	case 'display':
		$validatorSkip = true;
		break;
	default:
		$ntsdb =& dbWrapper::getInstance();
		$propName = $validationParams['prop'];
		$className = $validationParams['class'];
		$om =& objectMapper::getInstance();
		$tblName = $om->getTableForClass( $className );

		$where = array();
		$where[$propName] = " = \"$checkValue\"";

		if( isset($formValues['id']) && ($formValues['id'] > 0) ){
			$myId = $formValues['id'];
			$where['id'] = " <> $myId";
			}

		if( isset($validationParams['skipMe']) && $validationParams['skipMe'] ){
			if( isset($formParams['myId']) && $formParams['myId'] ){
				$myId = $formParams['myId'];
				$where['id'] = " <> $myId";
				}
			}

		if( isset($validationParams['also']) && $validationParams['also'] ){
			reset($validationParams['also']);
			foreach( $validationParams['also'] as $k => $v )
				$where[$k] = $v;
			}

	/* build where */
		$parts = array();
		reset( $where );
		foreach( $where as $key => $value )
			$parts[] = $key . $value;
		$whereString = join( ' AND ', $parts );

		$sql = "SELECT COUNT(*) AS count FROM {PRFX}$tblName WHERE $whereString";
		$result = $ntsdb->runQuery( $sql );
		if( $i = $result->fetch() ){
			if( $i['count'] )
				$validationFailed = 1;
			}
		break;
	}
?>