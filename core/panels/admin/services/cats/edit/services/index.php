<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();

$catId = $req->getParam( '_id' );
$object = new ntsObject( 'service_cat' );
$object->setId( $catId );

/* get services */
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
$serviceOptions = array();
$result = $ntsdb->runQuery( $sql );
while( $e = $result->fetch() ){
	$serviceOptions[] = $e['id'];
	}
$formInfo = array(
	'services'	=> $serviceOptions,
	);

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formInfo );
$form->display();
?>