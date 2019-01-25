<?php
$allCount = 0;
switch( $inputAction ){
	case 'display':
		$ntsdb =& dbWrapper::getInstance();

		/* services */
		$sql =<<<EOT
		SELECT
			id
		FROM
			{PRFX}services
		ORDER BY
			show_order ASC
EOT;

		if( ! $conf['value'] )
			$conf['value'] = array();
		if( ! is_array($conf['value']) )
			$conf['value'] = array( $conf['value'] );

		$servicesOptions = array();
		$result = $ntsdb->runQuery( $sql );
		$allCount = 0;
		if( $result ){
			while( $i = $result->fetch() ){
				$allCount++;
				if( ! in_array($i['id'], $conf['value']) )
					continue;

				$e = ntsObjectFactory::get('service');
				$e->setId( $i['id'] );
				$moreValues = array();
				if( isset($conf['moreValue']) ){
					$moreValues = isset( $conf['moreValue'][$e->getId()] ) ? $conf['moreValue'][$e->getId()] : array();
					}

				if( $moreValues )
					$servicesOptions[] = array($e->getId(), ntsView::objectTitle($e), $moreValues );
				else
					$servicesOptions[] = array($e->getId(), ntsView::objectTitle($e) );
				}
			}

		$conf['options'] = $servicesOptions;
		break;
	}

/* handle action by default control */
$allItemsCount = $allCount;
$newItemsUrl = ntsLink::makeLink('admin/ajax/services');
require( NTS_BASE_DIR . '/lib/form/inputs/dynamicList.php' );
?>