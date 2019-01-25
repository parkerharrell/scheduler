<?php
$ntsdb =& dbWrapper::getInstance();

$catOptions = array();
/* all categories */
$sql =<<<EOT
SELECT
	id, title
FROM
	{PRFX}service_cats
ORDER BY
	show_order ASC
EOT;
$result = $ntsdb->runQuery( $sql );
while( $e = $result->fetch() ){
	$catOptions[] = array( $e['id'], $e['title'] );
	}
?>
<ul style="list-style-type: none; padding: 0 1em;">
<?php
echo $this->makeInput (
/* type */
	'checkboxSet',
/* attributes */
	array(
		'id'		=> 'cats',
		'options'	=> $catOptions,
		'attr'		=> array(
			'separator_before'	=> '<li style="margin: 1em 0; padding: 0;">',
			'separator_after'	=> '',
			),
		)
	);
?>

<?php echo $this->makePostParams('-current-', 'update' ); ?>
<li>
<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
</ul>
