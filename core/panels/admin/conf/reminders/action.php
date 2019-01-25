<?php
$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();

$params = array(
	'remindBefore',
	);

switch( $action ){
	case 'update':
		$ff =& ntsFormFactory::getInstance();

		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate($req) ){
			$formValues = $form->getValues();

			reset( $params );
			foreach( $params as $p ){
				$newValue = $conf->set( $p, $formValues[$p] );
				$sql = $conf->getSaveSql( $p, $newValue );
				$result = $ntsdb->runQuery( $sql );
				}

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
		$default = array();
		reset( $params );
		foreach( $params as $p ){
			$default[ $p ] = $conf->get( $p );
			}

		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, $default );
		break;
	}
?>