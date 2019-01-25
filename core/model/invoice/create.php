<?php
$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();

/* init some params */
$createdAt = $object->getProp( 'created_at' );
if( ! $createdAt )
	$object->setProp( 'created_at', time() );

$object->setProp( 'currency', $conf->get('currency') );

/* generate refno */
include_once( NTS_BASE_DIR . '/lib/crypt/ntsRandomGenerator.php' );

$gen = new ntsRandomGenerator;

$refNoParts = array();

$gen->useLetters( false );
$gen->useCaps( true );
$gen->useDigits( false );
$refNoParts[] = $gen->generate(3);

$gen->useLetters( false );
$gen->useCaps( false );
$gen->useDigits( true );
$refNoParts[] = $gen->generate(3);

$refNo = join( '-', $refNoParts );
$object->setProp( 'refno', $refNo );
?>