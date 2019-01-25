<?php
global $NTS_VIEW;
/* redefine display file */
$NTS_VIEW['displayFile'] = dirname(__FILE__) . '/index.php';

$ntsdb =& dbWrapper::getInstance();

if( ! isset($id) ){
	$id = $req->getParam( '_id' );
	}
if( ! is_array($id) ){
	$id = array( $id );
	}

/* customer itself */
$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

switch( $action ){
	case 'delete':
		$cm =& ntsCommandManager::getInstance();
		$deletedCount = 0;
		$failedCount = 0;
		reset( $id );
		foreach( $id as $i ){
			$object = new ntsUser();
			$object->setId( $i );

			$cm->runCommand( $object, 'delete' );

			$actionOk = true;
			if( $cm->isOk() ){
				$deletedCount++;
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				$failedCount++;
				$actionOk = false;
				}
			}

		if( $deletedCount ){
			$msg = $deletedCount . ' ' . M('Users') . ': ' . M('Deleted');
			ntsView::addAnnounce( $msg, 'ok' );
			}

	/* continue to users list with anouncement */
		if( ! isset($forwardTo) ){
			$forwardTo = ntsLink::makeLink( '-current-/../../browse' );
			}

		ntsView::redirect( $forwardTo );
		exit;
		break;

	default:
	/* count appointments of this customer */
		$idCondition = 'IN (' . join( ',', $id ). ')';
		$sql = "SELECT COUNT(*) AS count FROM {PRFX}appointments WHERE customer_id $idCondition";
		$result = $ntsdb->runQuery( $sql );
		$o2 = $result->fetch();
		$customerAppointmentsCount = $o2['count'];

		$NTS_VIEW['id'] = $id;
		$NTS_VIEW['customerAppointmentsCount'] = $customerAppointmentsCount;

		$formParams = array(
			'id'		=> $id,
			);
		if( isset($forwardTo) ){
			$formParams['forwardTo'] = $forwardTo;
			}
		else
			$formParams['forwardTo'] = '';

		$ff =& ntsFormFactory::getInstance();
		$confirmForm =& $ff->makeForm( dirname(__FILE__) . '/confirmForm', $formParams );

		break;
	}
?>