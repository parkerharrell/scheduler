<?php
global $NTS_VIEW;
$showCats = $NTS_VIEW['showCats'];
$cat2service = $NTS_VIEW['cat2service'];

$conf =& ntsConf::getInstance();
$showSessionDuration = $conf->get('showSessionDuration');
$servicesOptions = array();

foreach( $showCats as $e ){
	$servicesOptions[] = array( $e[1] );
	$myEntries = $cat2service[$e[0]];
	foreach( $myEntries as $s ){
		$linkView = ntsView::objectTitle($s);
		if( strlen($s->getProp('price')) )
			$linkView .= ' - ' . ntsCurrency::formatServicePrice($s->getProp('price'));
		$servicesOptions[] = array( $s->getId(), $linkView );
		}
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