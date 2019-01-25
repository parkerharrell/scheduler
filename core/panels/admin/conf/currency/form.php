<table>
<tr>
	<th><?php echo M('Currency'); ?></th>
<?php
$pgm =& ntsPaymentGatewaysManager::getInstance();
$allCurrOptions = $pgm->getAllCurrencies();
$allowedCurrencies = $pgm->getActiveCurrencies();

if( $allowedCurrencies ){
	$currOptions = array();
	reset( $allCurrOptions );
	foreach( $allCurrOptions as $co ){
		if( in_array($co[0], $allowedCurrencies) )
			$currOptions[] = $co;
		}
	}
else {
	$currOptions = $allCurrOptions;
	}
?>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'currency',
			'options'	=> $currOptions,
			)
		);
	?>
	</td>
</tr>
<tr>
	<th><?php echo M('Price Format'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'sign-before',
			'attr'		=> array(
				'size'	=> 4,
				'style'	=> 'text-align: right;'
				),
			)
		);
	?>
	<?php
	$formats = array(
		'.||,',
		'.|| ',
		',|| ',
		'.||',
		',||',
		',||.',
		);

	$demoPrice = 54321;
	reset( $formats );
	$formatOptions = array();
	foreach( $formats as $f ){
		list( $decPoint, $thousandSep ) = explode( '||', $f );
		$formatOptions[] = array( $f, number_format($demoPrice, 2, $decPoint, $thousandSep) );
		}

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'format',
			'options'	=> $formatOptions,
			)
		);
	?>	
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'sign-after',
			'attr'		=> array(
				'size'	=> 4,
				),
			)
		);
	?>
	<br>
	<a href="<?php echo ntsLink::makeLink('-current-', 'reset'); ?>"><?php echo M('Reset To Defaults'); ?></a> 
	</td>
</tr>
</TABLE>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update'); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Save'); ?>">
</DIV>