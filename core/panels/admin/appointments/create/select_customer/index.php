<!-- APPOINTMENT REQUEST FLOW -->
<?php require( dirname(__FILE__) . '/../common/flow.php' ); ?>

<div style="width: 100%; overflow: auto; margin: 0; padding: 0;">
	<div style="width: 45%; float: left; padding: 0 2em 0 0; border-right: #bbbbbb 1px solid;">
	<p>
	<h3><?php echo M('Customer'); ?>: <?php echo M('Find'); ?></h3>
	<?php
	$ff =& ntsFormFactory::getInstance();
	$form =& $ff->makeForm( dirname(__FILE__) . '/form' );
	$form->display();
	?>
	</div>

	<div style="width: 45%; float: left; padding: 0 0 0 2em;">
<?php if( ! $NTS_CURRENT_USER->isPanelDisabled('admin/customers/create') ) : ?>
	<p>
	<h3><?php echo M('Customer'); ?>: <?php echo M('Create'); ?></h3>
	<?php
	$createForm =& $ff->makeForm( dirname(__FILE__) . '/createForm' );
	$createForm->display();
	?>
<?php endif; ?>
	</div>
</div>
