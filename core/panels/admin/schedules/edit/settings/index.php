<?php
$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form' );

global $NTS_READ_ONLY;
$form->readonly = $NTS_READ_ONLY;

$form->display();
?>