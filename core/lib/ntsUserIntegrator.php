<?php
class ntsUserIntegrator {
	var $usersById = array();
	var $usersByUsername = array();
	var $cacheCount = array();
	var $idIndex = 'id';
	var $db = null;
	var $error = '';

	function ntsUserIntegrator(){
		$this->init();
		}

	function getError(){
		return $this->error;
		}

	function setError( $err ){
		$this->error = $err;
		}

	function getUsers( $where = array(), $order = array(), $limit = '', $userStatus = '' ){
		$return = array();

	/* user ids */
		$ids = $this->loadUsers( $where, $order, $limit, $userStatus );

		reset( $ids );
		foreach( $ids as $id ){
			$u = $this->loadUser( $id );
			$u = $this->convertFrom( $u );

			$metaInfo = $this->loadUserMeta( $id );
			$u = array_merge( $u, $metaInfo );
			if( isset($u['id']) ){
				$return[] = $u;
				}
			}
		return $return;
		}

	function loadUsers( $where = array(), $order = array(), $limit = '', $userStatus = '' ){
		$return = array();
		$whereString = serialize( $where ) . $userStatus;

		if( isset($where['_role']) && ($where['_role'] == '="customer"') )
		{
			$where2 = array(
				'_role'	=> '<>"customer"'
				);
			// find thouse who are not customers 
			list( $noIds, $count ) = $this->queryUsersMeta( $where2 );

			if( $noIds )
			{
				$where['id'] = ' NOT IN(' . join(',', $noIds) . ')';
			}
			unset( $where['_role'] );
		}

	/* split where in builtin and custom part */
		list( $whereB, $whereC ) = $this->_splitWhere( $where );
		list( $orderB, $orderC ) = $this->_splitOrder( $order );

	/* check if we need to force user status search before */
		if( $userStatus && ($userStatus != '-any-') ){
			$whereC['_restriction'] = "=\"$userStatus\"";
			
//echo '<h2>AH</h2>';
//			_print_r( $whereB );
			}

	/* IF BOTH BUILTIN AND CUSTOM HERE */
		if( ($whereB || $orderB) && ($whereC || $orderC) ){
			if( $orderB && $orderC ){
			/* can't order by both builtin and meta */
				echo "sorry I can't order listings by both builtin and meta properties, skipping meta order<br>";
				$orderC = array();
				}

			if( $orderC ){
//				echo "<h3>BACA</h3>";
			/* first builtin then custom ordered */
				$limitB = '';

				$statusIds = isset($whereB[' id ']) ? $whereB[' id '] : '';
				$whereB = $this->convertTo( $whereB );
				if( $statusIds )
					$whereB[ ' ' . $this->idIndex . ' ' ] = $statusIds;

				list( $ids, $count ) = $this->queryUsers( $whereB, $orderB, $limitB );

				if( $ids ){
					$whereC['id'] = ' IN (' . join(',', $ids) . ')';
					list( $ids, $count ) = $this->queryUsersMeta( $whereC, $orderC, $limit, $userStatus );
					}
				}
			else {
//				echo "<h3>CABA</h3>";

			/* first custom then builtin ordered */
				$limitC = '';
				$noIds = array();
				$ids = array();
				if( isset($whereC['_role']) && ($whereC['_role'] == '="customer"') ){
					$whereC['_role'] = '<>"customer"';
					list( $noIds, $count ) = $this->queryUsersMeta( $whereC, $orderC, $limitC, $userStatus );
					unset( $whereC['_role'] );

					if( $whereC ){
						if( $ids )
							$whereC['id'] = 'NOT IN(' . join(',', $ids) . ')';
						list( $ids, $count ) = $this->queryUsersMeta( $whereC, $orderC, $limitC, $userStatus );
						}
					}
				else {
					list( $ids, $count ) = $this->queryUsersMeta( $whereC, $orderC, $limitC, $userStatus );
					}

				if( $ids || $noIds ){
					if( $ids ){
						if( isset($whereB['id']) )
							$whereB['id '] = $whereB['id'];
						$whereB['id'] = ' IN (' . join(',', $ids) . ')';

						$statusIds = isset($whereB[' id ']) ? $whereB[' id '] : '';
						$whereB = $this->convertTo( $whereB );
						if( $statusIds )
							$whereB[ ' ' . $this->idIndex . ' ' ] = $statusIds;
						}
					if( $noIds ){
						$whereB = $this->convertTo( $whereB );
						if( $noIds )
							$whereB[' id  '] = ' NOT IN (' . join(',', $noIds) . ')';
						}

					list( $ids, $count ) = $this->queryUsers( $whereB, $orderB, $limit );
					}
				}
			}

	/* ELSE CUSTOM ONLY */
		elseif( ($whereC || $orderC) ){
//			echo "CACA";
			list( $ids, $count ) = $this->queryUsersMeta( $whereC, $orderC, $limit, $userStatus );
			}

	/* ELSE IF BUILTIN ONLY, OR ALL */
		elseif( ($whereB || $orderB) || 1 ){
//			echo "<h3>BABA</h3>";
			$statusIds = isset($whereB[' id ']) ? $whereB[' id '] : '';
			$whereB = $this->convertTo( $whereB );
			if( $statusIds )
				$whereB[ ' ' . $this->idIndex . ' ' ] = $statusIds;
			list( $ids, $count ) = $this->queryUsers( $whereB, $orderB, $limit, $userStatus );
			}

	/* cache count */
		$this->cacheCount[ $whereString ] = $count;

		return $ids;
		}

	function countUsers( $where = array(), $userStatus = '' ){
		$cacheString = serialize( $where ) . $userStatus;

	/* ALREADY HAVE THIS? */
		if( isset($this->cacheCount[$cacheString]) ){
			$return = $this->cacheCount[$cacheString];
//			echo "<b>ON CACHE = $return</b><br>";
			}
		else {
//			echo "<b>NOT ON CACHE</b><br>";
			/* create a simple query to store count */
			$this->loadUsers( $where, array(), '1' );
			$return = $this->cacheCount[$cacheString];
			}
		return $return;
		}

	function loadUserMeta( $userId ){
		global $NTS_OBJECT_PROPS_CONFIG;

		$mainDb =& dbWrapper::getInstance();
		$return = array();
		$metaClass = "user";

		global $NTS_CURRENT_VERSION_NUMBER;
		if( $NTS_CURRENT_VERSION_NUMBER >= 4500 ){
			$cols = 'meta_name, meta_value, meta_data';
			}
		else {
			$cols = 'meta_name, meta_value';
			}

		$sql =<<<EOT
SELECT 
	$cols
FROM 
	{PRFX}objectmeta 
WHERE
	obj_id = $userId AND obj_class = "$metaClass"
EOT;

		$result = $mainDb->runQuery( $sql );
		if( $result ){
			while( $n = $result->fetch() ){
				if( isset($return[$n['meta_name']]) ){
					if( isset($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]) && $NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] ){
						if( ! is_array($return[$n['meta_name']]) )
							$return[$n['meta_name']] = array( $return[$n['meta_name']] );
						if( isset($n['meta_data']) && strlen($n['meta_data']) )
							$return[$n['meta_name']][ $n['meta_value'] ] = $n['meta_data'];
						else {
							if( ! in_array($n['meta_value'], $return[$n['meta_name']] ) ) 
								$return[$n['meta_name']][] = $n['meta_value'];
							}
						}
					}
				else {
					if( isset($n['meta_data']) && strlen($n['meta_data']) )
						$return[ $n['meta_name'] ] = array( $n['meta_value'] => $n['meta_data'] );
					else
						$return[ $n['meta_name'] ] = $n['meta_value'];
					}
				}
			}

		return $return;
		}

	function queryUsersMeta_old( $whereC = array(), $orderC = array(), $limit = '', $userStatus = '' ){
		$ids = array();

		$limitString = ( $limit ) ? 'LIMIT ' . $limit : '';

		$alsoId = '';
		if( isset($whereC['id']) ){
			$alsoId = $whereC['id'];
			unset( $whereC['id'] );
			}

		$orderString = '';
		if( $orderC ){
			$orderQueries = array();
			reset( $orderC );
			foreach( $orderC as $oa ){
				$k = $oa[0];
				$v = isset($oa[1]) ? $oa[1] : 'DESC';
				$q =<<<EOT
(
SELECT
	meta_value
FROM
	{PRFX}objectmeta AS tmeta
WHERE
	tmeta.obj_class = {PRFX}objectmeta.obj_class AND 
	tmeta.obj_id = {PRFX}objectmeta.obj_id AND 
	meta_name = "$k"
) $v
EOT;
				$orderQueries[] = $q;
				}
			$orderString = 'ORDER BY ' . join( ",\n", $orderQueries );
			}

	/* init in case there're no conditions */
		$tempWhere = array(
			'obj_class'		=> "=\"user\"",
			);

		$tempWhereString = 'WHERE ' . $this->buildWhere($tempWhere);
		$finalQuery =<<<EOT
SELECT 
	DISTINCT(obj_id)
FROM 
	{PRFX}objectmeta 
$tempWhereString
$orderString
$limitString
EOT;

		$countQuery =<<<EOT
SELECT 
	COUNT(obj_id) AS count
FROM 
	{PRFX}objectmeta 
$tempWhereString
EOT;

	/* process conditions */
		$stackCount = 0;
		$stackIn = array();
		reset( $whereC );
		foreach( $whereC as $k => $v ){
			$tempWhere = array(
				'obj_class'		=> "=\"user\"",
				'meta_name'		=> "=\"$k\"",
				'meta_value'	=> $v,
				);
			if( $stackCount ){
				$tempWhere[ 'obj_id' ] = " IN (\n" . $stackIn[$stackCount-1] . "\n)";
				}
			$tempWhereString = 'WHERE ' . $this->buildWhere($tempWhere);

		/* last one */
			$stackCount++;
			if( $stackCount == count($whereC) ){
				if( $alsoId ){
//					echo "<h3>also = '$alsoId'</h3>";
					$tempWhere[ 'obj_id ' ] = $alsoId;
					}

				$tempWhereString = 'WHERE ' . $this->buildWhere($tempWhere);
				$finalQuery =<<<EOT
SELECT 
	DISTINCT(obj_id)
FROM 
	{PRFX}objectmeta 
$tempWhereString
$orderString
$limitString
EOT;

				$countQuery =<<<EOT
SELECT 
	COUNT(obj_id) AS count
FROM 
	{PRFX}objectmeta 
$tempWhereString
EOT;
				}
			else {
				$stackIn[] =<<<EOT
SELECT 
	DISTINCT(obj_id)
FROM 
	{PRFX}objectmeta 
$tempWhereString
EOT;
				}
			}

if( count($whereC) > 1 )
{
	echo $finalQuery;
	exit;
}

//_print_r( $finalQuery );
		$mainDb =& dbWrapper::getInstance();
		$result = $mainDb->runQuery( $finalQuery );
		while( $u = $result->fetch() ){
			$ids[] = $u['obj_id'];
 			}

	/* count */
		$result = $mainDb->runQuery( $countQuery );
		$u = $result->fetch();
		$count = $u['count'];

		return array( $ids, $count );
		}

	function queryUsersMeta( $whereC = array(), $orderC = array(), $limit = '', $userStatus = '' ){
		$ids = array();

		$limitString = ( $limit ) ? 'LIMIT ' . $limit : '';

		$alsoId = '';
		if( isset($whereC['id']) ){
			$alsoId = $whereC['id'];
			unset( $whereC['id'] );
			}

		$orderString = '';
		if( $orderC ){
			$orderQueries = array();
			reset( $orderC );
			foreach( $orderC as $oa ){
				$k = $oa[0];
				$v = isset($oa[1]) ? $oa[1] : 'DESC';
				$q =<<<EOT
(
SELECT
	meta_value
FROM
	{PRFX}objectmeta AS tmeta
WHERE
	tmeta.obj_class = {PRFX}objectmeta.obj_class AND 
	tmeta.obj_id = {PRFX}objectmeta.obj_id AND 
	meta_name = "$k"
) $v
EOT;
				$orderQueries[] = $q;
				}
			$orderString = 'ORDER BY ' . join( ",\n", $orderQueries );
			}

	/* init in case there're no conditions */
		$tempWhere = array(
			'obj_class'		=> "=\"user\"",
			);

		$tempWhereString = 'WHERE ' . $this->buildWhere($tempWhere);
		$finalQuery =<<<EOT
SELECT 
	DISTINCT(obj_id)
FROM 
	{PRFX}objectmeta 
$tempWhereString
$orderString
$limitString
EOT;

		$countQuery =<<<EOT
SELECT 
	COUNT(obj_id) AS count
FROM 
	{PRFX}objectmeta 
$tempWhereString
EOT;

	/* process conditions */
		foreach( $whereC as $k => $v )
		{
			$tempWhere = array(
				'obj_class'		=> "=\"user\"",
				'meta_name'		=> "=\"$k\"",
				'meta_value'	=> $v,
				);
		}
	
	
	
		$stackCount = 0;
		$stackIn = array();
		reset( $whereC );
		foreach( $whereC as $k => $v ){
			$tempWhere = array(
				'obj_class'		=> "=\"user\"",
				'meta_name'		=> "=\"$k\"",
				'meta_value'	=> $v,
				);
			if( $stackCount ){
				$tempWhere[ 'obj_id' ] = " IN (\n" . $stackIn[$stackCount-1] . "\n)";
				}
			$tempWhereString = 'WHERE ' . $this->buildWhere($tempWhere);

		/* last one */
			$stackCount++;
			if( $stackCount == count($whereC) ){
				if( $alsoId ){
//					echo "<h3>also = '$alsoId'</h3>";
					$tempWhere[ 'obj_id ' ] = $alsoId;
					}

				$tempWhereString = 'WHERE ' . $this->buildWhere($tempWhere);
				$finalQuery =<<<EOT
SELECT 
	DISTINCT(obj_id)
FROM 
	{PRFX}objectmeta 
$tempWhereString
$orderString
$limitString
EOT;

				$countQuery =<<<EOT
SELECT 
	COUNT(obj_id) AS count
FROM 
	{PRFX}objectmeta 
$tempWhereString
EOT;
				}
			else {
/*
				$stackIn[] =<<<EOT
SELECT 
	DISTINCT(obj_id)
FROM 
	{PRFX}objectmeta 
$tempWhereString
EOT;
*/
				$stackQuery =<<<EOT
SELECT 
	DISTINCT(obj_id)
FROM 
	{PRFX}objectmeta 
$tempWhereString
EOT;

				$tempIds = array();
				$mainDb =& dbWrapper::getInstance();
//				echo "sq = $stackQuery<br>";
				$resultTemp = $mainDb->runQuery( $stackQuery );
				while( $uTemp = $resultTemp->fetch() ){
					$tempIds[] = $uTemp['obj_id'];
		 			}
				if( $tempIds )
				{
					$stackIn[] = join( ',', $tempIds );
				}
				else
				{
					$finalQuery = '';
					break;
				}
				}
			}

//_print_r( $finalQuery );
		$mainDb =& dbWrapper::getInstance();

		$count = 0;
		if( $finalQuery )
		{
			$result = $mainDb->runQuery( $finalQuery );
			while( $u = $result->fetch() ){
				$ids[] = $u['obj_id'];
	 			}

		/* count */
			$result = $mainDb->runQuery( $countQuery );
			$u = $result->fetch();
			$count = $u['count'];
		}

		return array( $ids, $count );
		}

/* reloaded methods */
	function init(){
		$this->db =& dbWrapper::getInstance();
		}

	// this function adapts user information to common form.
	// user info array should have: 'id', 'username', 'first_name', 'last_name', 'created'
	function convertFrom( $userInfo ){
		$return = $userInfo;
		// built-in, no conversion required
		return $return;
		}

	function convertTo( $userInfo ){
		$return = $userInfo;
		// built-in, no conversion required
		
	/* password */
		if( isset($userInfo['new_password']) ){
			$return['password'] = md5( $userInfo['new_password'] );
			unset( $return['new_password'] );
			}

		return $return;
		}
/* end of reloaded methods */

/* internal methods */
	function buildWhere( $where ){
		$parts = array();
		reset( $where );
		foreach( $where as $key => $value ){
			$parts[] = $key . $value;
			}
		$whereString = join( ' AND ', $parts );
		return $whereString;
		}

	function buildOrder( $order ){
		$parts = array();
		reset( $order );
		foreach( $order as $oa ){
			$how = isset($oa[1]) ? $oa[1] : 'DESC';
			$parts[] = $oa[0] . ' ' . $how;
			}
		$orderString = join( ', ', $parts );
		return $orderString;
		}

	function getUserById( $userId ){
		if( $userId <= 0 ){
			$return = array(
				'id'		=> 0,
				);
			return $return;
			}

		if( ! isset($this->usersById[$userId]) ){
			$userInfo = $this->loadUser( $userId );
			if( $userInfo ){
				$userInfo = $this->convertFrom( $userInfo );
				$metaInfo = $this->loadUserMeta( $userId );
				$userInfo = array_merge( $userInfo, $metaInfo );
				$this->usersById[$userId] = $userInfo;
				}
			else {
				$this->usersById[$userId] = array();
				}
			}

		return $this->usersById[$userId];
		}

	function getUserByUsername( $userName ){
		if( ! isset($this->usersByUsername[$userName]) ){
			$userInfo = array();
			$users = $this->getUsers( 
				array(
					'username' => "='" . mysql_real_escape_string($userName) . "'"
					)
				);

			if( $users )
				$userInfo = $users[0];

			$this->usersByUsername[$userName] = $userInfo;
			}

		return $this->usersByUsername[$userName];
		}

	function getUserByEmail( $userEmail ){
		$return = array();
		$users = $this->getUsers( 
			array(
				'email' => "='" . mysql_real_escape_string($userEmail) . "'"
				)
			);

		if( $users )
			$return = $users[0];
		return $return;
		}

	function _splitWhere( $where ){
		$om =& objectMapper::getInstance();
		list( $coreProps, $metaProps ) = $om->getPropsForClass( 'user' );
		$builtinFields = array_keys( $coreProps );
		$builtinFields[] = 'id';

	/* split where in builtin and custom part */
		$whereB = array();
		$whereC = array();

		reset( $where );
		foreach( $where as $k => $v ){
			if( in_array($k, $builtinFields) ){
				$whereB[ $k ] = $v;
				}
			else{
				$whereC[ $k ] = $v;
				}
			}
		$return = array( $whereB, $whereC );
		return $return;
		}

	function _splitOrder( $order ){
		$om =& objectMapper::getInstance();
		list( $coreProps, $metaProps ) = $om->getPropsForClass( 'user' );
		$builtinFields = array_keys( $coreProps );

	/* split where in builtin and custom part */
		$orderB = array();
		$orderC = array();

		reset( $order );
		foreach( $order as $oa ){
			if( in_array($oa[0], $builtinFields) ){
				$orderB[] = $oa;
				}
			else {
				$orderC[] = $oa;
				}
			}
		$return = array( $orderB, $orderC );
		return $return;
		}
	}
?>