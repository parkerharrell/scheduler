<H2><?php echo M('Form Field'); ?>: <?php echo M('Create'); ?></H2>
<p>
<?php
$ntsdb =& dbWrapper::getInstance();
/* form id */
$sql =<<<EOT
	SELECT
		*
	FROM
		{PRFX}forms
	WHERE
		class = 'customer'
	LIMIT 1
EOT;
$result = $ntsdb->runQuery( $sql );
$formInfo = $result->fetch();
$formId = $formInfo['id'];

$formParams = array(
	'form_id'	=> $formId,
	);

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formParams );
$form->display();
?>