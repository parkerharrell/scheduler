<?php
class ntsEmailLogger {
	var $params = array();
/*
	'to'
	'from'
	'from_name'
	'subject'
	'body'
	'alt_body'
*/

	function ntsEmailLogger(){
		$this->params = array();
		}

	function setParam( $pName, $pValue ){
		$this->params[ $pName ] = $pValue;
		}

	function getParams(){
		return $this->params;
		}

	function add(){
		$ntsdb =& dbWrapper::getInstance();

		$tblName = 'emaillog';
		$paramsArray = $this->getParams();
		$paramsArray['sent_at'] = time();
		$propsAndValues = $ntsdb->prepareInsertStatement( $paramsArray );

		$sql =<<<EOT
INSERT INTO {PRFX}$tblName 
$propsAndValues
EOT;

		$result = $ntsdb->runQuery( $sql );
		}
	}
?>