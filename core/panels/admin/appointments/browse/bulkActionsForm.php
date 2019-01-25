<?php
global $NTS_VIEW;
### Customized by RAH 5/17/11 - Added No Show option to bulk actions list
$bulkActions = array(
	array( '', M('Bulk Actions') . ':' ),
	array( 'approve', M('Approve') ),
	array( 'reject', M('Reject') ),
	array( 'purge', M('Purge Cancelled') ),
   array( 'noshow', M('No Show') ),
	);
?>
<input type="checkbox" id="checker" name="checker" onClick="ntsMarkAllRows('nts-appointment-listing', this.checked);">
<?php
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'bulk-action',
		'options'	=> $bulkActions,
		),
/* validators */
	array(
		)
	);

$params = array(
	'nts-return' => 1,
	)
?>
<?php echo $this->makePostParams('-current-/bulk_action', '', $params ); ?>
<input type="submit" VALUE="<?php echo M('Go'); ?>">