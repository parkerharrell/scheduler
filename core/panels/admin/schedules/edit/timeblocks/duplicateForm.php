<?php
$dayFrom = $this->getValue('day_from');

$ntsConf =& ntsConf::getInstance();
$weekStartsOn = $ntsConf->get('weekStartsOn');
$text_Weekdays = array( M('Sunday'), M('Monday'), M('Tuesday'), M('Wednesday'), M('Thursday'), M('Friday'), M('Saturday') );

$dayOptions = array();
$dayOptions[] = array( -1, '- ' . M('All Days') . ' -' );
for( $i = 0; $i <= 6; $i++ ){
	$dayIndex = $weekStartsOn + $i;
	$dayIndex = $dayIndex % 7;
	if( $dayIndex != $dayFrom )
		$dayOptions[] = array( $dayIndex, $text_Weekdays[$dayIndex] );
	}
?>
<?php
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'day_to',
		'options'	=> $dayOptions,
		)
	);
?>

<?php echo $this->makePostParams('-current-', 'copy', array('day_from' => $dayFrom) ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('OK'); ?>">