<?php
switch( $inputAction ){
	case 'display':
		$om =& objectMapper::getInstance();
		$ntsdb =& dbWrapper::getInstance();

		// options
		$className = $conf['attr']['_class'];
		$propName = $conf['id'];
		$conf['options'] = array();

		list( $core, $meta ) = $om->getPropsForClass( $className );
		if( in_array($propName, array_keys($core) ) ){
			$tbl = $om->getTableForClass( $className );

		// core property
			$sql =<<<EOT
SELECT
	DISTINCT($propName) AS pvalue
FROM
	{PRFX}$tbl
ORDER BY pvalue ASC
EOT;
			}
		else {
			$sql =<<<EOT
SELECT
	DISTINCT(meta_value) AS pvalue
FROM	
	{PRFX}objectmeta
WHERE
	meta_name = "$propName" AND
	obj_class = "$className"
ORDER BY pvalue ASC
EOT;
			}

		$result = $ntsdb->runQuery( $sql );

		while( $fieldInfo = $result->fetch() ){
			$fieldInfo['pvalue'] = trim( $fieldInfo['pvalue'] );
			if( ! $fieldInfo['pvalue'] )
				continue;
			$conf['options'][] = array( $fieldInfo['pvalue'], $fieldInfo['pvalue'] );
			}
		break;
	}
?>
<?php 
if( isset($allowNew) && $allowNew )
	require( dirname(__FILE__) . '/selectNew.php' );
else
	require( dirname(__FILE__) . '/select.php' );
?>