<?php
$ntsdb =& dbWrapper::getInstance();

/* entries */
$sql =<<<EOT
SELECT
	*,
	(
	SELECT
		COUNT(*)
	FROM
		{PRFX}form_controls
	WHERE
		{PRFX}form_controls.form_id = {PRFX}forms.id
	) AS count_fields,
	(
	SELECT
		COUNT(*)
	FROM
		{PRFX}objectmeta
	WHERE
		meta_name = "_form" AND
		obj_class = "service" AND
		meta_value = {PRFX}forms.id
	) AS count_services
FROM
	{PRFX}forms
WHERE 
	class="appointment"
ORDER BY
	title ASC
EOT;

$result = $ntsdb->runQuery( $sql );
$NTS_VIEW['entries'] = array();
if( $result ){
	while( $e = $result->fetch() ){
		$NTS_VIEW['entries'][] = $e;
		}
	}
?>
<table class="nts-listing">
<tr class="listing-header">
	<th><?php echo M('Title'); ?></th>
	<th><?php echo M('Form Fields'); ?></th>
	<th><?php echo M('Services'); ?></th>
</tr>

<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['entries'] as $e ) : ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td>
		<a href="<?php echo ntsLink::makeLink('-current-/../edit', '', array('_id' => $e['id']) ); ?>"><?php echo $e['title']; ?></a>
	</td>
	<td>
		<?php echo $e['count_fields']; ?>
	</td>
	<td>
		<?php echo $e['count_services']; ?>
	</td>
</tr>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td colspan="3" class="nts-row-actions">
		<div>
		<a href="<?php echo ntsLink::makeLink('-current-/../edit/delete', '', array('_id' => $e['id']) ); ?>"><?php echo M('Delete'); ?></a>
		</div>
	</td>
</tr>
<?php $count++; ?>
<?php endforeach; ?>
</table>