<?php
global $NTS_VIEW;
$conf =& ntsConf::getInstance();
$showSessionDuration = $conf->get('showSessionDuration');

$entries = $NTS_VIEW['entries'];

$servicesOptions = array();
foreach( $entries as $s ){
	$linkView = ntsView::objectTitle($s);
	if( strlen($s->getProp('price')) )
		$linkView .= ' - ' . ntsCurrency::formatServicePrice($s->getProp('price'));
	$servicesOptions[] = array( $s->getId(), $linkView );
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
		'options'	=> $servicesOptions,
		'attr'		=> array(
			),
		)
	);
?>
<?php echo $this->makePostParams('-current-', 'select' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Continue'); ?>">