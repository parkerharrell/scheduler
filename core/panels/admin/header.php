<?php
$conf =& ntsConf::getInstance();
$theme = $conf->get( 'theme' );
$themeFolder = NTS_EXTENSIONS_DIR . '/themes/' . $theme;
$adminHeaderFile = $themeFolder . '/admin-header.php';
if( file_exists($adminHeaderFile) ){
	require( $adminHeaderFile );
	}
$display = isset($_REQUEST['display']) ? $_REQUEST['display'] : '';
?>
<script language="JavaScript" src="<?php echo ntsLink::makeLink('system/pull', '', array('what' => 'js', 'files' => 'functions.js|jquery-1.4.2.min.js|jquery.simplemodal-1.3.4.min.js') ); ?>">
</script>