<!-- APPOINTMENT REQUEST FLOW -->
<?php require( dirname(__FILE__) . '/../../common/flow.php' ); ?>

<h3><?php echo M('Customer'); ?></h3>
<?php
include_once( NTS_BASE_DIR . '/lib/view/ntsPager.php' );
$app =& ntsApplication::getInstance();
$displayUsername = NTS_EMAIL_AS_USERNAME ? 0 : 1;
$displayEmail = true;

$pager = new ntsPager( $NTS_VIEW['totalCount'], $NTS_VIEW['showPerPage'], 10 );
$pager->setPage( $NTS_VIEW['currentPage'] );

$pages = $pager->getPages();
reset( $pages );

$fields = array(
	'email'			=> 'Email',
	'username'		=> 'Username',
	'first_name'	=> 'First Name',
	'last_name'		=> 'Last Name',
	);
	
/* custom fields */
$om =& objectMapper::getInstance();
$allFields = $om->getFields( 'customer', 'internal', true );
reset( $allFields );
foreach( $allFields as $f ){
	$fields[ $f[0] ] = $f[1];
	}
/* status options */
$fields[ 'nts_user_status' ] = M('User Status');

$values = array();
$values[ '-any-' ] = M('- Any -');
$values[ 'email_not_confirmed' ] = M('Email Not Confirmed');
$values[ 'not_approved' ] = M('Not Approved');
$values[ 'suspended' ] = M('Suspended');
?>

<?php if( $NTS_VIEW['searchParams'] ) : ?>
	<b><?php echo M('Search'); ?></b>: 
	<?php foreach( $NTS_VIEW['searchParams'] as $k => $v ) : ?>
		<?php echo isset($fields[$k]) ? $fields[$k] : $k; ?>: <b><?php echo isset($values[$v]) ? $values[$v] : $v; ?></b> 
	<?php endforeach; ?>
<?php else : ?>
<?php endif; ?>

<?php if( (! $NTS_VIEW['entries'] ) ) : ?>
	<p>
	<?php echo M('No users found matching your search'); ?>
	<a href="<?php echo ntsLink::makeLink('-current-/..' ); ?>"><?php echo M('Revise Search?'); ?></a>
	
	<?php return; ?>
<?php endif; ?>

<p>
<table>
<tr>
	<td style="text-align: right;">
<?php echo M('Showing {SHOW_FROM} - {SHOW_TO} of {TOTAL_COUNT}', array('SHOW_FROM' => $NTS_VIEW['showFrom'], 'SHOW_TO' => $NTS_VIEW['showTo'], 'TOTAL_COUNT' => $NTS_VIEW['totalCount'])); ?>
&nbsp;&nbsp;<?php echo M('Pages'); ?>: 
<?php $pagerParams = ( isset($NTS_VIEW['searchParams']) ) ?  $NTS_VIEW['searchParams'] : array(); ?>
<?php foreach( $pages as $pi ): ?>
	<?php if( $NTS_VIEW['currentPage'] != $pi['number'] ) : ?>
		<?php $pagerParams['p'] = $pi['number']; ?>
		<a href="<?php echo ntsLink::makeLink('-current-', '', $pagerParams ); ?>"><?php echo $pi['title']; ?></a>
	<?php else : ?>
		<b><?php echo $pi['title']; ?></b>
	<?php endif; ?>
<?php endforeach; ?>
	</td>
</tr>
</table>

<table class="nts-listing" id="nts-customer-listing">
<tr class="listing-header">
	<?php if( $displayUsername ) : ?>
		<th><?php echo M('Username'); ?></th>
	<?php endif; ?>
	<th><?php echo M('Full Name'); ?></th>
	<?php if( $displayEmail ) : ?>
		<th><?php echo M('Email'); ?></th>
	<?php endif; ?>
	<th><?php echo M('Status'); ?></th>
</tr>

<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['entries'] as $e ) : ?>
<?php
$obj = new ntsUser();
$obj->setByArray( $e );
$restrictions = $obj->getProp('_restriction');
?>

<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<?php if( $displayUsername ) : ?>
		<td>
			<b><a href="<?php echo ntsLink::makeLink('-current-/..', 'select', array('id' => $obj->getId()) ); ?>"><?php echo $obj->getProp('username'); ?></a></b><br>
		</td>
	<?php endif; ?>
	<td>
		<a href="<?php echo ntsLink::makeLink('-current-/..', 'select', array('id' => $obj->getId()) ); ?>"><?php echo $obj->getProp('first_name'); ?> <?php echo $obj->getProp('last_name'); ?></a>
	</td>
	<?php if( $displayEmail ) : ?>
		<td><?php echo $obj->getProp('email'); ?></td>
	<?php endif; ?>
	<td>
	<?php
		$statusOk = true;
		if( $restrictions ){
			$statusOk = false;
			if( in_array('email_not_confirmed', $restrictions) )
				$status = M('Email Not Confirmed');
			elseif( in_array('not_approved', $restrictions) )
				$status = M('Not Approved');
			elseif( in_array('suspended', $restrictions) )
				$status = M('Suspended');
			else
				$status = M('N/A');
			}
		else {
			$status = M('Active');
			}
	?>
	<?php if( $statusOk ) : ?>
		<span class="ok">
	<?php else: ?>
		<span class="alert">
	<?php endif; ?>
	<?php echo $status; ?></span>
	</td>
</tr>
<?php $count++; ?>
<?php endforeach; ?>

</table>
</form>