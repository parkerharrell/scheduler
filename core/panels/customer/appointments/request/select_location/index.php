<?php
$conf =& ntsConf::getInstance();
$selectStyle = $conf->get('selectStyle');

$entries = $NTS_VIEW['entries'];
?>
<!-- APPOINTMENT REQUEST FLOW -->
<?php require( dirname(__FILE__) . '/../common/flow.php' ); ?>

<div id="nts-selector">
<h2><?php echo M('Locations'); ?></h2>

<?php if( $selectStyle == 'dropdown' ): ?>
	<?php
	$ff =& ntsFormFactory::getInstance();
	$form =& $ff->makeForm( dirname(__FILE__) . '/form' );
	$form->display();
	?>
<?php else : ?>
	<ul>

<?php if( $NTS_VIEW['selectionMode'] == 'manualplus' ) : ?>
	<li>
		<h3> - <a href="<?php echo ntsLink::makeLink('-current-', 'select', array('id' => 'auto') ); ?>"><?php echo M("Don't have a particular preference"); ?></a> - </h3>
	</li>
	<li><?php echo M('Or select one below'); ?></li>
<?php endif; ?>

	<?php foreach( $entries as $e ) : ?>
<?php
		$earliestTs = isset($NTS_VIEW['availability']['locations'][$e->getId()]) ? $NTS_VIEW['availability']['locations'][$e->getId()] : 0;
?>
	<li>
		<h3><a href="<?php echo ntsLink::makeLink('-current-', 'select', array('id' => $e->getId()) ); ?>"><?php echo ntsView::objectTitle($e); ?></a></h3>
<?php
		if( isset($NTS_VIEW['availability']) ){
?>
<?php	 	if( $earliestTs ) : ?>
<?php			$t->setTimestamp( $earliestTs ); ?>
<?php 			echo M('Nearest Availability'); ?>: <b><?php echo $t->formatDate(); ?> <?php echo $t->formatTime(); ?></b>
<?php 		else : ?>
<?php 			echo M('Not Available Now'); ?>
<?php 		endif; ?>
<?php 	} ?>
		<?php if( $e->getProp('description') ) : ?> 
			<p><?php echo $e->getProp('description'); ?>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

</div>