<?php
$conf =& ntsConf::getInstance();
$maxAppsInPack = $conf->get('maxAppsInPack');
$ntsdb =& dbWrapper::getInstance();

$options = array();

/* services */
$sql =<<<EOT
SELECT
	id, title
FROM
	{PRFX}services
ORDER BY
	show_order ASC
EOT;
$result = $ntsdb->runQuery( $sql );
while( $e = $result->fetch() ){
	$options[] = array( $e['id'], $e['title'] );
	}
?>
<ol style="">
<?php for( $i = 1; $i <= $maxAppsInPack; $i++ ) : ?>
<?php
if( $i == 1 ){
	$validators = array(
		array(
			'code'		=> 'notEmpty.php', 
			'error'		=> M('Please choose at least one option'),
			),
		);
	}
else {
	$validators = array();
	}
?>

<li style="margin: 0 0 2em 0;">
<?php
echo $this->makeInput (
/* type */
	'checkboxSet',
/* attributes */
	array(
		'id'		=> 'services-' . $i,
		'options'	=> $options,
		'attr'		=> array(
//			'separator_before'	=> '<li style="margin: 1em 0; padding: 0;">',
			'separator_after'	=> '<br>',
			),
		),
/* validators */
	$validators
	);
?>
</li>
<?php endfor; ?>
</ol>

<p>
<?php echo $this->makePostParams('-current-', 'update' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
