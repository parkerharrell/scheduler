<?php 
require( dirname(__FILE__) . '/_header.php' );
?>

<p>
<table>
<?php $pagerParams = ( isset($NTS_VIEW['searchParams']) ) ?  $NTS_VIEW['searchParams'] : array(); ?>

<tr>
	<td style="text-align: left;">
[<?php echo $NTS_VIEW['showFrom']; ?> - <?php echo $NTS_VIEW['showTo']; ?> of <?php echo $NTS_VIEW['totalCount']; ?>]
	</td>
</tr>
</table>

<?php
$displayActions = false;
$listFile = ntsLib::fileInCoreDirs( 'panels/admin/customers/browse/_list.php' );
require( $listFile );
?>