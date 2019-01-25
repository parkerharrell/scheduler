<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

$formId = $req->getParam( 'formId' );
$formParams = array(
	'form_id'	=> $formId,
	);

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formParams );

if( $form->validate($req) ){
	$formValues = $form->getValues();
	reset( $formValues );

/* attributes */
	$prefix = 'attr-';
	$attr = array();
	reset( $formValues );
	foreach( $formValues as $k => $v ){
		if( substr($k, 0, strlen($prefix)) == $prefix ){
			$shortName = substr($k, strlen($prefix) );
			$attr[ $shortName ] = $v;
			}
		}
	$formValues['attr'] = $attr;

	if( isset($formValues['default_value-' . $formValues['type']]) ){
		$formValues['default_value'] = $formValues['default_value-' . $formValues['type']];
		}

/* create field */
	$object = new ntsObject( 'form_control' );
	$object->setByArray( $formValues );
	$object->setProp( 'form_id', $formId );

	$cm->runCommand( $object, 'create' );
	if( $cm->isOk() ){
		ntsView::setAnnounce( M('Form Field') . ': ' . M('Created'), 'ok' );

		$id = $object->getId();
	/* continue to the list with anouncement */
		$forwardTo = ntsLink::makeLink( '-current-/../', '', array('_id' => $formId ) );
		ntsView::redirect( $forwardTo );
		exit;
		}
	else {
		$errorText = $cm->printActionErrors();
		ntsView::addAnnounce( $errorText, 'error' );
		}
	}
else {
/* form not valid, continue to edit form */
	}
?>