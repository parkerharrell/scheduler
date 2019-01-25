<?php
$allCount = 0;
switch( $inputAction ){
	case 'display':
		$ntsdb =& dbWrapper::getInstance();

		/* locations */
		$sql =<<<EOT
		SELECT
			id
		FROM
			{PRFX}locations
		ORDER BY
			title ASC
EOT;

		if( ! $conf['value'] )
			$conf['value'] = array();
		if( ! is_array($conf['value']) )
			$conf['value'] = array( $conf['value'] );

		$locationsOptions = array();
		$result = $ntsdb->runQuery( $sql );
		$allCount = 0;
		if( $result ){
			while( $i = $result->fetch() ){
				$allCount++;
				if( ! in_array($i['id'], $conf['value']) )
					continue;

				$e = ntsObjectFactory::get('location');
				$e->setId( $i['id'] );
				$moreValues = array();
				if( isset($conf['moreValue']) ){
					$moreValues = isset( $conf['moreValue'][$e->getId()] ) ? $conf['moreValue'][$e->getId()] : array();
					}

				if( $moreValues )
					$locationsOptions[] = array($e->getId(), ntsView::objectTitle($e), $moreValues );
				else
					$locationsOptions[] = array($e->getId(), ntsView::objectTitle($e) );
				}
			}

		$conf['options'] = $locationsOptions;
		break;
	}

/* handle action by default control */
$allItemsCount = $allCount;
$newItemsUrl = ntsLink::makeLink('admin/ajax/locations');

//$rowAddon = "'lala'";

require( NTS_BASE_DIR . '/lib/form/inputs/dynamicList.php' );
?>