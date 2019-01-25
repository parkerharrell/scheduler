<?php
global $NTS_OBJECT_CACHE, $NTS_OBJECT_PROPS_CONFIG;

class ntsObjectFactory {
	function clearCache( $className, $id ){
		global $NTS_OBJECT_CACHE;
		unset( $NTS_OBJECT_CACHE[$className][$id] );
		}

	function getAllIds( $className, $addonString = '' ){
		$return = array();
		$ntsdb =& dbWrapper::getInstance();
		$om =& objectMapper::getInstance();
		$tblName = $om->getTableForClass( $className );

		if( $om->isPropRegistered($className, 'show_order') )
			$addonString .= ' ORDER BY show_order ASC';

		$sql = "SELECT id FROM {PRFX}$tblName $addonString";
		$result = $ntsdb->runQuery( $sql );
		while( $u = $result->fetch() ){
			$return[] = $u['id'];
			}
		return $return;
		}

	function getAll( $className, $addonString = '' ){
		$return = array();
		$ids = ntsObjectFactory::getAllIds( $className, $addonString );
		reset( $ids );
		foreach( $ids as $id ){
			$o = ntsObjectFactory::get( $className );
			$o->setId( $id );
			$return[] = $o;
			}
		return $return;
		}

	function get( $className ){
		static $classes;
		if( ! isset($classes[$className]) ){
			$classes[$className] = '';
			$customClassName = 'nts' . ucfirst( $className );
			$customClassFileName = $customClassName . '.php';
			$realClassFileName = ntsLib::fileInCoreDirs( '/objects/' . $customClassFileName );
			if( $realClassFileName ){
				include_once( $realClassFileName );
				$classes[$className] = $customClassName;
				}
			}

		$customClassName = $classes[$className];
		if( $customClassName ){
			$return = new $customClassName;
			}
		else {
			$return = new ntsObject( $className );
			}

		return $return;
		}
	}

class ntsObject {
	var $className;
	var $props = array();
	var $updatedProps = array();
	var $id = 0;
	var $notFound = false;

	function ntsObject( $className ){
		global $NTS_OBJECT_PROPS_CONFIG;
		$this->className = $className;
		$this->id = 0;
		$this->props = array();
		$this->notFound = false;

		$myClasses = ( $this->className == 'user' ) ? array('customer', 'user') : array($this->className);
		reset( $myClasses );
		foreach( $myClasses as $myClass ){
			if( ! isset($NTS_OBJECT_PROPS_CONFIG[$myClass]) ){
				$om =& objectMapper::getInstance();
				list( $coreProps, $metaProps ) = $om->getPropsForClass( $myClass );
				$NTS_OBJECT_PROPS_CONFIG[ $myClass ] = array_merge( $coreProps, $metaProps );
				}
			}
		$this->resetUpdatedProps();
		}

	function getProp( $pName ){
		global $NTS_OBJECT_PROPS_CONFIG;
		$return = null;
		if( isset($this->props[$pName]) ){
			if(
				isset($NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]) && 
				$NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]['isArray'] && 
				( ! is_array($this->props[$pName]) )
				){
				$this->props[$pName] = trim( $this->props[$pName] );
				if( $this->props[$pName] )
					$this->props[$pName] = array( $this->props[$pName] );
				else
					$this->props[$pName] = array();
				}
			$return = $this->props[$pName];
			}
		else {
			$myClasses = ( $this->className == 'user' ) ? array('customer', 'user') : array($this->className);
			reset( $myClasses );
			foreach( $myClasses as $myClass ){
				if( isset($NTS_OBJECT_PROPS_CONFIG[$myClass][$pName]) ){
					$return = $NTS_OBJECT_PROPS_CONFIG[$myClass][$pName]['default'];
					break;
					}
				}
			}
		return $return;
		}

	function setId( $id, $load = true ){
		if( preg_match("/[^\d]/", $id) )
			return;
		$this->id = $id;
		if( ($this->id > 0) && $load ){
			$this->load();
			}
		}

	function notFound(){
		return $this->notFound;
		}

	function getId(){
		return $this->id;
		}

	function getClassName(){
		return $this->className;
		}

	function resetUpdatedProps(){
		$this->updatedProps = array();
		}

	function getMetaClass(){
		$useMetaIn = array( 'user', 'timeblock', 'service', 'appointment', 'location', 'pack', 'schedule', 'resource' );

		$return = '';
		$className = $this->getClassName();
		if( in_array($className, $useMetaIn) )
			$return = $className;

		return $return;
		}

	function load(){
		global $NTS_OBJECT_CACHE;
		$className = $this->getClassName();
		$id = $this->getId();
		if( ! $id )
			return;

		switch( $className ){
			case 'user':
//				echo "<h3>LOADING: $id</h3>";
				if( isset($NTS_OBJECT_CACHE[$className][$id]) ){
					$userInfo = $NTS_OBJECT_CACHE[$className][$id];
					}
				else {
					$uif =& ntsUserIntegratorFactory::getInstance();
					$integrator =& $uif->getIntegrator();
					$userInfo = $integrator->getUserById( $id );
					$NTS_OBJECT_CACHE[$className][$id] = $userInfo;
					}
				if( $userInfo ){
					$this->setByArray( $userInfo );
					$this->resetUpdatedProps();
					}
				else {
					$this->notFound = true;
					}
				break;

			default:
				$ntsdb =& dbWrapper::getInstance();
				$className = $this->getClassName();

				if( isset($NTS_OBJECT_CACHE[$className][$id]) ){
					$this->setByArray( $NTS_OBJECT_CACHE[$className][$id], true );
					$this->resetUpdatedProps();
					}
				else {
					$om =& objectMapper::getInstance();
					$tblName = $om->getTableForClass( $className );

					$sql = "SELECT * FROM {PRFX}$tblName WHERE id = $id";

					$result = $ntsdb->runQuery( $sql );
					if( $result && ($u = $result->fetch()) ){
						$metaClass = $this->getMetaClass();
					/* load meta as well */
						$metaInfo = $this->loadMeta();
						$u = array_merge( $u, $metaInfo );
//_print_r( $u );
						$this->setByArray( $u, true );
						$this->resetUpdatedProps();

						$NTS_OBJECT_CACHE[$className][$id] = $u;
						}
					else {
						$this->notFound = true;
						}
					}
				break;
			}
		}

	function loadMeta(){
		global $NTS_OBJECT_PROPS_CONFIG;

		$return = array();
		$objId = $this->getId();
		if( ! $objId )
			return;
		$metaClass = $this->getMetaClass();
		if( ! $metaClass )
			return $return;
		
		$ntsdb =& dbWrapper::getInstance();
		$sql =<<<EOT
SELECT 
	meta_name, meta_value, meta_data
FROM 
	{PRFX}objectmeta 
WHERE
	obj_id = $objId AND obj_class = "$metaClass"
EOT;

		$result = $ntsdb->runQuery( $sql );
		$slipNames = array();
		if( $result ){
			while( $n = $result->fetch() ){
				$n['meta_data'] = trim( $n['meta_data'] );

				$srlzd = '_srlzd';
				if( substr( $n['meta_name'], - strlen($srlzd) ) == $srlzd ){
					// serialized
					$realMetaName = substr( $n['meta_name'], 0, - strlen($srlzd) );
					$return[ $realMetaName ] = unserialize( $n['meta_value'] );
					$slipNames[] = $realMetaName;
					continue;
					}

				if( in_array($n['meta_name'], $slipNames) )
					continue;

				if( isset($return[$n['meta_name']]) ){
					if( isset($NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]) && $NTS_OBJECT_PROPS_CONFIG[$metaClass][$n['meta_name']]['isArray'] ){
						if( ! is_array($return[$n['meta_name']]) )
							$return[$n['meta_name']] = array( $return[$n['meta_name']] );
						if( strlen($n['meta_data']) )
							$return[$n['meta_name']][ $n['meta_value'] ] = $n['meta_data'];
						else {
							if( ! in_array($n['meta_value'], $return[$n['meta_name']] ) ) 
								$return[$n['meta_name']][] = $n['meta_value'];
							}
						}
					}
				else {
					if( strlen($n['meta_data']) )
						$return[ $n['meta_name'] ] = array( $n['meta_value'] => $n['meta_data'] );
					else
						$return[ $n['meta_name'] ] = $n['meta_value'];
					}
				}
			}

		return $return;
		}

	function deleteProp( $pName, $pValue ){
		if( ! isset($this->props[$pName]) )
			return;
			
		if( ! is_array($this->props[$pName]) )
			return;
			
		$result = array();
		reset( $this->props[$pName] );
		foreach( $this->props[$pName] as $v ){
			if( $v == $pValue )
				continue;
			$result[] = $v;
			}
		$this->props[$pName] = $result;
		}

	function setProp( $pName, $pValue, $fromStorage = false ){
		if( $pValue === 0 )
			$pValue = '0';

		global $NTS_OBJECT_PROPS_CONFIG;
	/* if updated */
		if( ! $fromStorage ){
			if( 
				(! isset($this->props[$pName])) || 
				($pValue != $this->props[$pName]) )
				{
				$this->updatedProps[] = $pName;
				}
			}

		if( isset($NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]) ){
			if( 
				$NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]['isCore'] && 
				$NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]['isArray'] 
				){
				if( $fromStorage ){
					$pValue = trim($pValue);
					if( strlen($pValue) )
						$pValue = unserialize( $pValue );
					else
						$pValue = array();
					$this->props[$pName] = $pValue;
					}
				else {
					if( is_array($pValue) ){
						$this->props[$pName] = $pValue;
						}
					else {
						if( ! isset($this->props[$pName]) )
							$this->props[$pName] = array();
						$pValue = trim($pValue);
						if( strlen($pValue) )
							$this->props[$pName][] = $pValue;
						}
					}
				}
			else {
				$this->props[$pName] = $pValue;
				}
			}
		else {
			$this->props[$pName] = $pValue;
			}
		}

	function setByArray( $array, $fromStorage = false ){
		reset( $array );
		foreach( $array as $pName => $pValue ){
			$this->setProp( $pName, $pValue, $fromStorage );
			if( $pName == 'id' )
				$this->setId( $pValue, false );
			}
		}

	function getByArray( $split = false, $updated = false ){
		global $NTS_OBJECT_PROPS_CONFIG;
		if( $updated ){
			$props = array();
			reset( $this->updatedProps );
			foreach( $this->updatedProps as $upn ){
				$props[ $upn ] = $this->getProp( $upn );
				}
			}
		else {
//			$props = $this->props;
			reset( $this->props );
			foreach( $this->props as $k => $v ){
				$props[ $k ] = $this->getProp( $k );
				}

		/* check if any default props missing */
			reset( $NTS_OBJECT_PROPS_CONFIG[$this->className] );
			foreach( $NTS_OBJECT_PROPS_CONFIG[$this->className] as $pName => $pConfig ){
				if( ! isset($props[$pName]) )
					$props[$pName] = $NTS_OBJECT_PROPS_CONFIG[$this->className][$pName]['default'];
				}
			}

		if( $split ){
			$core = array();
			$meta = array();

			$om =& objectMapper::getInstance();
			list( $coreProps, $metaProps ) = $om->getPropsForClass( $this->getClassName() );
			$corePropsNames = array_keys( $coreProps );

			reset( $props );
			foreach( $props as $k => $v ){
				if( in_array($k, $corePropsNames) )
					$core[ $k ] = $v;
				else
					$meta[ $k ] = $v;
				}
			$return = array( $core, $meta );
			}
		else {
			$return = $props;
			}
		return $return;
		}
	}
?>