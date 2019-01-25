<?php
if( $NTS_VIEW['entries'] ){
	reset( $NTS_VIEW['entries'] );
	$return = '';
	$return .= '<option value="0"> - ' . M('Select') . ' - </option>';
	foreach( $NTS_VIEW['entries'] as $l ){
		$valueString = $l->getId() . '::' . ntsView::objectTitle($l);
		$return .= '<option value="' . $valueString . '">' . ntsView::objectTitle($l) . '</option>';
		}
	}
else {
	$return = '<i>' . M('None') . '</i>';
	}
echo $return;
?>