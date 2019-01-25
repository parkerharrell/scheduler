<?php
$class = 'customer';
$title = M('Customer') . ': ' . M('New');
?>
<?php
$fparams = array(
	'class'	=> $class
	);

$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form', $fparams );
$form->display();
?>