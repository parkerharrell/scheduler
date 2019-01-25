<?php
class ntsPluginManager {
	var $dir;
	var $plugins;

	function ntsPluginManager(){
		$this->dir = NTS_APP_DIR . '/plugins';
		$this->plugins = $this->_getActivePlugins();
		}

	function getActivePlugins(){
		return $this->plugins;
		}

	function getPanels( $plugin ){
		$return = array();
		$panelsFile = $this->getPluginFolder( $plugin ) . '/panels.php';
		if( file_exists($panelsFile) ){
			require( $panelsFile );
			$return = $panels;
			}
		return $return;
		}
		
	function _getActivePlugins(){
		$conf =& ntsConf::getInstance();
		$currentPlugins = $conf->get( 'plugins' );
		$currentPlugins = array_unique( $currentPlugins );

		reset( $currentPlugins );
		// check if every plugin is still there
		foreach( $currentPlugins as $p ){
			$pluginFolder = $this->getPluginFolder( $p );
			if( ! file_exists($pluginFolder) ){
				$this->pluginDisable( $p );
				continue;
				}
			$infoFile = $pluginFolder . '/info.php';
			if( ! file_exists($infoFile) ){
				$this->pluginDisable( $p );
				continue;
				}
			require( $infoFile );
			$require = ntsLib::parseVersionNumber( $requireVersion );
			$systemVersion = ntsLib::parseVersionNumber( NTS_APP_VERSION );
			if( $systemVersion < $require ){
				$this->pluginDisable( $p );
				continue;
				}
			}

		return $currentPlugins;
		}

	function pluginActivate( $plugin ){
		$conf =& ntsConf::getInstance();
		$ntsdb =& dbWrapper::getInstance();

		$currentPlugins = $this->getActivePlugins();
		$currentPlugins[] = $plugin;
		$currentPlugins = array_unique( $currentPlugins );

		$newValue = $conf->set( 'plugins', $currentPlugins );
		$sql = $conf->getSaveSql( 'plugins', $newValue );
		$result = $ntsdb->runQuery( $sql );

		/* run install file */
		$plgFolder = $this->getPluginFolder( $plugin );
		$installFile = $plgFolder . '/install.php';
		if( file_exists($installFile) ){
			require($installFile);
			}

		return $result;
		}

	function pluginDisable( $plugin ){
		$conf =& ntsConf::getInstance();
		$ntsdb =& dbWrapper::getInstance();

		$currentPlugins = $this->getActivePlugins();
		$newCurrentPlugins = array();
		reset( $currentPlugins );
		foreach( $currentPlugins as $plg ){
			if( $plg = $plugin )
				continue;
			$newCurrentPlugins[] = $plg;
			}
		$newCurrentPlugins = array_unique( $newCurrentPlugins );

		$newValue = $conf->set( 'plugins', $newCurrentPlugins );
		$sql = $conf->getSaveSql( 'plugins', $newValue );
		$result = $ntsdb->runQuery( $sql );

		/* run uninstall file */
		$plgFolder = $this->getPluginFolder( $plugin );
		$uninstallFile = $plgFolder . '/uninstall.php';
		if( file_exists($uninstallFile) ){
			require($uninstallFile);
			}

		return $result;
		}

	function getPlugins(){
		$plugins = array();

		$folders = ntsLib::listSubfolders( $this->dir );
		reset( $folders );
		foreach( $folders as $f ){
			$plugins[] = $f;
			}

		return $plugins;
		}

	function getPluginFolder( $plg ){
		$folderName = $plg;
		$fullFolderName = $this->dir . '/' . $folderName;
		return $fullFolderName;
		}

	function getPluginSettings( $plg ){
		$return = array();
		$conf =& ntsConf::getInstance();

		$confPrefix = 'plugin-' . $plg . '-';
		$allSettingsNames = $conf->getLoadedNames();
		reset( $allSettingsNames );
		foreach( $allSettingsNames as $confName ){
			if( substr($confName, 0, strlen($confPrefix)) == $confPrefix ){
				$shortName = substr($confName, strlen($confPrefix));
				$confValue = $conf->get( $confName );
				$return[ $shortName ] = $confValue;
				}
			}
		return $return;
		}

	function getPluginSetting( $plg, $settingName ){
		$conf =& ntsConf::getInstance();
		$confPrefix = 'plugin-' . $plg . '-';
		$settingName = $confPrefix . $settingName;
		return $conf->get( $settingName );
		}

	function savePluginSetting( $plg, $settingName, $settingValue ){
		$conf =& ntsConf::getInstance();
		$confPrefix = 'plugin-' . $plg . '-';
		$settingName = $confPrefix . $settingName;
		return $conf->save( $settingName, $settingValue );
		}

	// Singleton stuff
	function &getInstance(){
		return ntsLib::singletonFunction( 'ntsPluginManager' );
		}
	}
?>