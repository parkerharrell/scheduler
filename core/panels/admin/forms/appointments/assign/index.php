<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';

/* get services */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}services
ORDER BY
	title ASC
EOT;

$services = array();
$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$service = new ntsObject( 'service' );
		$service->setId( $e['id'] );
		$services[] = $service;
		}
	}

/* get forms */
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
	) AS count_fields
FROM
	{PRFX}forms
WHERE 
	class="appointment"
ORDER BY
	title ASC
EOT;

$formsOptions = array();
$result = $ntsdb->runQuery( $sql );
$formsOptions[] = array( 0, M('None') );
if( $result ){
	while( $e = $result->fetch() ){
		$formsOptions[] = array( $e['id'], $e['title'] );
		}
	}
?>
<table class="nts-listing">
<tr class="listing-header">
	<th><?php echo M('Service'); ?></th>
	<th><?php echo M('Custom Form'); ?></th>
</tr>

<?php $count = 0; ?>
<?php foreach( $services as $e ) : ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td>
		<b><?php echo $e->getProp('title'); ?></b>
	</td>
	<td>
	<?php
		$thisCustomForm = $e->getProp( '_form' );
		$si = array(
			'_form'		=> $thisCustomForm,
			'forms'		=> $formsOptions,
			'serviceId'	=> $e->getId(),
			);
		$form =& $ff->makeForm( $formFile, $si, $e->getId() );
		$form->display();
	?>
	</td>
</tr>
<?php $count++; ?>
<?php endforeach; ?>
</table>