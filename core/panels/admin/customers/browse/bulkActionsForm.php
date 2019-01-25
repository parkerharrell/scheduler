<?php
$bulkActions = array(
	array( '', M('Bulk Actions') . ':' ),
	array( 'delete', M('Delete') ),
	array( 'suspend', M('Suspend') ),
	array( 'activate', M('Activate') ),
	);

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
?>
<?php echo $this->makePostParams('admin/customers/bulk_action', '' ); ?>
<input type="submit" VALUE="<?php echo M('Go'); ?>">