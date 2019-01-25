<?php
$app =& ntsApplication::getInstance();

/* delete all restrictions */
$app->deleteMeta( $object, '_restriction' );
$actionResult = 1;
?>