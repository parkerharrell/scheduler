<?php
global $NTS_VIEW;
$packs = $NTS_VIEW['packs'];

$sessionsOptions = array();
foreach( $packs as $p ){
	$packTitle = $p->getProp('title');
	$sessionsOptions[] = array( $p->getId(), $packTitle );
	}
?>
<p>
<?php
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'id',
		'options'	=> $sessionsOptions,
		'attr'		=> array(
			),
		)
	);
?>
<?php echo $this->makePostParams('-current-', 'select_pack' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Continue'); ?>">