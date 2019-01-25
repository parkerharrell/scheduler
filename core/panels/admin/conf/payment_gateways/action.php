<?php
$conf =& ntsConf::getInstance();
$ntsdb =& dbWrapper::getInstance();
$gtm =& ntsPaymentGatewaysManager::getInstance();

switch( $action ){
	case 'activate':
		$newGateway = $req->getParam( 'gateway' );
		$setting = $gtm->gatewayActivate( $newGateway );

		$newValue = $conf->set( 'paymentGateways', $setting );
		$sql = $conf->getSaveSql( 'paymentGateways', $newValue );
		$result = $ntsdb->runQuery( $sql );

		if( $result ){
			ntsView::setAnnounce( M('Payment Gateway') . ": <b>$newGateway</b>: " . M('Activate') . ': ' . M('OK'), 'ok' );
		/* continue to this page */
			$forwardTo = ntsLink::makeLink( '-current-' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
			echo '<BR>Database error:<BR>' . $ntsdb->getError() . '<BR>';
			}
		break;

	case 'disable':
		$disableGateway = $req->getParam( 'gateway' );
		$setting = $gtm->gatewayDisable( $disableGateway );

		$newValue = $conf->set( 'paymentGateways', $setting );
		$sql = $conf->getSaveSql( 'paymentGateways', $newValue );
		$result = $ntsdb->runQuery( $sql );

		if( $result ){
			ntsView::addAnnounce( M('Payment Gateway') .  ": <b>$disableGateway</b>: " . M('Disable') . ': ' . M('OK'), 'ok' );

		/* check if we have services left without payment gateways */
			$cm =& ntsCommandManager::getInstance();
			$services = ntsObjectFactory::getAll( 'service' );
			foreach( $services as $service ){
				$thisPaymentGateways = $service->getPaymentGateways();
				if( ! $thisPaymentGateways ){
					// automatically assign the first one
					$disabledGateways = $service->getProp( '_disable_gateway' );
					$newDisabledGateways = array();
					foreach( $disabledGateways as $dg ){
						if( $dg == $setting[0] )
							continue;
						$newDisabledGateways[] = $dg;
						}
					$service->setProp( '_disable_gateway', $newDisabledGateways );
					$cm->runCommand( $service, 'update' );
					ntsView::addAnnounce( '<b>' . $setting[0] . '</b> Payment Gateway Added To Service: <b>' . ntsView::objectTitle($service) . '</b>', 'ok' );
					}
				}

			$allowedCurrencies = $gtm->getActiveCurrencies();
			$currentCurrency = $conf->get('currency');
			if( ! in_array($currentCurrency, $allowedCurrencies) ){
				$result2 = $conf->reset( 'currency' );
				/* reset currency as well */
				ntsView::addAnnounce( 'Currency Reset To USD', 'ok' );
				$forwardTo = ntsLink::makeLink( '-current-/../currency' );
				}
			else {
			/* continue to this page */
				$forwardTo = ntsLink::makeLink( '-current-' );
				}
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
			echo '<BR>Database error:<BR>' . $ntsdb->getError() . '<BR>';
			}
		break;

	default:
		break;
	}
?>