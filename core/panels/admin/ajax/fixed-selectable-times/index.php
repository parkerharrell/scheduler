<?php
if( $NTS_VIEW['entries'] ){
	$t = new ntsTime();
	$t->setDateTime( 2011, 1, 14, 0, 0, 0 );

	reset( $NTS_VIEW['entries'] );
	$return = '';
	$return .= '<option value="0"> - ' . M('Select') . ' - </option>';
	foreach( $NTS_VIEW['entries'] as $ts ){
		$t->setDateTime( 2011, 1, 14, 0, 0, 0 );
		$t->modify( '+' . $ts . ' seconds' );
		$valueString = $ts . '::' . $t->formatTime();
		$return .= '<option value="' . $valueString . '">' . $t->formatTime() . '</option>';
		}
	}
else {
	$return = '';
	$return .= '<option value="0"> - ' . M('No more available') . ' - </option>';
	}
echo $return;
?>