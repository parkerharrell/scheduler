<?php
if( ! defined('NTS_HEADER_SENT') )
	define( 'NTS_HEADER_SENT', 1 );

/* this page defines NTS_CSS_URL */
global $NTS_CURRENT_PANEL;
$conf =& ntsConf::getInstance();
$theme = $conf->get( 'theme' );

if( preg_match('/^admin/', $NTS_CURRENT_PANEL) || preg_match('/^superadmin/', $NTS_CURRENT_PANEL) )
	$NTS_CSS_URL = ntsLink::makeLink('system/pull', '', array('what' => 'css', 'panel' => 'admin') );
else
	$NTS_CSS_URL = ntsLink::makeLink('system/pull', '', array('theme' => $theme, 'files' => 'style.css|colors.css') );
?>