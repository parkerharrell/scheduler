<?php
$class = 'appointment';

$otherDetails = array(
	'service_id'	=> $this->getValue('service_id'),
	);
$om =& objectMapper::getInstance();
$fields = $om->getFields( $class, 'internal', true, $otherDetails );
reset( $fields );
?>
<?php if( $fields ) : ?>
<table>
<?php foreach( $fields as $f ) : ?>
<?php $c = $om->getControl( $class, $f[0], false ); ?>
<tr>
	<th><?php echo $c[0]; ?></th>
	<td>
	<?php
	echo $this->makeInput (
		$c[1],
		$c[2],
		$c[3]
		);
	?>
	</td>
</tr>
<?php endforeach; ?>

<tr>
<th>&nbsp;</th>
<td>	
<?php if( ! $this->readonly ) : ?>
	<?php echo $this->makePostParams('-current-', 'update' ); ?>
	<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
<?php endif; ?>
</td>
</tr>
</table>
<?php endif; ?>
