<?php
$app =& ntsApplication::getInstance();

$ntsdb =& dbWrapper::getInstance();

/* delete restriction */
$app->deleteMeta( $object, '_restriction', 'email_not_confirmed' );
$object->deleteProp( '_restriction', 'email_not_confirmed' );
?>