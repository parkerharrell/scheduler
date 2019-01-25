<H2><?php echo M('Time Slot'); ?>: <?php echo M('Add'); ?></H2>

<?php
$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form' );
$form->display();
?>