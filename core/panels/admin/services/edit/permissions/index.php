<?php
$object = $NTS_VIEW['object'];

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );
?>
<?php
$form->display();
?>
<p>
If you choose to hide this service from customers, you can still let them select it alone by this URL:
<br>
<a target="_blank" href="<?php echo ntsLink::makeLinkFull( NTS_FRONTEND_WEBPAGE, 'customer', '', array('service' => $object->getId()) ); ?>"><?php echo ntsLink::makeLinkFull( NTS_FRONTEND_WEBPAGE, 'customer', '', array('service' => $object->getId()) ); ?></a>