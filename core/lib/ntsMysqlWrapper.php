<?php
class ntsMysqlResult {
	var $_result;

	function ntsMysqlResult( &$res ){
		$this->_result =& $res;
		@mysql_data_seek( $this->_result, 0 );
		}
	function fetch() {
		return mysql_fetch_assoc( $this->_result );
		}
	function size() {
		$size = mysql_num_rows( $this->_result );
		return $size;
		}
	}

class ntsMysqlWrapper {
	var $_host;
	var $_user;
	var $_pass;
	var $_db;
	var $_prefix;
	var $_dbLink;

	var $_debug;
	var $_queryCount;
	var $_error;

	var $_cache;
	var $_enableCache;

	function ntsMysqlWrapper( $host, $user, $pass, $db, $prefix = '' ){
		$this->_host = $host;
		$this->_user = $user;
		$this->_pass = $pass;
		$this->_db = $db;
		$this->_prefix = $prefix;

		$this->_dbLink = null;
		$this->_debug = false;
		$this->_queryCount = 0;

		$this->_cache = array();
		$this->_enableCache = true;
		}

	function getError(){
		return $this->_error;
		}
	function setError( $err ){
		$this->_error = $err;
		}
	function resetError(){
		$this->_error = '';
		}

	function checkSettings(){
		$return = false;
		// CONNECT TO DB SERVER
		if(! $this->_dbLink = @mysql_connect($this->_host, $this->_user, $this->_pass, true)){
			$error = "<br>Cannot connect to the MySQL database.";
			$error .= "<br>MySQL error: " . mysql_error();
			$error .= "<br>Supplied info: ";
			$error .= "hostname: " . $this->_host . ', ';
			$error .= "username: " . $this->_user . ', ';
			$error .= "password: " . $this->_pass;
			$this->setError( $error );
			return $return;
			}

		// TRY TO SELECT DB
		if(! @mysql_select_db($this->_db, $this->_dbLink) ){
			$error = "Logged in but cannot select the specified MySQL database: " . $this->_db;
			$this->setError( $error );
			return $return;
			}

		$return = true;
		return $return;
		}

	function init() {
		if( ! $this->_dbLink ){
			// CONNECTS TO THE DATABASE
			if(! $this->_dbLink = @mysql_connect($this->_host, $this->_user, $this->_pass, true)){
				echo "Cannot login to the MySQL database with the specified login information. The following error occurs: <BR>";
				echo '<I>' . mysql_errno() . ': ' . mysql_error() . '</I>';
				exit;
				}

			if(! @mysql_select_db($this->_db, $this->_dbLink) ){
				echo "Cannot select the specified MySQL database. The following error occurs: <BR>";
				echo '<I>' . mysql_errno() . ': ' . mysql_error() . '</I>';
				exit;
				}
			}
		}

	function runQuery( $sqlQuery ){
		$return = false;
		if( ! $sqlQuery )
			return $return;

	/* add prefix */
		$sqlQuery = str_replace( '{PRFX}', $this->_prefix, $sqlQuery );

		if( $this->_debug ){
			echo '<BR>' . nl2br($sqlQuery) . '<BR>';
			}

		if( $this->_enableCache && isset($this->_cache[$sqlQuery]) ){
			$mySqlResult = $this->_cache[$sqlQuery];
			if( $this->_debug ){
				echo '==== ON CACHE =====<BR>';
				}
			}
		else {
			if( ! $mySqlResult = @mysql_query($sqlQuery, $this->_dbLink) ){
				$errStr = 'MySQL error - ' . mysql_errno() . ': ' . mysql_error();
				$this->setError( $errStr );
				echo 'MySQL error - ' . mysql_errno() . ': ' . mysql_error() . '. The query was:<BR><pre>' . $sqlQuery . '</pre>';
//				echo 'MySQL error occured<br>';
				return $return;
				}
			if( $this->_enableCache )
				$this->_cache[$sqlQuery] = $mySqlResult;
			$this->_queryCount++;
			}

		$result = new ntsMysqlResult( $mySqlResult );
		return $result;
		}

	function getInsertId(){
		return mysql_insert_id( $this->_dbLink );
		}

	function insert( $tblName, $paramsArray, $forcedTypes = array() ){
		$propsAndValues = $this->prepareInsertStatement( $paramsArray, $forcedTypes );
		$sql =<<<EOT
INSERT INTO {PRFX}$tblName 
$propsAndValues
EOT;

		$this->runQuery( $sql );
		}

	function prepareInsertStatement( $array, $forcedTypes = array(), $fieldsOrder = array() ){
		$columns = array();
		$values = array();

		$forcedTypes['meta_value'] = 'string';

		if( $fieldsOrder ){
			foreach( $fieldsOrder as $f ){
				$pName = $f;
				$pValue = $array[$f];
			/* is a string */
				if( is_array($pValue) ){
					$pValue = $pValue ? serialize( $pValue ) : '';
					}
				if ( strlen($pValue) == 0 || preg_match("/[^\d]/", $pValue) || ( isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'string') )  )
					if( ! (isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'number')) )
						$pValue = "'" . mysql_real_escape_string($pValue) . "'";
				$values[] = $pValue;
				}
			$valuesString = '(' . join( ', ', $values ) . ')';

			$sql = "VALUES $valuesString";
			}
		else {
			foreach( $array as $pName => $pValue ){
				$columns[] = $pName;
			/* is a string */
				if( is_array($pValue) ){
					$pValue = $pValue ? serialize( $pValue ) : '';
					}
				if ( strlen($pValue) == 0 || preg_match("/[^\d]/", $pValue) || ( isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'string') )  )
					if( ! (isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'number')) )
						$pValue = "'" . mysql_real_escape_string($pValue) . "'";
				$values[] = $pValue;
				}
			$columnsString = '(' . join( ', ', $columns ) . ')';
			$valuesString = '(' . join( ', ', $values ) . ')';

			$sql = "$columnsString VALUES $valuesString";
			}
		return $sql;
		}

	function prepareUpdateStatement( $array, $forcedTypes = array() ){
		reset( $array );

		$forcedTypes['meta_value'] = 'string';

		$pairs = array();
		foreach( $array as $pName => $pValue ){
			if( $pName == 'id' )
				continue;
			if( is_array($pValue) ){
				$pValue = $pValue ? serialize( $pValue ) : '';
				}
			if ( strlen($pValue) == 0 || preg_match("/[^\d]/", $pValue) || ( isset($forcedTypes[$pName]) && ($forcedTypes[$pName] == 'string') )  )
				$pValue = "'" . mysql_real_escape_string($pValue) . "'";
			$pairs[] = $pName . ' = ' . $pValue;
			}

		$sql = join( ', ', $pairs );
		return $sql;
		}
	
	function dumpTable( $tblName, $parsePrefix = false, $clearBefore = true ){
		$return = '';
		$prfx = $parsePrefix ? $this->_prefix : '{PRFX}';

		$fieldsDesc = array();
		$fieldsOrder = array();
		$sql = "DESCRIBE {PRFX}$tblName";
		$result = $this->runQuery( $sql );

		$priKey = '';
		while( $l = $result->fetch() ){
			if( strlen($l['Default']) )
				$l['Default'] = "DEFAULT '" . $l['Default'] . "'";
			if( $l['Null'] != 'NO' )
				$l['Null'] = 'NOT NULL';
			else
				$l['Null'] = '';
			if( $l['Key'] == 'PRI' )
				$priKey =  $l['Field'];

			$fieldsDesc[] = "`" . $l['Field'] . "` " . $l['Type'] . ' ' . $l['Null'] . ' ' . $l['Default'] . ' ' . $l['Extra'];
			$fieldsOrder[] = $l['Field'];
			}
		if( $priKey )
			$fieldsDesc[] = "PRIMARY KEY  (`" . $priKey . "`)";
		$propsAndValues = '(' . join( ", ", $fieldsDesc ) . ")";

		if( $clearBefore ){
			$return .= "DROP TABLE IF EXISTS $prfx" . "$tblName;\n";
			}
		$return .= "CREATE TABLE IF NOT EXISTS $prfx" . "$tblName $propsAndValues;\n";

		$sql = "SELECT * FROM {PRFX}$tblName";
		$result = $this->runQuery( $sql );
		while( $l = $result->fetch() ){
			if( $clearBefore )
				$propsAndValues = $this->prepareInsertStatement( $l, array(), $fieldsOrder );
			else
				$propsAndValues = $this->prepareInsertStatement( $l );

			if( (! $clearBefore) && $priKey ){
				$priKeyValue = $l[$priKey];
				$return .= "DELETE FROM $prfx" . "$tblName WHERE $priKey = $priKeyValue;\n";
				}
			$return .= "INSERT INTO $prfx" . "$tblName $propsAndValues;\n";
			}
		return $return;
		}

	function getTablesInDatabase(){
		$sql = 'SHOW tables';
		$return = array();
		$result = $this->runQuery( $sql );
		if( $result ){
			while( $e = $result->fetch() ){
				foreach( $e as $k => $v ){
					if( substr($v, 0, strlen($this->_prefix)) != $this->_prefix )
						continue;
					$v = substr( $v, strlen($this->_prefix) );
					$return[] = $v;
					}
				}
			}
		return $return;
		}
	}

class dbWrapper extends ntsMysqlWrapper {
	function dbWrapper(){
		parent::ntsMysqlWrapper( NTS_DB_HOST, NTS_DB_USER, NTS_DB_PASS, NTS_DB_NAME, NTS_DB_TABLES_PREFIX );
		$this->init();
		$this->_debug = false;
		}

	// Singleton stuff
	function &getInstance(){
		return ntsLib::singletonFunction( 'dbWrapper' );
		}
	}
?>