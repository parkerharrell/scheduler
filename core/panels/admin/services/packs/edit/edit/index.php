<?php
$ff =& ntsFormFactory::getInstance();
$formFile = dirname(__FILE__) . '/form';
$objInfo = array(
	'id'	=> $NTS_VIEW['id'],
	);
$form =& $ff->makeForm( $formFile, $objInfo );
?>
<?php
	$form->display();
?>