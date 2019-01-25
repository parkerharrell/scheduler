<?php
$allCount = 0;
switch( $inputAction ){
	case 'display':
		$startTs = 0;
		$endTs = 24 * 60 * 60;
		$allCount = ( ( $endTs - $startTs ) / NTS_TIME_UNIT ) + 1;

		if( ! $conf['value'] )
			$conf['value'] = array();
		if( ! is_array($conf['value']) )
			$conf['value'] = array( $conf['value'] );

		$timeOptions = array();
		reset( $conf['value'] );
		$t = new ntsTime();
		foreach( $conf['value'] as $ts ){
			$t->setDateTime( 2011, 1, 14, 0, 0, 0 );
			$t->modify( '+' . $ts . ' seconds' );
			$timeOptions[] = array($ts, $t->formatTime() );
			}

		$conf['options'] = $timeOptions;
		break;
	}

/* handle action by default control */
$allItemsCount = $allCount;

$newItemsUrl = ntsLink::makeLink('admin/ajax/fixed-selectable-times');

$ntsDynaList_AllowEmpty = true;
$ntsDynaList_SortOptions = true;
require( NTS_BASE_DIR . '/lib/form/inputs/dynamicList.php' );
?>
<?php 
switch( $inputAction ){
	case 'display':
		$myMin = $conf['params']['min'];
		$myMax = $conf['params']['max'];
		$input .=<<<EOT
<script language="JavaScript">
dynaList${thisId}.setMin( $myMin );
dynaList${thisId}.setMax( $myMax );
dynaList${thisId}.walkOptions();
</script>

EOT;
	break;
	}
?>