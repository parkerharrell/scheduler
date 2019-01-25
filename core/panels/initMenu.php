<?php
global $NTS_VIEW, $NTS_CURRENT_USER, $NTS_CORE_DIRS;
$NTS_VIEW['menu1'] = array();
$NTS_VIEW['menu2'] = array();
$NTS_VIEW['subtitle'] = '';
$NTS_VIEW['subHeaderFile'] = '';

$requestedPanel = $NTS_CURRENT_PANEL;

/* search for root file to locate first level menu */
$rootFileFound = false;
$rootPath = $requestedPanel;
do {
	$rootFile = ( $rootPath ) ? '/' . $rootPath . '/root.php' : '/root.php';
	$rootFile = ntsLib::fileInCoreDirs( '/panels/' . $rootFile );
	if( $rootFile ){
		$rootFileFound = true;
		break;
		}
	$rootPath = ntsLib::getParentPath( $rootPath );
	}
while( $rootPath );

if( $rootFileFound ){
	require( $rootFile );
	}

/* check if we may have redirects defined */
$redirectsFile = ntsLib::fileInCoreDirs( 'panels/redirects.php' );
if( $redirectsFile ){
	include_once( $redirectsFile );
	if( isset($NTS_PANEL_REDIRECTS[$NTS_CURRENT_PANEL]) ){
		list( $NTS_CURRENT_PANEL, $noForward ) = $NTS_PANEL_REDIRECTS[$NTS_CURRENT_PANEL];
		}
	}

$requestedPanel = $NTS_CURRENT_PANEL;

/* requested panel got index.php ? */
$indexFile = ntsLib::fileInCoreDirs( '/panels/' . $requestedPanel . '/index.php' );
$actionFile = ntsLib::fileInCoreDirs( '/panels/' . $requestedPanel . '/action.php' );

if( $indexFile || $actionFile ){
	$NTS_CURRENT_PANEL = $requestedPanel;
	}
else {
	/* btw, if there's subheader here, include it first */
	$subHeaderFile = ntsLib::fileInCoreDirs( '/panels/' . $requestedPanel . '/subheader.php' );
	if( $subHeaderFile ){
		ntsLib::requireSubheaderFile( $subHeaderFile );
		}

	/* get subfolders to auto expand */
	$expandFound = false;
	$lowestSequence = 100;

	$startSearchForExpand = array();
	reset( $NTS_CORE_DIRS );
	foreach( $NTS_CORE_DIRS as $rcd ){
		$startSearchForExpand[] = $rcd . '/panels/' . $requestedPanel;
		}
	$mySubFolders = ntsLib::listSubfolders( $startSearchForExpand );
	$mySubFoldersReal = array();
	$myMenuFilesReal = array();
	reset( $mySubFolders );
	foreach( $mySubFolders as $sf ){
		$thisPath = 'panels/' . $requestedPanel . '/' . $sf;
		$thisMenuFile = ntsLib::fileInCoreDirs( $thisPath . '/menu.php' );
		$myMenuFilesReal[] = $thisMenuFile;
		$mySubFoldersReal[] = ntsLib::fileInCoreDirs( $thisPath );
		}

	$subFoldersCount = count( $mySubFolders );
	for( $i = 0; $i <$subFoldersCount; $i++ ){
		$sf = $mySubFolders[ $i ];
		$menuFile = $myMenuFilesReal[ $i ];

		if( $menuFile ){
			list( $title, $sequence, $params, $directLink, $ajax ) = ntsLib::requireMenuFile( $menuFile );
			if( $sequence < $lowestSequence ){
				$thisExpandAllowed = true;
				$checkExpandTo = $requestedPanel . '/' . $sf;

				if( $NTS_CURRENT_USER->isPanelDisabled($checkExpandTo) ){
					$thisExpandAllowed = false;
					}

				if( $thisExpandAllowed ){
					$lowestSequence = $sequence;
					$expandFound = true;
					$expandTo = $sf;
					}
				}
			}
		}

	if( $expandFound ){
		$NTS_CURRENT_PANEL = $requestedPanel . '/' . $expandTo;
		}
	}

/* NOW CHECK FOR DISABLED PANELS */
$checkPanel = $NTS_CURRENT_PANEL;

if( $NTS_CURRENT_USER->isPanelDisabled($checkPanel) ){
	// not allowed - go to default
	$NTS_CURRENT_PANEL = 'admin/appointments';
	ntsView::setAnnounce( M('Permission Denied'), 'error' );

	/* redirect to default page */
	$forwardTo = ntsLink::makeLink();
	ntsView::redirect( $forwardTo );
	exit;
	}

/* first level menu in root folder */
$startSearchForRoot = array();
reset( $NTS_CORE_DIRS );
foreach( $NTS_CORE_DIRS as $rcd ){
	$startSearchForRoot[] = $rcd . '/panels/' . $rootPath;
	}
$mySubFolders = ntsLib::listSubfolders( $startSearchForRoot );

$myMenuFilesReal = array();
$mySubFoldersReal = array();
reset( $mySubFolders );
foreach( $mySubFolders as $sf ){
	$thisPath = 'panels/' . $rootPath . '/' . $sf;
	$thisMenuFile = ntsLib::fileInCoreDirs( $thisPath . '/menu.php' );
	$myMenuFilesReal[] = $thisMenuFile;
	$mySubFoldersReal[] = ntsLib::fileInCoreDirs( 'panels/' . $rootPath . '/' . $sf );
	}

$subFoldersCount = count( $myMenuFilesReal );

for( $i = 0; $i <$subFoldersCount; $i++ ){
	$menuFile = $myMenuFilesReal[ $i ];
	if( ! $menuFile )
		continue;
	$sf = $mySubFolders[ $i ];

	list( $title, $sequence, $params, $directLink, $ajax ) = ntsLib::requireMenuFile( $menuFile );
	if( $title ){
		$showThisMenu = true;
	// check if I have anything below it if not this direct
		$relativePath = 'panels/' . $rootPath . '/' . $sf;
		$mIndexFile = ntsLib::fileInCoreDirs( $relativePath . '/index.php' );
		$mActionFile = ntsLib::fileInCoreDirs( $relativePath . '/action.php' );
	
		if( ! ( $mIndexFile || $mActionFile ) ){
			$showThisMenu = false;

			$startSearchForSubRoot = array();
			reset( $NTS_CORE_DIRS );
			foreach( $NTS_CORE_DIRS as $rcd ){
				$startSearchForSubRoot[] = $rcd . '/' . $relativePath;
				}
			$mySubSubFolders = ntsLib::listSubfolders( $startSearchForSubRoot );

			reset( $mySubSubFolders );
			foreach( $mySubSubFolders as $msf ){
				$checkThisNow = $rootPath . '/' . $sf . '/' . $msf;
				$subSubMenuFile = ntsLib::fileInCoreDirs( 'panels/' . $checkThisNow . '/menu.php' );
				if( $subSubMenuFile && (! $NTS_CURRENT_USER->isPanelDisabled($checkThisNow) ) ){
					$showThisMenu = true;
					break;
					}
				}
			}

		if( $showThisMenu ){
			$NTS_VIEW['menu1'][] = array(
				'panel'		=> $rootPath . '/' . $sf,
				'title'		=> $title,
				'seq'		=> $sequence,
				'params'	=> $params,
				);
			}
		}

	}

usort( $NTS_VIEW['menu1'], create_function('$a, $b', 'return ntsLib::numberCompare($a["seq"], $b["seq"]);') );

/* second level menu */
/* if subheader exists then parent of current, otherwise second level after root */
$searchForSubheaderIn = array();
$searchForSubheaderIn[] = ntsLib::getParentPath( $NTS_CURRENT_PANEL );

reset( $searchForSubheaderIn );
foreach( $searchForSubheaderIn as $menuParentPath ){
	$subHeaderFile = ntsLib::fileInCoreDirs( '/panels/' . $menuParentPath . '/subheader.php' );
	while( (! $subHeaderFile) && $menuParentPath ){
		$menuParentPath = ntsLib::getParentPath( $menuParentPath );
		$subHeaderFile = ntsLib::fileInCoreDirs( '/panels/' . $menuParentPath . '/subheader.php' );
		}
	if( $menuParentPath )
		break;
	}

/* subheader not found */
if( $menuParentPath ){
	$secondLevelPath = $menuParentPath;

	$title = '';
	$NTS_VIEW['subHeaderFile'] = $subHeaderFile;
	list( $title ) = ntsLib::requireSubheaderFile( $subHeaderFile );
	if( $title ){
		$NTS_VIEW['subtitle'] = $title;
		}
	}
else{
	$remain = substr( $NTS_CURRENT_PANEL, strlen($rootPath) + 1 );
	$slashPos = strpos( $remain, '/' );
	if( $slashPos && ($slashPos > 0) )
		$secondLevel = substr( $remain, 0, $slashPos );
	else
		$secondLevel = $remain;
	$secondLevelPath = $rootPath . '/' . $secondLevel;
	}

$startSearchForSecondLevel = array();
reset( $NTS_CORE_DIRS );
foreach( $NTS_CORE_DIRS as $rcd ){
	$startSearchForSecondLevel[] = $rcd . '/panels/' . $secondLevelPath;
	}
$mySubFolders = ntsLib::listSubfolders( $startSearchForSecondLevel );
$mySubFoldersReal = array();
reset( $mySubFolders );
foreach( $mySubFolders as $sf ){
	$mySubFoldersReal[] = ntsLib::fileInCoreDirs( 'panels/' . $secondLevelPath . '/' . $sf );
	$menuFile = ntsLib::fileInCoreDirs( 'panels/' . $secondLevelPath . '/' . $sf . '/menu.php' );
	$myMenuFiles[] = $menuFile;
	}

$subFoldersCount = count( $mySubFolders );
for( $i = 0; $i <$subFoldersCount; $i++ ){
	$sf = $mySubFolders[ $i ];
	$menuFile = $myMenuFiles[ $i ];

	if( $menuFile ){
		list( $title, $sequence, $params, $directLink, $ajax ) = ntsLib::requireMenuFile( $menuFile );
		if( $title ){
			$link2panel = $secondLevelPath . '/' . $sf;

			// CHECK IF NOT DISABLED
			if( ! $NTS_CURRENT_USER->isPanelDisabled($link2panel) ){
				$menuArray = array(
					'panel'		=> $link2panel,
					'title'		=> $title,
					'seq'		=> $sequence,
					'params'	=> $params,
					'ajax'		=> $ajax,
					);
				if( $directLink ){
					$menuArray['directLink'] = $directLink;
					}
				$NTS_VIEW['menu2'][] = $menuArray;
				}
			}
		}
	}
usort( $NTS_VIEW['menu2'], create_function('$a, $b', 'return ntsLib::numberCompare($a["seq"], $b["seq"]);') );

/* get header file */
$NTS_VIEW['headFile'] = '';

$headerFile = ntsLib::fileInCoreDirs( '/panels/' . $rootPath . '/header.php' );
$footerFile = ntsLib::fileInCoreDirs( '/panels/' . $rootPath . '/footer.php' );
if( $headerFile && $footerFile ){
	$NTS_VIEW['headerFile'] = $headerFile;
	$NTS_VIEW['footerFile'] = $footerFile;
	}
else {
/* for customer view */
	$defaultThemeFolder = NTS_APP_DIR . '/defaults/theme';
	$conf =& ntsConf::getInstance();
	$theme = $conf->get( 'theme' );
	$themeFolder = NTS_EXTENSIONS_DIR . '/themes/' . $theme;

	if( file_exists($themeFolder) ){
		$NTS_VIEW['headFile'] = $themeFolder . '/head.php';
		$NTS_VIEW['headerFile'] = $themeFolder . '/header.php';
		$NTS_VIEW['footerFile'] = $themeFolder . '/footer.php';
		}
	// default theme
	else {
		$NTS_VIEW['headerFile'] = $defaultThemeFolder . '/header.php';
		$NTS_VIEW['footerFile'] = $defaultThemeFolder . '/footer.php';
		}
	}
?>