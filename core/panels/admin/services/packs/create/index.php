<h2><?php echo M('Appointment Pack'); ?>: <?php echo M('Create'); ?></h2>
<?php
$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form' );
$form->display();
?>