<?php
include_once( NTS_BASE_DIR . '/lib/view/ntsPager.php' );
$pager = new ntsPager( $NTS_VIEW['totalCount'], $NTS_VIEW['showPerPage'], 10 );
$pager->setPage( $NTS_VIEW['currentPage'] );

$pages = $pager->getPages();
reset( $pages );

$skipList = false;
?>
<?php require( dirname(__FILE__) . '/_header.php' ); ?>

<?php if( (($NTS_VIEW['action'] == 'search') && $NTS_VIEW['searchParams']) || (count($NTS_VIEW['entries']) > 0) ) : ?>
<?php if( ! $skipList ) : ?>
<p>
<table class="nts-listing-top">
<?php 
$pagerParams = ( isset($NTS_VIEW['searchParams']) ) ?  $NTS_VIEW['searchParams'] : array();
$pagerParams[ 'sort' ] = $NTS_VIEW['sortBy'];
?>

<tr>
	<td style="text-align: left;">
	<?php
	$ff =& ntsFormFactory::getInstance();
	$form =& $ff->makeForm( dirname(__FILE__) . '/bulkActionsForm' );
	$form->display( array(), true );
	?>
	</td>

	<td style="text-align: center;">
[<?php echo $NTS_VIEW['showFrom']; ?> - <?php echo $NTS_VIEW['showTo']; ?> of <?php echo $NTS_VIEW['totalCount']; ?>]
&nbsp;&nbsp;<?php echo M('Pages'); ?>: 
<?php foreach( $pages as $pi ): ?>
	<?php if( $NTS_VIEW['currentPage'] != $pi['number'] ) : ?>
		<?php $pagerParams['p'] = $pi['number']; ?>
		<a href="<?php echo ntsLink::makeLink('-current-', $NTS_VIEW['action'], $pagerParams ); ?>"><?php echo $pi['title']; ?></a>
	<?php else : ?>
		<b><?php echo $pi['title']; ?></b>
	<?php endif; ?>
<?php endforeach; ?>
	</td>

	<td style="font-size: 70%;">
		<?php $overParams = $pagerParams; ?>	
		<?php echo M('Other Views & Export'); ?>:
		<?php 
			$overParams['display'] = 'print';
			$overParams[NTS_PARAM_ACTION] = $action;
		?>	
		<a target="_blank" href="<?php echo ntsLink::makeLink('-current-', '', $overParams ); ?>"><?php echo M('Print'); ?></a> 
		<?php $overParams['display'] = 'excel'; ?>	
		<a href="<?php echo ntsLink::makeLink('-current-', 'export', $overParams ); ?>">Excel</a>
	</td>

	<td>
		<a href="<?php echo ntsLink::makeLink('-current-/search' ); ?>"><?php echo M('Advanced Search'); ?></a>
	</td>
</tr>

<tr>
<td colspan="4">
<?php
$overParams = $pagerParams;
unset( $overParams['p'] );
?>
<ul>
<li><?php echo M('Sort By'); ?></li>
<?php foreach( $NTS_VIEW['sortByOptions'] as $so ) : ?>
<?php $overParams['sort'] = $so[0]; ?>
<li>
<?php if( $NTS_VIEW['sortBy'] == $so[0] ) : ?>
	<b><?php echo $so[1]; ?></b>
<?php else : ?>
	<a href="<?php echo ntsLink::makeLink('-current-', '', $overParams ); ?>"><?php echo $so[1]; ?></a>
<?php endif; ?>

</li>
<?php endforeach; ?>
</ul>

</td>
</tr>

</table>

<?php
$displayActions = true;
$listFile = ntsLib::fileInCoreDirs( 'panels/admin/customers/browse/_list.php' );
require( $listFile );
?>

</form>
<?php endif; ?>
<?php else : ?>
	<p><?php echo M('None'); ?>&nbsp;<a class="ok" href="<?php echo ntsLink::makeLink('-current-/../create'); ?>"><?php echo M('Create'); ?>?</a>
<?php endif; ?>
