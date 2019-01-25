<?php
include_once( NTS_BASE_DIR . '/lib/ntsUserIntegrator.php' );

class wordpressIntegrator extends ntsUserIntegrator {
	var $db;

	function wordpressIntegrator(){
		$this->idIndex = 'ID';
		$this->init();
		}

/* SPECIFIC METHODS */
	function dumpUsers(){
		$table = 'users';
		return $this->db->dumpTable( $table, true, false );
		}

	function init(){	
	/* init database */
		global $table_prefix;
		$this->db = new ntsMysqlWrapper( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $table_prefix );
		$this->db->checkSettings();
		if( $dbError = $this->db->getError() ){
			echo "wordpress database error: $dbError";
			return;
			}
		$this->db->_debug = false;
		}

// this function adapts user information to common form.
// user info array should have: 'id', 'username', 'email', 'first_name', 'last_name', 'created'
	function convertFrom( $userInfo, $idOnly = false ){
		$return = $userInfo;

	/* id */
		unset( $return[$this->idIndex] );
		$return['id'] = $userInfo[$this->idIndex];
		if( $idOnly )
			return $return;

	/* username */
		unset( $return['user_login'] );
		$return['username'] = $userInfo['user_login'];

	/* email */
		unset( $return['user_email'] );
		$return['email'] = $userInfo['user_email'];

	/* first_name, last_name */
		if( isset($userInfo['first_name']) )
			$return['first_name'] = $userInfo['first_name'];
		if( isset($userInfo['last_name']) )
			$return['last_name'] = $userInfo['last_name'];

		unset( $return['user_nicename'] );

	/* created */
		unset( $return['user_registered'] );
		$return['created'] = strtotime( $userInfo['user_registered'] );

		return $return;
		}

	function convertTo( $userInfo ){
		$return = array();

	/* id */
		if( isset($userInfo['id']) )
			$return[$this->idIndex] = $userInfo['id'];

	/* username */
		if( isset($userInfo['username']) ){
			$return['user_login'] = $userInfo['username'];
			}

	/* email */
		// not changed
		if( isset($userInfo['email']) ){
			$return['user_email'] = $userInfo['email'];
			}

	/* first_name, last_name */
		if( isset($userInfo['first_name']) ){
			$return['first_name'] = $userInfo['first_name'];
			}
		if( isset($userInfo['last_name']) ){
			$return['last_name'] = $userInfo['last_name'];
			}

	/* created */
		if( isset($userInfo['created']) )
			$return['user_registered'] = date( "Y-m-d H:i:s", $userInfo['created'] );

	/* password */
		if( isset($userInfo['new_password']) ){
			$return['user_pass'] = wp_hash_password( $userInfo['new_password'] );
			}
		return $return;
		}

	function checkPassword( $username, $password ){
		$return = false;
		$userInfo = $this->getUserByUsername( $username );

		if( $userInfo ){
			$storedHash = $userInfo['user_pass'];
			$return = wp_check_password( $password, $storedHash, $userInfo['id'] );
			}
		return $return;
		}

	function createUser( $info, $metaInfo = array() ){
		/** WordPress Registration API */
		require_once( ABSPATH . WPINC . '/registration.php');

		$newPassword = $info['new_password'];
		$info = $this->convertTo( $info );
		$info['user_pass'] = $newPassword;
		
		$return = wp_insert_user( $info );
		if( is_wp_error($return) ){
			$this->setError( 'wordpress error: ' . $return->get_error_message() );
			$return = 0;
			}
		return $return;
		}

	function updateUser( $id, $info, $metaInfo = array() ){
		/** WordPress Registration API */
		require_once( ABSPATH . WPINC . '/registration.php');

		if( ! $info )
			return true;

		$newPass = isset($info['new_password']) ? $info['new_password'] : '';

		$info = $this->convertTo( $info );
		$info[ $this->idIndex ] = $id;
		if( $newPass ){
			$info[ 'user_pass' ] = $newPass;
			}

		$return = wp_update_user( $info );
		if( is_wp_error($return) ){
			$this->setError( 'wordpress error: ' . $return->get_error_message() );
			$return = 0;
			}
		return $return;
		}

	function deleteUser( $id ){
		return wp_delete_user( $id );
		}

	function login( $userId, $userPass ){
		$userInfo = $this->getUserById( $userId );
		$credentials = array(
			'user_login'	=> $userInfo['username'],
			'user_password'	=> $userPass,
			'remember'		=> true,
			);
		wp_signon( $credentials );
		}

	function logout(){
		wp_logout();
		}

	function currentUserId(){
		$currentUser = wp_get_current_user();
		$return = $currentUser->ID;
		return $return;
		}

	function queryUsers( $where = array(), $order = array(), $limit = '' ){
		$ids = array();
		if( isset($where['id']) ){
			$where[$this->idIndex] = $where['id'];
			unset($where['id']);
			}
		if( isset($where['first_name']) ){
			$where['(SELECT meta_value FROM {PRFX}usermeta WHERE meta_key="first_name" AND user_id={PRFX}users.ID)'] = $where['first_name'];
			unset( $where['first_name'] );
			}
		if( isset($where['last_name']) ){
			$where['(SELECT meta_value FROM {PRFX}usermeta WHERE meta_key="last_name" AND user_id={PRFX}users.ID)'] = $where['last_name'];
			unset( $where['last_name'] );
			}

		$whereString = ( $where ) ? 'WHERE ' . $this->buildWhere($where) : '';
		$limitString = ( $limit ) ? 'LIMIT ' . $limit : '';
		if( $order ){
			$ordCount = count( $order );
			for( $i = 0; $i < $ordCount; $i++ ){
				switch( $order[$i][0] ){
					case 'first_name':
						$order[$i][0] = '(SELECT meta_value FROM {PRFX}usermeta WHERE meta_key="first_name" AND user_id={PRFX}users.ID)';
						break;
					case 'last_name':
						$order[$i][0] = '(SELECT meta_value FROM {PRFX}usermeta WHERE meta_key="last_name" AND user_id={PRFX}users.ID)';
						break;
					default :
						$temp = array();
						$temp[ $order[$i][0] ] = 1;
						$temp = $this->convertTo( $temp );
						$newkeys = array_keys( $temp );
						$order[$i][ 0 ] = $newkeys[ 0 ];
						break;
					}
				}
			}
		$orderString = ( $order ) ? 'ORDER BY ' . $this->buildOrder($order) : '';

		$sql =<<<EOT
SELECT 
	ID
FROM 
	{PRFX}users 
$whereString  
$orderString 
$limitString 
EOT;

		$result = $this->db->runQuery( $sql );
		while( $u = $result->fetch() ){
			$ids[] = $u[$this->idIndex];
 			}

	/* count */
		$sql =<<<EOT
SELECT 
	COUNT(ID) AS count 
FROM 
	{PRFX}users 
$whereString 
EOT;

		$result = $this->db->runQuery( $sql );
		$u = $result->fetch();
		$count = $u['count'];

		return array( $ids, $count );
		}

	function loadUser( $userId ){
		$return = array();
		$sql =<<<EOT
SELECT 
	* 
FROM 
	{PRFX}users 
WHERE
	{PRFX}users.ID = $userId
LIMIT 1
EOT;

		$result = $this->db->runQuery( $sql );
		if( $result ){
			if( $u = $result->fetch() ){
				$uid = $u[$this->idIndex];
				$sql2 =<<<EOT
SELECT 
	meta_key, meta_value
FROM 
	{PRFX}usermeta 
WHERE
	user_id = $uid
EOT;

				$result2 = $this->db->runQuery( $sql2 );
				while( $u2 = $result2->fetch() ){
					$u[ $u2['meta_key'] ] = $u2['meta_value'];
					}

				$return = $u;
				}
			}
		return $return;
		}
/* END OF SPECIFIC METHODS */
	}
?>