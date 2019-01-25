<?php
global $NTS_READ_ONLY;
$id = $req->getParam( '_id' );
if( ! is_array($id) ){
	if( ! $NTS_READ_ONLY ){
		$object = ntsObjectFactory::get( 'appointment' );
		$object->setId( $id );

		if( (! $object->getProp('cancelled')) ){
			$title = M('Change');
			$sequence = 3;

			global $PANEL_PREFIX;
			if( $PANEL_PREFIX )
				$targetPanel = $PANEL_PREFIX . '/manage';
			else
				$targetPanel = 'admin/appointments/manage';

			if( isset($id) ){
				$viewMode = isset($_REQUEST['viewMode']) ? $_REQUEST['viewMode'] : '';
				$startsAt = $object->getProp( 'starts_at' );
				$t = new ntsTime;
				$t->setTimestamp( $startsAt );
				$appDate = $t->formatDate_Db();
				$url = ntsLink::makeLink( $targetPanel, '', array('reschedule' => $id, 'viewMode' => 0, 'viewStyle' => 'week', 'cal' => $appDate) );
				if( $viewMode == 'inline' )
					$directLink = "javascript: parent.location.href='$url';";
				else
					$directLink = $url;
				}
			}
		}
	}
?>