<!-- APPOINTMENT REQUEST FLOW -->
<?php require( dirname(__FILE__) . '/../common/flow.php' ); ?>
<?php
$conf =& ntsConf::getInstance();
$showSessionDuration = $conf->get('showSessionDuration');
$selectStyle = $conf->get('selectStyle');
$showPacksAbove = $conf->get('showPacksAbove'); 

$ntsdb =& dbWrapper::getInstance();
$entries = $NTS_VIEW['entries'];
$packs = $NTS_VIEW['packs'];
?>
<?php
/* check categories */
$cat2service = array();
$allCats = array();
reset( $entries );
foreach( $entries as $service ){
	$thisCats = $service->getProp( '_service_cat' );
	if( ! $thisCats )
		$thisCats = array( 0 );

	reset( $thisCats );
	foreach( $thisCats as $catId ){
		if( ! isset($cat2service[$catId]) )
			$cat2service[$catId] = array();
		$cat2service[$catId][] = $service;
		}
	$allCats = array_merge( $allCats, $thisCats );
	}
$allCats = array_unique( $allCats );

if( count($allCats) < 2 ){
	$showCats = false;
	}
else {
	$showCats = true;
	}
	
if( $showCats ){
	$idsIn = join( ',', $allCats );
	$sql =<<<EOT
SELECT
	id, title, description
FROM
	{PRFX}service_cats
WHERE
	id IN ($idsIn)
ORDER BY
	show_order ASC
EOT;

	$showCats = array();
	$result = $ntsdb->runQuery( $sql );
	while( $c = $result->fetch() ){
		$showCats[] = array( $c['id'], $c['title'], $c['description'] );
		}
	if( in_array(0, $allCats) )
		$showCats[] = array( 0, M('Uncategorized'), '' );
	}
$NTS_VIEW['showCats'] = $showCats;
$NTS_VIEW['cat2service'] = $cat2service;
?>

<div id="nts-selector">

<?php if( $packs && $showPacksAbove ) : ?>
	<h2><?php echo M('Appointment Packs'); ?></h2>
	<?php require( dirname(__FILE__) . '/index-packs.php' ); ?>
<?php endif; ?>

<?php if( $entries ) : ?>
	<h2><?php echo M('Services'); ?></h2>
	<?php if( $showCats ) : ?>
		<?php require( dirname(__FILE__) . '/index-categories.php' ); ?>
	<?php else : ?>
		<?php require( dirname(__FILE__) . '/index-services.php' ); ?>
	<?php endif; ?>
<?php endif; ?>

<?php if( $packs && ( ! $showPacksAbove ) ) : ?>
	<h2><?php echo M('Appointment Packs'); ?></h2>
	<?php require( dirname(__FILE__) . '/index-packs.php' ); ?>
<?php endif; ?>

</div>
