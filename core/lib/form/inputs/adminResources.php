<?php
$allCount = 0;
$inputAddons = array( 'appointments', 'schedules' );
$ntsDynaList_AllowEmpty = true;

switch( $inputAction ){
	case 'display':
		$ntsdb =& dbWrapper::getInstance();
		/* resources */
		$sql =<<<EOT
		SELECT
			id
		FROM
			{PRFX}resources
		ORDER BY
			title ASC
EOT;

		if( ! $conf['value'] )
			$conf['value'] = array();

		$currentResIds = array_keys( $conf['value'] );

		$options = array();
		$result = $ntsdb->runQuery( $sql );
		$allCount = 0;
		if( $result ){
			while( $i = $result->fetch() ){
				$allCount++;
				if( ! in_array($i['id'], $currentResIds) )
					continue;

				$e = ntsObjectFactory::get('resource');
				$e->setId( $i['id'] );
				$moreValues = array();
				if( isset($conf['moreValue']) ){
					$moreValues = isset( $conf['moreValue'][$e->getId()] ) ? $conf['moreValue'][$e->getId()] : array();
					}

				if( $moreValues )
					$options[] = array($e->getId(), ntsView::objectTitle($e), $moreValues );
				else
					$options[] = array($e->getId(), ntsView::objectTitle($e) );
				}
			}

		$conf['options'] = $options;

		$accessOptions = array(
			array( 'none', M('No Access') ),
			array( 'view', M('View') ),
			array( 'edit', M('View and Update') ),
			);
		$accessOptions2 = array(
			array( 'none', M('No Access') ),
			array( 'view', M('View') ),
			array( 'edit', M('View and Update') ),
			array( 'manage', M('Manage') ),
			);

		$appointmentsAccess = $this->makeInput (
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> $conf['id'] . '_appointments_{OPTION_ID}',
				'options'	=> $accessOptions2,
				),
		/* validators */
			array(
				)
			);
		$schedulesAccess = $this->makeInput (
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> $conf['id'] . '_schedules_{OPTION_ID}',
				'options'	=> $accessOptions,
				),
		/* validators */
			array(
				)
			);

		$rowAddon = "'<td>" . addslashes( $appointmentsAccess ) . "</td>" . "<td>" . addslashes( $schedulesAccess ) . "</td>'";
		$headerAddon = '<tr><th></th><th>' . M('Appointments') . '</th><th>' . M('Schedules') . '</th><th></th></tr>';
		break;
	}

/* handle action by default control */
$allItemsCount = $allCount;
$newItemsUrl = ntsLink::makeLink('admin/ajax/resources');

require( NTS_BASE_DIR . '/lib/form/inputs/dynamicList.php' );
?>