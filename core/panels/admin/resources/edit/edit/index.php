<?php
$ntsdb =& dbWrapper::getInstance();
$id = $NTS_VIEW['id'];
?>
<?php
$ff =& ntsFormFactory::getInstance();
$formFile = dirname(__FILE__) . '/form';
$form =& $ff->makeForm( $formFile );
?>
<?php
	$form->display();
?>