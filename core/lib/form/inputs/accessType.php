<?php
$conf['options'] = array(
	array('write',	M('View and Update') ),
	array('read',	M('View Only') ),
	array('hidden',	M('Hidden') ),
	);
require( dirname(__FILE__) . '/select.php' );
?>