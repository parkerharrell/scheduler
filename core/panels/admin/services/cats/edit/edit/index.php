<?php
$ff =& ntsFormFactory::getInstance();
$formFile = dirname(__FILE__) . '/form';
$catInfo = array(
	'id'	=> $NTS_VIEW['id'],
	);
$form =& $ff->makeForm( $formFile, $catInfo );
?>
<?php
	$form->display();
?>