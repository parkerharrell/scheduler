<?php
global $NTS_CURRENT_PANEL, $NTS_REQUESTED_PANEL, $NTS_REQUESTED_ACTION;

/* init menu - in this file $NTS_CURRENT_PANEL may be redefined to go to the first 2nd level menu item */
require( NTS_BASE_DIR . '/panels/initMenu.php' );

/* action and display files */
$actionFiles = array();
$filterFiles = array();
$realPanelDirs = array();
if( $NTS_REQUESTED_ACTION ){
	reset( $NTS_CORE_DIRS );
	foreach( $NTS_CORE_DIRS as $rcd )
		$actionFiles[] = $rcd . '/panels/' . $NTS_CURRENT_PANEL . '/action-' . $NTS_REQUESTED_ACTION . '.php';
	}
reset( $NTS_CORE_DIRS );
foreach( $NTS_CORE_DIRS as $rcd ){
	$actionFiles[] = $rcd . '/panels/' . $NTS_CURRENT_PANEL . '/action.php'; 
	$filterFiles[] = $rcd . '/panels/' . $NTS_CURRENT_PANEL . '/filter.php'; 
	$realPanelDirs[] = $rcd . '/panels/' . $NTS_CURRENT_PANEL;
	}

$actionError = false;
/* if this panel and action is disabled */
$disabledPanels = isset($GLOBALS['DISABLED_PANELS']) ? $GLOBALS['DISABLED_PANELS'] : array();
if( $NTS_REQUESTED_ACTION )
	$checkPanel = $NTS_CURRENT_PANEL . '::' . $NTS_REQUESTED_ACTION;
else
	$checkPanel = $NTS_CURRENT_PANEL;

if( in_array($checkPanel, $disabledPanels) ){
	ntsView::setAnnounce( 'This action is disabled', 'error' );
	}
else {
/* filter action */
	reset( $filterFiles );
	foreach( $filterFiles as $filterFile ){
		if( file_exists($filterFile) ){
			require( $filterFile );
			break;
			}
		}

/* handle action */
	reset( $actionFiles );
	foreach( $actionFiles as $actionFile ){
		if( file_exists($actionFile) ){
			require( $actionFile );
			break;
			}
		}
	}
?>