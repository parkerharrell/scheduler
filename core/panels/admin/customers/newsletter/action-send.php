<?php
$ff =& ntsFormFactory::getInstance();
$conf =& ntsConf::getInstance();

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );

if( $form->validate($req) ){
	$formValues = $form->getValues();

	$sendTo = $formValues['send_to'];
	$subj = $formValues['subject']; 
	$msg = $formValues['text']; 

	$countUsers = $integrator->countUsers( array('_role' => '="' . $sendTo . '"') );

	if( ! $countUsers ){
		ntsView::addAnnounce( 'No users to send newsletter to', 'error' );

		$forwardTo = ntsLink::makeLink( '-current-' );
		ntsView::redirect( $forwardTo );
		exit;
		}
	else {
	/* store in session for running */
		$_SESSION['NTS_NEWSLETTER_SENDTO'] = $sendTo;
		$_SESSION['NTS_NEWSLETTER_SUBJ'] = $subj;
		$_SESSION['NTS_NEWSLETTER_MSG'] = $msg;

	/* redirect to run */
		$forwardTo = ntsLink::makeLink( '-current-', 'run', array('start' => 0, 'all' => $countUsers) );
		ntsView::redirect( $forwardTo );
		exit;
		}
	}
else {
/* form not valid, continue to create form */
	}

?>