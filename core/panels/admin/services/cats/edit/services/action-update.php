<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$catId = $req->getParam( '_id' );

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );

if( $form->validate($req) ){
	$formValues = $form->getValues();
	$newServices = $formValues['services']; 

/* get current */
	$sql =<<<EOT
SELECT
	{PRFX}services.id
FROM
	{PRFX}services
INNER JOIN
	{PRFX}objectmeta
ON
	{PRFX}objectmeta.obj_id = {PRFX}services.id
WHERE
	{PRFX}objectmeta.obj_class = "service" AND 
	{PRFX}objectmeta.meta_name = "_service_cat" AND
	{PRFX}objectmeta.meta_value = $catId
EOT;
	$currentServices = array();
	$result = $ntsdb->runQuery( $sql );
	while( $e = $result->fetch() ){
		$currentServices[] = $e['id'];
		}

/* see what to delete */
	$toDelete = array_diff( $currentServices, $newServices );
	if( $toDelete ){
		$toDeleteIn = join( ',', $toDelete );
		$sql =<<<EOT
DELETE FROM
	{PRFX}objectmeta
WHERE
	{PRFX}objectmeta.obj_class = "service" AND 
	{PRFX}objectmeta.meta_name = "_service_cat" AND
	{PRFX}objectmeta.meta_value = $catId AND
	{PRFX}objectmeta.obj_id IN ($toDeleteIn) 
EOT;
		$result = $ntsdb->runQuery( $sql );
		}

/* see what to add */
	$toAdd = array_diff( $newServices, $currentServices );
	if( $toAdd ){
		reset( $toAdd );
		foreach( $toAdd as $sid ){
			$sql =<<<EOT
INSERT INTO 
	{PRFX}objectmeta
	(
	obj_class,
	obj_id,
	meta_name,
	meta_value
	)
VALUES
	(
	"service",
	$sid,
	"_service_cat",
	$catId
	)
EOT;
			$result = $ntsdb->runQuery( $sql );
			}
		}

	ntsView::addAnnounce( M('Update') . ': ' . M('OK'), 'ok' );

/* continue to the list with anouncement */
	$forwardTo = ntsLink::makeLink( '-current-' );
	ntsView::redirect( $forwardTo );
	exit;
	}
else {
/* form not valid, continue to create form */
	}
?>