<?php
class ntsCurrency {
	function formatPrice( $amount ){
		$conf =& ntsConf::getInstance();
		$formatConf = $conf->get( 'priceFormat' );
		list( $beforeSign, $decPoint, $thousandSep, $afterSign ) = $formatConf;

		$amount = number_format( $amount, 2, $decPoint, $thousandSep );
		$return = $beforeSign . $amount . $afterSign;
		return $return;
		}

	function formatServicePrice( $amount ){
		$return = '';
		if( strlen($amount) ){
			if( $amount > 0 ){
				$price = ntsCurrency::formatPrice( $amount );
				$return = $price;
				}
			else {
				$return = M('Free');
				}
			}
		
		return $return;
		}
	}
?>