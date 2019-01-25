<?php
global $req;
$conf =& ntsConf::getInstance();
$maxAppsInPack = $conf->get('maxAppsInPack');
$ff =& ntsFormFactory::getInstance();

$objId = $req->getParam( '_id' );
$object = new ntsObject( 'pack' );
$object->setId( $objId );

$formInfo = array();
$sessionsString = $object->getProp( 'services' );
$sessionsArray = explode( '|', $sessionsString );
for( $i = 1; $i <= $maxAppsInPack; $i++ ){
	$thisArray = array();
	if( isset($sessionsArray[$i-1]) ){
		$thisArray = explode( '-', $sessionsArray[$i-1] );
		}
	$formInfo['services-' . $i] = $thisArray;
	}
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formInfo );
?>
<?php
$form->display();
?>