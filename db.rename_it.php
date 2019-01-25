<?php
/* rename this file to db.php and enter your MySQL database login details */
define( 'NTS_DB_HOST',			'localhost');
define( 'NTS_DB_USER',			'my_username');
define( 'NTS_DB_PASS',			'my_password');
define( 'NTS_DB_NAME',			'hitappoint4');

/* usually not required to change */
if( ! defined('NTS_DB_TABLES_PREFIX') )
	define( 'NTS_DB_TABLES_PREFIX',	'ha45_');
?>