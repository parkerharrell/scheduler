<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();

/* default timeframe */
$t = new ntsTime();

$formParams = array(
	'status'	=> $req->getParam( 'status' ),
	'location'	=> $req->getParam( 'location' ),
	'resource'	=> $req->getParam( 'resource' ),
	'customer'	=> $req->getParam( 'customer' ),
	'service'	=> $req->getParam( 'service' ),
	);

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formParams );

switch( $action ){
	case 'search':
		if( $form->validate($req) ){
			$formValues = $form->getValues();
			$formValues['perpage'] = 'all';

			$forwardTo = ntsLink::makeLink( '-current-/../browse', '', $formValues );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
		/* form not valid, continue to create form */
			}		
		break;
	}
$NTS_VIEW['form'] = $form;
?>