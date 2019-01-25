<?php
switch( $inputAction ){
	case 'display':
		$myValue = array();
		if( preg_match('/discount/', $conf['value'])){
			$myValue = explode( ':', $conf['value'] );
			}
		elseif( preg_match('/price/', $conf['value'])){
			$myValue = explode( ':', $conf['value'] );
			}
		else{
			$myValue = array( $conf['value'], '' );
			}
?>
<SCRIPT LANGUAGE="Javascript">
function toggleDiscount( what ){
	if( what == 'price' ){
		document.forms["<?php echo $this->getName(); ?>"].<?php echo $conf['id']; ?>_is_discount.checked = false;
		document.forms["<?php echo $this->getName(); ?>"].<?php echo $conf['id']; ?>_is_price.checked = true;
		document.forms["<?php echo $this->getName(); ?>"].<?php echo $conf['id']; ?>_is_onefree.checked = false;
		ntsElementShow( "controlPrice" );
		ntsElementHide( "controlDiscount" );
		}
	else if( what == 'discount' ) {
		document.forms["<?php echo $this->getName(); ?>"].<?php echo $conf['id']; ?>_is_discount.checked = true;
		document.forms["<?php echo $this->getName(); ?>"].<?php echo $conf['id']; ?>_is_price.checked = false;
		document.forms["<?php echo $this->getName(); ?>"].<?php echo $conf['id']; ?>_is_onefree.checked = false;
		ntsElementHide( "controlPrice" );
		ntsElementShow( "controlDiscount" );
		}
	else {
		document.forms["<?php echo $this->getName(); ?>"].<?php echo $conf['id']; ?>_is_discount.checked = false;
		document.forms["<?php echo $this->getName(); ?>"].<?php echo $conf['id']; ?>_is_price.checked = false;
		document.forms["<?php echo $this->getName(); ?>"].<?php echo $conf['id']; ?>_is_onefree.checked = true;
		ntsElementHide( "controlPrice" );
		ntsElementHide( "controlDiscount" );
		}
	}
</SCRIPT>

<table>
<TR>
	<TH>
	<?php
	$isDiscount = ( $myValue[0] == 'discount' ) ? 1 : 0;
	echo $this->makeInput(
		'checkbox',
		array(
			'id'		=> $conf['id'] . '_is_discount',
			'default'	=> $isDiscount,
			'attr'		=> array(
				'onClick'	=> 'toggleDiscount( \'discount\' );',
				),
			)
		);
	?>
	<?php echo M('Discount'); ?>
	</TH>
	<td>
<?php
$discountOptions = array(
	array( '0', '0%' ),
	array( '5', '5%' ),
	array( '10', '10%' ),
	array( '15', '15%' ),
	array( '20', '20%' ),
	array( '25', '25%' ),
	array( '30', '30%' ),
	array( '35', '35%' ),
	array( '40', '40%' ),
	array( '45', '45%' ),
	array( '50', '50%' ),
	array( '55', '55%' ),
	array( '60', '60%' ),
	array( '65', '65%' ),
	array( '70', '70%' ),
	array( '75', '75%' ),
	array( '80', '80%' ),
	array( '85', '85%' ),
	array( '90', '90%' ),
	array( '95', '95%' ),
	);
?>
	<div id="controlDiscount">
	<?php
	echo $this->makeInput(
		'select',
		array(
			'id'		=> $conf['id'] . '_discount',
			'default'	=> ($myValue[0] == 'discount') ? $myValue[1] : $discountOptions[0][0],
			'options'	=> $discountOptions,
			)
		);
	?>
	</div>
	</td>
</TR>

<TR>
	<TH>
	<?php
	$isPrice = ( $myValue[0] == 'price' ) ? 1 : 0;
	echo $this->makeInput(
		'checkbox',
		array(
			'id'		=> $conf['id'] . '_is_price',
			'default'	=> $isPrice,
			'attr'		=> array(
				'onClick'	=> 'toggleDiscount( \'price\' );',
				),
			)
		);
	?>
	<?php echo M('Total Price'); ?>
	</TH>
	<td>
	<div id="controlPrice">
	<?php
	$appConf =& ntsConf::getInstance();
	$formatConf = $appConf->get( 'priceFormat' );
	list( $beforeSign, $decPoint, $thousandSep, $afterSign ) = $formatConf;
	?>
	<?php echo $beforeSign; ?>
	<?php
	echo $this->makeInput(
		'text',
		array(
			'id'		=> $conf['id'] . '_price',
			'default'	=> ($myValue[0] == 'price') ? $myValue[1] : 100,
			'attr'	=> array(
				'size' => 3,
				),
			)
		);
	?>
	<?php echo $afterSign; ?>
	</div>
	</td>
</TR>

<TR>
	<TH colspan="2" style="width: 100%;">
	<?php
	$isOneFree = ( $myValue[0] == 'onefree' ) ? 1 : 0;
	echo $this->makeInput(
		'checkbox',
		array(
			'id'		=> $conf['id'] . '_is_onefree',
			'default'	=> $isOneFree,
			'attr'		=> array(
				'onClick'	=> 'toggleDiscount( \'onefree\' );',
				),
			)
		);
	?>
	<?php echo M('One Appointment Free'); ?>
	</TH>
</tr>
</table>

<SCRIPT LANGUAGE="Javascript">
toggleDiscount( '<?php echo $myValue[0]; ?>' );
</SCRIPT>
<?php
		break;

	case 'submit':
		$isPrice = $req->getParam( $handle . '_is_price' );
		$isDiscount = $req->getParam( $handle . '_is_discount' );
		$isOneFree = $req->getParam( $handle . '_is_onefree' );
		if( $isPrice ){
			$qty = $req->getParam( $handle . '_price' );
			$input = 'price:' . $qty;
			}
		elseif( $isDiscount ){
			$qty = $req->getParam( $handle . '_discount' );
			$input = 'discount:' . $qty;
			}
		else {
			$input = 'onefree';
			}
		break;

	case 'check_submit':
		$isPrice = $req->getParam( $handle . '_is_price' );
		$isDiscount = $req->getParam( $handle . '_is_discount' );
		$isOneFree = $req->getParam( $handle . '_is_onefree' );

		if( $isPrice ){
			$qty = $req->getParam( $handle . '_price' );
			$input = $qty ? true : false;
			}
		elseif( $isDiscount ){
			$qty = $req->getParam( $handle . '_discount' );
			$input = ( $qty || $qty === 0 || $qty === '0' )? true : false;
			}
		else {
			$input = true;
			}
		break;
	}
?>