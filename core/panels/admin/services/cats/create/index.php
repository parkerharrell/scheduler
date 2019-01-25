<h2><?php echo M('Category'); ?>: <?php echo M('Create'); ?></h2>
<?php
$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form' );
$form->display();
?>