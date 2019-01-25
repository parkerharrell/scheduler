<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

/* form info */
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

$id = $formInfo['id'];

$NTS_VIEW['fields'] = array();
/* fields info */
$sql =<<<EOT
SELECT
	*
FROM
	{PRFX}form_controls
WHERE
	form_id = $id
ORDER BY
	show_order ASC
EOT;
$result = $ntsdb->runQuery( $sql );
while( $c = $result->fetch() ){
	$NTS_VIEW['fields'][] = $c;
	}

switch( $action ){
	case 'update':
		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, array('id' => $id) );

		if( $form->validate($req) ){
			$formValues = $form->getValues();

		/* update customer */
			$object = new ntsObject( 'form' );
			$object->setByArray( $formValues );
			$object->setId( $id );

			$cm->runCommand( $object, 'update' );
			if( $cm->isOk() ){
				ntsView::setAnnounce( M('Form') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-', '', array('id' => $id ) );
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
		break;

	case 'control_up':
		$controlId = $req->getParam('control');
		$object = new ntsObject( 'form_control' );
		$object->setId( $controlId );

		$cm->runCommand( $object, 'move_up' );
		if( $cm->isOk() ){
			ntsView::setAnnounce( M('Moved Up'), 'ok' );
			}
		else {
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );
			}
	/* continue to the list with anouncement */
		$forwardTo = ntsLink::makeLink( '-current-' );
		ntsView::redirect( $forwardTo );
		exit;
		break;

	case 'control_down':
		$controlId = $req->getParam('control');
		$object = new ntsObject( 'form_control' );
		$object->setId( $controlId );

		$cm->runCommand( $object, 'move_down' );
		if( $cm->isOk() ){
			ntsView::setAnnounce( M('Moved Down'), 'ok' );
			}
		else {
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );
			}
	/* continue to the list with anouncement */
		$forwardTo = ntsLink::makeLink( '-current-' );
		ntsView::redirect( $forwardTo );
		exit;
		break;
	}

if( ! isset($form) ){
	$formFile = dirname( __FILE__ ) . '/form';
	$form =& $ff->makeForm( $formFile, $formInfo );
	}
?>