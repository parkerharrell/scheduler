<?php
$allCount = 0;
$inputAddons = array( 'appointments', 'schedules' );
$ntsDynaList_AllowEmpty = true;

switch( $inputAction ){
	case 'display':
		/* admins */
		$uif =& ntsUserIntegratorFactory::getInstance();
		$integrator =& $uif->getIntegrator();
		$admins = $integrator->getUsers( array('_role' => '="admin"') );

		if( ! $conf['value'] )
			$conf['value'] = array();

		$currentAdminIds = array_keys( $conf['value'] );
		$options = array();

		$allCount = 0;
		foreach( $admins as $i ){
			$allCount++;
			if( ! in_array($i['id'], $currentAdminIds) )
				continue;

			$e = new ntsUser;
			$e->setId( $i['id'] );
			$moreValues = array();
			if( isset($conf['moreValue']) ){
				$moreValues = isset( $conf['moreValue'][$e->getId()] ) ? $conf['moreValue'][$e->getId()] : array();
				}

			$adminFullName = trim( ntsView::objectTitle($e) );
			$adminTitle = '<b>' . $e->getProp('username') . '</b>';
			if( $adminFullName ){
				$adminTitle .= ' (' . $adminFullName . ')';
				}

			if( $moreValues )
				$options[] = array($e->getId(), $adminTitle, $moreValues );
			else
				$options[] = array($e->getId(), $adminTitle );
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
$newItemsUrl = ntsLink::makeLink('admin/ajax/admins');

require( NTS_BASE_DIR . '/lib/form/inputs/dynamicList.php' );
?>