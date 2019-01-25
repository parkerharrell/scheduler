<?php
switch( $action ){
	case 'start':
		$ff =& ntsFormFactory::getInstance();
		$conf =& ntsConf::getInstance();

		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate($req) ){
			$formValues = $form->getValues();
			reset( $formValues );
			$searchParams = array();
			foreach( $formValues as $key => $value ){
				$value = trim( $value );
				if( $value ){
					$searchParams[ $key ] = $value;
					}
				}

		/* continue to search results */
			$forwardTo = ntsLink::makeLink( '-current-/..', 'search', $searchParams );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
		/* form not valid, continue to create form */
			}

		break;
	default:
		break;
	}
?>