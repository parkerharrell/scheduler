<?php
class ntsConf {
	var $rawValues;
	var $arrayType = array();
	var $codeGet = '';
	var $codeSet = '';
	var $_cache = array();

	function ntsConf(){
		$this->rawValues = array();
		$this->arrayType = array(
			'disabledNotifications',
			);

		$codeFile = NTS_BASE_DIR . '/model/confGet.php';
		$code2run = file_get_contents( $codeFile );
		$code2run = str_replace( '<?php', '', $code2run );
		$code2run = str_replace( '?>', '', $code2run );
		$this->codeGet = $code2run;

		$codeFile = NTS_BASE_DIR . '/model/confSet.php';
		$code2run = file_get_contents( $codeFile );
		$code2run = str_replace( '<?php', '', $code2run );
		$code2run = str_replace( '?>', '', $code2run );
		$this->codeSet = $code2run;

		$this->_cache = array();
		$this->load();
		}

	function load(){
		$this->rawValues = array();
		$ntsdb =& dbWrapper::getInstance();

		$tables = $ntsdb->getTablesInDatabase();
		if( ! in_array('conf', $tables) ){
			return;
			}

		$sql = "SELECT name, value FROM {PRFX}conf";
		$result = $ntsdb->runQuery( $sql );
		while( $oInfo = $result->fetch() ){
			if( in_array($oInfo['name'], $this->arrayType)){
				if( ! isset($this->rawValues[ $oInfo['name'] ]) ){
					$this->rawValues[ $oInfo['name'] ] = array();
					}
				$this->rawValues[ $oInfo['name'] ][] = $oInfo['value'];
				}
			else {
				if( isset($this->rawValues[$oInfo['name']]) ){
					if( ! is_array($this->rawValues[ $oInfo['name'] ]) )
						$this->rawValues[ $oInfo['name'] ] = array( $this->rawValues[ $oInfo['name'] ] );
					$this->rawValues[ $oInfo['name'] ][] = $oInfo['value'];
					}
				else {
					$this->rawValues[ $oInfo['name'] ] = $oInfo['value'];
					}
				}
			}
		}

	function getLoadedNames(){
		$return = array_keys( $this->rawValues );
		return $return;
		}

	function get( $name ){
		if( ! isset($this->_cache[$name]) ){
			if( in_array($name, $this->arrayType) || (isset($this->rawValues[$name]) && is_array($this->rawValues[$name])) ){
				$rawValue = isset($this->rawValues[$name]) ? $this->rawValues[$name] : array();
				}
			else {
				$rawValue = isset($this->rawValues[$name]) ? $this->rawValues[$name] : '';
				$rawValue = trim( $rawValue );
				}
			$return = $rawValue;

		/* actual code file */
			eval( $this->codeGet );

			$this->_cache[$name] = $return;
			}
		$return = $this->_cache[$name];
		return $return;
		}

	function set( $name, $value ){
		$return = $value;

	/* actual code file */
		eval( $this->codeSet );
		return $return;
		}

	function reset( $name ){
		$ntsdb =& dbWrapper::getInstance();

		$sql = "DELETE FROM {PRFX}conf WHERE name = '$name'";
		$result = $ntsdb->runQuery( $sql );
		if( $result )
			return true;
		else
			return false;
		}

	function save( $name, $newValue ){
		$ntsdb =& dbWrapper::getInstance();
		if( is_array($newValue) || in_array($name, $this->arrayType) ){
			$sql = "DELETE FROM {PRFX}conf WHERE name = '$name'";
			$result = $ntsdb->runQuery( $sql );
			reset( $newValue );
			foreach( $newValue as $nv ){
				$insertSql = $ntsdb->prepareInsertStatement( array('value' => $nv, 'name' => $name) );
				$sql = "INSERT INTO {PRFX}conf $insertSql";
				$result = $ntsdb->runQuery( $sql );
				}
			}
		else {
			$sql = "SELECT value FROM {PRFX}conf WHERE name = '$name'";
			$result = $ntsdb->runQuery( $sql );
			$update = ( $oInfo = $result->fetch() ) ? true : false;

		/* update */
			if( $update ){
				$updateSql = $ntsdb->prepareUpdateStatement( array('value' => $newValue) );
				$sql = "UPDATE {PRFX}conf SET $updateSql WHERE name = '$name'";
				}
		/* insert */
			else {
				$insertSql = $ntsdb->prepareInsertStatement( array('value' => $newValue, 'name' => $name) );
				$sql = "INSERT INTO {PRFX}conf $insertSql";
				}
			$result = $ntsdb->runQuery( $sql );
			}
		return $result;
		}
	
	function getSaveSql( $name, $newValue ){
		$ntsdb =& dbWrapper::getInstance();
		if( in_array($name, $this->arrayType) ){
			}
		else {
			$sql = "SELECT value FROM {PRFX}conf WHERE name = '$name'";
			$result = $ntsdb->runQuery( $sql );
			$update = ( $oInfo = $result->fetch() ) ? true : false;

		/* update */
			if( $update ){
				$updateSql = $ntsdb->prepareUpdateStatement( array('value' => $newValue) );
				$sql = "UPDATE {PRFX}conf SET $updateSql WHERE name = '$name'";
				}
		/* insert */
			else {
				$insertSql = $ntsdb->prepareInsertStatement( array('value' => $newValue, 'name' => $name) );
				$sql = "INSERT INTO {PRFX}conf $insertSql";
				}
			}
		return $sql;
		}

	// Singleton stuff
	function &getInstance(){
		return ntsLib::singletonFunction( 'ntsConf' );
		}
	}
?>