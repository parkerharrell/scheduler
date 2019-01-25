<?php
global $NTS_CORE_DIRS;

$what = isset( $_GET['what'] ) ? $_GET['what'] : 'css';
$finalFiles = array();
switch( $what ){
	case 'css':
		$contentType = 'text/css';
		$panel = isset( $_GET['panel'] ) ? $_GET['panel'] : '';
		switch( $panel ){
			case 'admin':
				reset( $NTS_CORE_DIRS );
				foreach( $NTS_CORE_DIRS as $rcd ){
					$thisFolder = $rcd . '/defaults/theme/admin';
					$subFiles = ntsLib::listFiles( $thisFolder, '.css' );
					reset( $subFiles );
					foreach( $subFiles as $sf ){
						array_unshift( $finalFiles, $thisFolder . '/' . $sf );
						}
					}
				break;

			default:
				$theme = isset( $_GET['theme'] ) ? $_GET['theme'] : '';

				reset( $NTS_CORE_DIRS );
				foreach( $NTS_CORE_DIRS as $rcd ){
					$thisFolder = $rcd . '/defaults/theme';
					$subFiles = ntsLib::listFiles( $thisFolder, '.css' );
					reset( $subFiles );
					foreach( $subFiles as $sf ){
						array_unshift( $finalFiles, $thisFolder . '/' . $sf );
						}

					if( $theme ){
						$thisFolder = NTS_EXTENSIONS_DIR . '/themes/' . $theme;
						$subFiles = ntsLib::listFiles( $thisFolder, '.css' );
						reset( $subFiles );
						foreach( $subFiles as $sf ){
							$finalFiles[] = $thisFolder . '/' . $sf;
							}
						}
					}
				break;
			}
		break;

	case 'js':
		$contentType = 'text/javascript';
		$files = $_GET['files'];
		$files = trim( $files );
		$files = explode( '|', $files );

		foreach( $files as $f ){
			$f = trim( $f );
			if( ! $f )
				continue;
			$fullPath = ntsLib::fileInCoreDirs( 'lib/js/' . $f );
			if( $fullPath )
				$finalFiles[] = $fullPath;
			}
		break;
	}

reset( $finalFiles );
header("Content-type: $contentType");
foreach( $finalFiles as $f ){
	if( file_exists($f) ){
		readfile( $f );
		}
	}
exit;
?>