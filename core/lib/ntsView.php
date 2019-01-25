<?php
class ntsLink {
	var $target = NTS_ROOT_WEBPAGE;
	var $prefix = '';

	function ntsLink(){
		$this->setTarget( NTS_ROOT_WEBPAGE );
		$this->prefix = '';
		}

	function setTarget( $trg ){
		$this->target = $trg;
		}

	function prepare( $panel = '', $action = '', $params = array() ){
		$this->prefix = ntsView::makeGetParams( $panel, $action, $params );
		}

	function make( $panel = '', $action = '', $params = array()  ){
		$joiner = ( strpos($this->target, '?' ) === false ) ? '?' : '&'; 
		$link = $this->target . $joiner . ntsView::makeGetParams( $panel, $action, $params );
		return $link;
		}
	function append( $p, $v ){
		$joiner = ( strpos($this->target, '?' ) === false ) ? '?' : '&'; 
		$link = $this->target . $joiner . $this->prefix . '&' . $p . '=' . urlencode($v);
		return $link;
		}
	function makeLink( $panel = '', $action = '', $params = array(), $return = false, $skipSaveOn = false ){
		return ntsLink::makeLinkFull( NTS_ROOT_WEBPAGE, $panel, $action, $params, $return, $skipSaveOn );
		}
	function makeLinkFull( $target, $panel = '', $action = '', $params = array(), $return = false, $skipSaveOn = false ){
		$rootWebPage = $target;
		if( $return )
			$params[ NTS_PARAM_RETURN ] = 1;
		if( $panel || $action || $params ){
			$joiner = ( strpos($rootWebPage, '?') === false ) ? '?' : '&';
			$link =  $rootWebPage . $joiner . ntsView::makeGetParams( $panel, $action, $params, $skipSaveOn );
			}
		else {
			$link =  $rootWebPage;
			}
		return $link;
		}
	function printLink( $p = array() ){
		global $NTS_CURRENT_USER;
		$return = '';
		if( ! isset($p['action']) )
			$p['action'] = '';
		if( ! isset($p['params']) )
			$p['params'] = array();
		if( ! isset($p['return']) )
			$p['return'] = false;
		if( ! isset($p['skipSaveOn']) )
			$p['skipSaveOn'] = false;

		$panel = ntsView::parsePanel( $p['panel'] );
		$attrLine = ' ';
		if( isset($p['attr']) ){
			$attrs = array();
			foreach( $p['attr'] as $pk => $pv ){
				$attrs[] = $pk . '="' . $pv . '"';
				}
			$attrLine = ' ' . join( ' ', $attrs );
			}
		if( ! $NTS_CURRENT_USER->isPanelDisabled($panel) ){
			$link = ntsLink::makeLink( $panel, $p['action'], $p['params'], $p['return'], $p['skipSaveOn'] );
			$return = '<a' . $attrLine . ' href="' . $link . '">' . $p['title'] . '</a>';
			}
		return $return;
		}

	}

class ntsView {
	function setTitle( $title ){
		global $NTS_PAGE_TITLE_ARRAY, $NTS_PAGE_TITLE;
		if( ! $NTS_PAGE_TITLE_ARRAY )
			$NTS_PAGE_TITLE_ARRAY = array();
		$title = trim( $title );
		if( $title )
			$NTS_PAGE_TITLE_ARRAY[] = $title;
		$NTS_PAGE_TITLE = join( ' - ', $NTS_PAGE_TITLE_ARRAY ); // for backward compatibility
		}

	function getTitle(){
		global $NTS_PAGE_TITLE_ARRAY;
		if( ! $NTS_PAGE_TITLE_ARRAY )
			$NTS_PAGE_TITLE_ARRAY = array();
		$return = join( ' - ', $NTS_PAGE_TITLE_ARRAY );
		return $return;
		}

	function setNextAction( $panel, $action = '' ){
		global $NTS_REQUESTED_PANEL, $NTS_REQUESTED_ACTION;
		$panel = ntsView::parsePanel( $panel );
		$NTS_REQUESTED_PANEL = $panel;
		$NTS_REQUESTED_ACTION = $action;
		}

	function objectTitle( $object ){
		if( ! $object )
			return;
		$className = $object->getClassName();
		switch( $className ){
			case 'service':
				$conf =& ntsConf::getInstance();
				$showSessionDuration = $conf->get('showSessionDuration');

				$return = $object->getProp( 'title' );
				$durationView = '';
				if( $showSessionDuration ){
					if( $object->getProp('until_closed') )
						$durationView .= M('Until Closed');
					else
						$durationView .= ntsTime::formatPeriod($object->getProp('duration'));
					}
				if( $durationView ){
					$return .= ' [' . $durationView . ']';
					}

				break;
			case 'user':
				$return = $object->getProp( 'first_name' ) . ' ' . $object->getProp( 'last_name' );
				break;
			default:
				$return = $object->getProp( 'title' );
				break;
			}

		$className = $object->getClassName();
	/* plugin files */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		reset( $activePlugins );
		$viewFiles = array();
		foreach( $activePlugins as $plg ){
			$viewFiles[] = $plm->getPluginFolder( $plg ) . '/views/' . $className . '/title.php';
			}
		reset( $viewFiles );
		foreach( $viewFiles as $vf ){
			if( file_exists($vf) ){
				require( $vf );
				break;
				}
			}
		return $return;
		}

	function appServiceView( $a ){
		$return = '';

		$service = ntsObjectFactory::get( 'service' );
		$service->setId( $a->getProp('service_id') );

		$seats = $a->getProp('seats');
		$duration = $a->getProp('duration');
		$untilClosed = $a->getProp('until_closed');

		$return .= ntsView::serviceView( $service, $seats, $duration, $untilClosed );

		$thisPrice = $a->getProp('price');
		$priceView = ntsCurrency::formatServicePrice($thisPrice);
		if( strlen($priceView) ){
			$return .= "\n" . M('Price') . ': ' . $priceView;
			}
		return $return;
		}

	function servicePriceView( $service, $seats ){
		$return = '';

		$showPrice = true;
		if( $seats > 0 ){
			$thisPrice = ntsLib::getServicePrice( 
				$service,
				$seats
				);
			}
		else {
			$thisPrice = $service->getProp('price');
			}
		if( ! strlen($thisPrice) ){
			$showPrice = false;
			}

		if( $showPrice ){
			if( $seats || ($thisPrice == 0) ){
				$return .= '' . ntsCurrency::formatServicePrice($thisPrice) . '';
				}
			}
		return $return;
		}

	function serviceView( $service, $seats, $duration, $untilClosed = false ){
		$return = '';

		$conf =& ntsConf::getInstance();
		$showSessionDuration = $conf->get('showSessionDuration');

		$return .= $service->getProp('title');
		if( $showSessionDuration ){
			if( $untilClosed )
				$return .= "\n [" . M('Until Closed') . ']';
			else
				$return .= "\n [" . ntsTime::formatPeriod($duration) . ']';
			}

		return $return;
		}

	function redirect( $to ){
		if( ! headers_sent() ){
			header( 'Location: ' . $to );
			exit;
			}
		else {
//			$html = "<META http-equiv=\"refresh\" content=\"0;URL=$to\">";
			$html = "<a href=\"$to\">$to</a>";
			echo $html;
			}
		}

	function resetPersistentParams( $rootPanel = '' ){
		global $NTS_PERSISTENT_PARAMS;
		if( ! $rootPanel )
			$rootPanel = '/';
		if( ! $NTS_PERSISTENT_PARAMS )
			$NTS_PERSISTENT_PARAMS = array();
		$NTS_PERSISTENT_PARAMS[ $rootPanel ] = array();
		}

	function setPersistentParams( $pNames, $req, $rootPanel = '' ){
		global $NTS_PERSISTENT_PARAMS;

		if( ! $rootPanel )
			$rootPanel = '/';

		if( ! $NTS_PERSISTENT_PARAMS )
			$NTS_PERSISTENT_PARAMS = array();
		if( ! isset($NTS_PERSISTENT_PARAMS[ $rootPanel ]) )
			$NTS_PERSISTENT_PARAMS[ $rootPanel ] = array();

		foreach( $pNames as $pName => $pValue ){
			if( is_array($pValue) || strlen($pValue) )
				$NTS_PERSISTENT_PARAMS[ $rootPanel ][ $pName ] = $pValue;
			}
		}

	function parsePanel( $panel ){
		global $NTS_CURRENT_PANEL;
		$currentTag = '-current-';
		if( substr($panel, 0, strlen($currentTag)) == $currentTag ){
			$replaceFrom = '-current-';
			$replaceTo = $NTS_CURRENT_PANEL;

			if( strpos($panel, '..') !== false ){
				$downCount = substr_count( $panel, '/..' );
				$re = "/^(.+)(\/[^\/]+){" . $downCount. "}$/U";
				preg_match($re, $replaceTo, $ma);

				$replaceFrom = '-current-' . str_repeat('/..', $downCount);
				$replaceTo = $ma[1];
				}

			$panel = str_replace( $replaceFrom, $replaceTo, $panel );
			}
		return $panel;
		}

	function makeGetParams( $panel = '', $action = '', $params = array(), $skipSaveOn = false ){
		global $NTS_PERSISTENT_PARAMS, $NTS_CURRENT_PANEL;

		$panel = ntsView::parsePanel( $panel );

		if( $panel )
			$params[ NTS_PARAM_PANEL ] = $panel;
		if( $action )
			$params[ NTS_PARAM_ACTION ] = $action;

		if( $NTS_PERSISTENT_PARAMS && (! $skipSaveOn) ){
			reset( $NTS_PERSISTENT_PARAMS );
		/* global */
			if( isset($NTS_PERSISTENT_PARAMS['/']) ){
				reset( $NTS_PERSISTENT_PARAMS['/'] );
				foreach( $NTS_PERSISTENT_PARAMS['/'] as $p => $v ){
					if( ! isset($params[$p]) )
						$params[ $p ] = $v;
					}
				}
		/* above panel */
			reset( $NTS_PERSISTENT_PARAMS );
			foreach( $NTS_PERSISTENT_PARAMS as $pan => $pampam ){
				if( substr($panel, 0, strlen($pan) ) != $pan )
					continue;
				reset( $pampam );
				foreach( $pampam as $p => $v ){
					if( ! isset($params[$p]) )
						$params[ $p ] = $v;
					}
				}
			}

		reset( $params );
		$linkParts = array();
		foreach( $params as $p => $v ){
			if( $v || ($v === 0) ){
				if( is_array($v) )
					$v = join( '-', $v );
				elseif( is_object($v) ){
					$v = $v->getId();
					}
				$linkParts[] = $p . '=' . urlencode($v);
				}
			}

		if( $linkParts )
			$link = join( '&', $linkParts );
		else
			$link = '';
		return $link;
		}

	function prepareUrlParams( $params = array() ){
		reset( $params );
		$linkParts = array();
		foreach( $params as $p => $v ){
			if( $v )
				$linkParts[] = $p . '=' . urlencode($v);
			}
		$link = join( '&', $linkParts );
		return $link;
		}

	function reloadParent( $forceOpen = false ){
		$_SESSION['reload_parent'] = 1;
		}

	function setAdminAnnounce( $msg, $type = 'ok' ){
	// type might be 'error' or 'ok'
		$_SESSION['announce_text_admin'] = array( $msg, $type );
		}
	function isAdminAnnounce(){
		$return = ( isset($_SESSION['announce_text_admin']) )? true : false;
		return $return;
		}
	function getAdminAnnounceText(){
		if( isset($_SESSION['announce_text_admin']) ){
			$return = $_SESSION['announce_text_admin'];
			}
		else {
			$return = '';
			}
		return $return;
		}
	function clearAdminAnnounce(){
		unset( $_SESSION['announce_text_admin'] );
		}

	function addAnnounce( $msg, $type = 'ok', $order = 50 ){
	// type might be 'error' or 'ok'
		if( ! isset($_SESSION['announce_text']) ){
			$_SESSION['announce_text'] = array();
			}
		$_SESSION['announce_text'][] = array( $msg, $type, $order );
		}

	function setAnnounce( $msg, $type = 'ok' ){
		ntsView::addAnnounce( $msg, $type );
		}
	function isAnnounce(){
		$return = ( isset($_SESSION['announce_text']) )? true : false;
		return $return;
		}
	function isReloadParent(){
		$return = ( isset($_SESSION['reload_parent']) )? $_SESSION['reload_parent'] : false;
		return $return;
		}

	function getAnnounceText(){
		if( isset($_SESSION['announce_text']) ){
			$return = $_SESSION['announce_text'];

		/* SORT BY ORDER */
			usort( $return, create_function('$a, $b', 'return ntsLib::numberCompare($a[2], $b[2]);' ) );
			}
		else {
			$return = '';
			}
		return $return;
		}
	function getAnnounceType(){
		$return = ( isset($_SESSION['announce_type']) )? $_SESSION['announce_type'] : '';
		return $return;
		}
	function clearAnnounce(){
		unset( $_SESSION['reload_parent'] );
		unset( $_SESSION['announce_text'] );
		unset( $_SESSION['announce_type'] );
		}
	}
?>