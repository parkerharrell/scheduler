<?php
$startsAtDate = $formValues['starts_at_date'];
$endsAtDate = $formValues['ends_at_date'];
$startsAtTime = $formValues['starts_at_time'];
$endsAtTime = $formValues['ends_at_time'];

$t = new ntsTime();
$startsAt = $t->timestampFromDbDate( $startsAtDate ) + $startsAtTime;
$endsAt = $t->timestampFromDbDate( $endsAtDate ) + $endsAtTime;

if( $endsAt <= $startsAt ){
	$validationFailed = 1;
	}
?>