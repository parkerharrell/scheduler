<?php global $NTS_CURRENT_USER; ?>
<H2><?php echo M('Register'); ?></H2>

<?php
$formParams = array(
	'_timezone'	=> $NTS_CURRENT_USER->getTimezone() 
	);

$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form', $formParams );
$form->display();
?>