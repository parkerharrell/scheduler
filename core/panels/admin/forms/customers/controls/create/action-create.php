<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

$ntsdb =& dbWrapper::getInstance();
/* form id */
$sql =<<<EOT
	SELECT
		*
	FROM
		{PRFX}forms
	WHERE
		class = 'customer'
	LIMIT 1
EOT;
$result = $ntsdb->runQuery( $sql );
$formInfo = $result->fetch();
$formId = $formInfo['id'];

$formParams = array(
	'form_id'	=> $formId,
	);

$ff =& ntsFormFactory::getInstance();
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

/* class name */
	$object->setProp( 'class', 'customer' );
/* form id */
	$sql =<<<EOT
		SELECT
			*
		FROM
			{PRFX}forms
		WHERE
			class = 'customer'
		LIMIT 1
EOT;
	$result = $ntsdb->runQuery( $sql );
	$formInfo = $result->fetch();
	$formId = $formInfo['id'];
	$object->setProp( 'form_id', $formId );

	$cm->runCommand( $object, 'create' );
	if( $cm->isOk() ){
		ntsView::setAnnounce( M('Form Field') . ': ' . M('Created'), 'ok' );

		$id = $object->getId();
	/* continue to the list with anouncement */
		$forwardTo = ntsLink::makeLink( '-current-/../edit', '', array('id' => $id ) );
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