<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

$id = $req->getParam( '_id' );
$form = new ntsObject('form');
$form->setId( $id );

/* super count */
$sql =<<<EOT
SELECT COUNT(*) AS count FROM {PRFX}form_controls WHERE form_id = $id
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	$e = $result->fetch();
	$totalCount = $e['count'];
	}

if( ! $totalCount ){
/* continue create service */
	$forwardTo = ntsLink::makeLink( '-current-/../create' );
	ntsView::redirect( $forwardTo );
	exit;
	}


$NTS_VIEW['fields'] = array();
/* fields info */
$sql =<<<EOT
SELECT
	*
FROM
	{PRFX}form_controls
WHERE
	form_id = $id
ORDER BY
	show_order ASC
EOT;
$result = $ntsdb->runQuery( $sql );
while( $c = $result->fetch() ){
	$NTS_VIEW['fields'][] = $c;
	}
?>
<?php
$vm =& ntsValidatorManager::getInstance();
$om =& objectMapper::getInstance();

$fieldTypeNames = array(
	'text'		=>	M('Text'),
	'checkbox'	=>	M('Yes/No'),
	'textarea'	=>	M('Textarea'),
	'select'	=>	M('Select'),
	);

$accessTypes = array(
	'hidden'	=>	M('Hidden'),
	'read'		=>	M('View Only'),
	'write'		=>	M('View and Update'),
	);

list( $coreProps, $metaProps ) = $om->getPropsForClass( 'user' );
$biltInFields = array_keys( $coreProps );
?>

<h2><?php echo M('Form Fields'); ?></h2>

<p>
<?php if( ! count($NTS_VIEW['fields'])) : ?>
	<?php echo M('None'); ?>
<?php else : ?>
<table>
<tr class="listing-header">
	<th><?php echo M('Name'); ?></th>
	<th><?php echo M('Type'); ?></th>
	<th><?php echo M('External User Access'); ?></th>
	<th><?php echo M('Validation'); ?></th>
	<th><?php echo M('Actions'); ?></th>
</tr>

<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['fields'] as $e ) : ?>
<tr class="<?php echo (($count++) % 2) ? 'even' : 'odd'; ?>">
	<td>
		<a href="<?php echo ntsLink::makeLink('-current-/edit', '', array('id' => $e['id']) ); ?>"><?php echo $e['title']; ?></a>
	</td>
	<td>
		<?php echo ( isset($fieldTypeNames[$e['type']]) ) ? $fieldTypeNames[$e['type']] : $e['type']; ?>
	</td>

	<td>
		<?php echo ( isset($accessTypes[$e['ext_access']]) ) ? $accessTypes[$e['ext_access']] : $e['ext_access']; ?>
	</td>

	<td>
		<?php if( $e['validators'] && ( $validators = unserialize($e['validators']) )) : ?>
			<?php if( count($validators) == 1 ) : ?>
				<?php
				$vi = $vm->getValidatorInfo( $validators[0]['code'] );
				echo $vi[1];
				?>
			<?php else : ?>
				<?php echo count($validators); ?> validations
			<?php endif; ?>
		<?php else : ?>
			<?php echo M('None'); ?>
		<?php endif; ?>
	</td>

	<td>
		<a class="ok" href="<?php echo ntsLink::makeLink('-current-', 'control_up', array('control' => $e['id']) ); ?>"><?php echo M('Up'); ?></a>
		<a class="ok" href="<?php echo ntsLink::makeLink('-current-', 'control_down', array('control' => $e['id']) ); ?>"><?php echo M('Down'); ?></a>
	<?php if( ! in_array($e['name'], $biltInFields) ) : ?>
		<a class="alert" href="<?php echo ntsLink::makeLink('-current-/delete', '', array('id' => $e['id']) ); ?>"><?php echo M('Delete'); ?></a>
	<?php endif; ?>
	</td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<p>
<a class="ok" href="<?php echo ntsLink::makeLink('-current-/create'); ?>"><?php echo M('Form Field'); ?>: <?php echo M('Create'); ?></a>
