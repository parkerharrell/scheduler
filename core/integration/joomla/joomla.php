<?php
include_once( NTS_BASE_DIR . '/lib/ntsUserIntegrator.php' );

class joomlaIntegrator extends ntsUserIntegrator {
	var $db;

	function wordpressIntegrator(){
		$this->idIndex = 'id';
		$this->init();
		}

/* SPECIFIC METHODS */
	function dumpUsers(){
		$table = 'users';
		return $this->db->dumpTable( $table, true, false );
		}

	function init(){
	/* init database */
		$config = new JConfig();

		$this->db = new ntsMysqlWrapper( $config->host, $config->user, $config->password, $config->db, $config->dbprefix );
		$this->db->checkSettings();
		if( $dbError = $this->db->getError() ){
			echo "joomla database error: $dbError";
			return;
			}
		$this->db->_debug = false;
		}

// this function adapts user information to common form.
// user info array should have: 'id', 'username', 'email', 'first_name', 'last_name', 'created'
	function convertFrom( $userInfo, $idOnly = false ){
		$return = array();

	/* id */
		$return['id'] = $userInfo['id'];
		if( $idOnly )
			return $return;

	/* username */
		$return['username'] = $userInfo['username'];

	/* email */
		$return['email'] = $userInfo['email'];

	/* first_name, last_name */
		$exploded = explode( ' ', $userInfo['name'], 2 );
		$return['first_name'] = isset($exploded[0]) ? $exploded[0] : '';
		$return['last_name'] = isset($exploded[1]) ? $exploded[1] : '';

	/* created */
		$return['created'] = strtotime( $userInfo['registerDate'] );

		return $return;
		}

	function convertTo( $userInfo ){
		$return = array();

	/* id */
		if( isset($userInfo['id']) )
			$return['id'] = $userInfo['id'];

	/* username */
		if( isset($userInfo['username']) ){
			$return['username'] = $userInfo['username'];
			}

	/* email */
		if( isset($userInfo['email']) ){
			$return['email'] = $userInfo['email'];
			}

	/* parse 'first_name' & 'last_name' to 'name' */
		$search = false;
		if( isset($userInfo['first_name']) || isset($userInfo['last_name']) ){
			if( isset($userInfo['first_name']) ){
				$string = trim($userInfo['first_name']);
				if( (substr($string, 0, 1) == '=') || (substr($string, 0, 5) == 'LIKE ') ){
					$search = true;
					}
				}
			if( isset($userInfo['last_name']) ){
				$string = trim($userInfo['last_name']);
				if( (substr($string, 0, 1) == '=') || (substr($string, 0, 5) == 'LIKE ') ){
					$search = true;
					}
				}
			}

		if( $search ){
			$names = array( 'first_name', 'last_name' );
			$comparisons = array();
			$values = array();
			foreach( $names as $n ){
				if( isset($userInfo[$n]) ){
					preg_match( '/(.+)\"(.+)\"/', $userInfo[$n], $ma );
					$comparisons[] = trim($ma[1]);
					$values[] = trim($ma[2]);
					}
				else {
					$comparisons[] = 'LIKE';
					$values[] = '%';
					}
				}
			$final = (in_array('LIKE', $comparisons)) ? ' LIKE ' : '=';
			$final .= '"' . join( ' ', $values ) . '"';
			$return['name'] = $final;
			}
		else {
		/* first_name, last_name */
			if( isset($userInfo['first_name']) && isset($userInfo['last_name'])){
				$return['name'] = $userInfo['first_name'] . ' ' . $userInfo['last_name'];
				}
			}

	/* created */
		if( isset($userInfo['created']) )
			$return['registerDate'] = date( "Y-m-d H:i:s", $userInfo['created'] );

	/* password */
		if( isset($userInfo['new_password']) ){
			$return['password'] = $userInfo['new_password'];
			$return['password2'] = $userInfo['new_password'];
			}
		return $return;
		}

	function checkPassword( $username, $password ){
			$return = false;

		jimport('joomla.user.authentication');
		$credentials = array(
			'username'	=> $username,
			'password'	=> $password
			);
		$options = array();
		$ja = & JAuthentication::getInstance();
		$response = $ja->authenticate( $credentials, $options );
		if( $response->status === JAUTHENTICATE_STATUS_SUCCESS )
			$return = true;
		else
			$return = false;

		return $return;
		}

	function createUser( $info, $metaInfo = array() ){
		// get the ACL
		$acl =& JFactory::getACL();
		jimport('joomla.application.component.helper'); // include libraries/application/component/helper.php
		$usersParams = &JComponentHelper::getParams( 'com_users' ); // load the Params

		$role = isset($metaInfo['_role']) ? $metaInfo['_role'][0] : 'customer';

		$info = $this->convertTo( $info );

		switch( $role ){
			case 'admin':
				// current user
				$currentUser =& JFactory::getUser();
				$userGroups = $currentUser->getAuthorisedGroups();
				break;
			default:
				$userConfig = JComponentHelper::getParams('com_users');
			// Default to Registered.
				$defaultUserGroup = $userConfig->get('new_usertype', 2);
				$userGroups = array( $defaultUserGroup );
				break;
			}
		$user = JFactory::getUser( 0 );
        $user->set('groups', $userGroups);

		$info['sendEmail'] = 1; // should the user receive system mails?
		$info['block'] = 0; // don't block the user

		$user->bind( $info );			
		if( ! $user->save() ){
			$error = $user->getError();
			$this->setError( $error );
//			JError::raiseWarning('', JText::_( $user->getError()));
			return false;
			}
		else {
			return $user->id;
			}
		}

	function updateUser( $id, $info, $metaInfo = array() ){
		if( ! $info )
			return true;

		$acl =& JFactory::getACL();
		jimport('joomla.application.component.helper'); // include libraries/application/component/helper.php
		$usersParams = &JComponentHelper::getParams( 'com_users' ); // load the Params

	// parse name
		$user = JFactory::getUser( $id );
		$oldInfo = array(
			'name'	=> $user->get('name'),
			);
		$oldInfo = $this->convertFrom( $oldInfo );

		if( isset($info['last_name']) || isset($info['first_name']) ){
			if( ! isset($info['last_name']) )
				$info['last_name'] = $oldInfo['last_name'];
			if( ! isset($info['first_name']) )
				$info['first_name'] = $oldInfo['first_name'];
			}

		$role = isset($metaInfo['_role']) ? $metaInfo['_role'] : 'customer';
		$info = $this->convertTo( $info );

		$info[ $this->idIndex ] = $id;
		$user->bind( $info );

		if( ! $user->save() ){
			$error = $user->getError();
			$this->setError( $error );
//			JError::raiseWarning('', JText::_( $user->getError()));
			return false;
			}
		else {
			return $user->id;
			}
		}

	function deleteUser( $id ){
		$user = JFactory::getUser( $id );
		return $user->delete();
		}

	function login( $userId, $userPass ){
		$userInfo = $this->getUserById( $userId );

		$app = JFactory::getApplication();
		$credentials = array(
			'username'	=> $userInfo['username'],
			'password'	=> $userPass,
			);
		$result = $app->login($credentials, array());
		}

	function logout(){
		$app = JFactory::getApplication();
		$userid = JRequest::getInt('uid', null);
		$options = array();
		$result = $app->logout($userid, $options);
		}

	function currentUserId(){
		$currentUser =& JFactory::getUser();
		$return = $currentUser->id;
		return $return;
		}

	function queryUsers( $where = array(), $order = array(), $limit = '' ){
		$ids = array();

		$whereString = ( $where ) ? 'WHERE ' . $this->buildWhere($where) : '';
		$limitString = ( $limit ) ? 'LIMIT ' . $limit : '';

		$sql =<<<EOT
SELECT 
	id
FROM 
	{PRFX}users 
$whereString 
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
	{PRFX}users.id = $userId
LIMIT 1
EOT;

		$result = $this->db->runQuery( $sql );
		if( $result ){
			$return = $result->fetch();
			}
		return $return;
		}
/* END OF SPECIFIC METHODS */
	}
?>