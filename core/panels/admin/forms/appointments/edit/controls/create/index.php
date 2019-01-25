<H2><?php echo M('Form Field'); ?>: <?php echo M('Create'); ?></H2>
<p>
<?php
$id = $req->getParam( '_id' );

$ff =& ntsFormFactory::getInstance();
$formParams = array(
	'form_id'	=> $id,
	);

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formParams );
$form->display();
?>