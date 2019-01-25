<?php
$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();

switch( $action ){
	case 'reset':
		$result = $conf->reset( 'priceFormat' );

		if( $result ){
			ntsView::setAnnounce( M('Reset To Defaults'), 'ok' );

		/* continue to return options form */
			$forwardTo = ntsLink::makeLink( '-current-' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
			echo '<BR>Database error<BR>';
			}
		break;

	case 'update':
		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate($req) ){
			$formValues = $form->getValues();

		/* price format */
			$setting = explode( '||', $formValues['format'] );
			array_unshift( $setting, $formValues['sign-before'] );
			$setting[] = $formValues['sign-after'];

			$newValue = $conf->set( 'priceFormat', $setting );
			$sql = $conf->getSaveSql( 'priceFormat', $newValue );
			$result1 = $ntsdb->runQuery( $sql );

		/* currency */
			$setting = $formValues['currency'];
			$newValue = $conf->set( 'currency', $setting );
			$sql = $conf->getSaveSql( 'currency', $newValue );
			$result2 = $ntsdb->runQuery( $sql );

			$result = $result1 && $result2;

			if( $result ){
				ntsView::setAnnounce( M('Settings') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to delivery options form */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				echo '<BR>Database error:<BR>' . $ntsdb->getError() . '<BR>';
				}
			}
		else {
		/* form not valid, continue to create form */
			}

		break;
	default:
	/* price formatting */
		$confValue = $conf->get('priceFormat');
		$default = array(
			'sign-before'	=> $confValue[0],
			'format'		=> $confValue[1] . '||' . $confValue[2],
			'sign-after'	=> $confValue[3]
			);
	/* currency */
		$currency = $conf->get('currency');
		$default['currency'] = $currency;

		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, $default );
		break;
	}
?>