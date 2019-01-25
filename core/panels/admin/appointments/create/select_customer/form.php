<i><?php echo M('You can use the * wildcard to match any set of characters'); ?></i>
<?php
$om =& objectMapper::getInstance();
$fields = $om->getFields( 'customer', 'internal', true );
reset( $fields );

?>
<table>
<?php foreach( $fields as $f ) : ?>
<?php $c = $om->getControl( 'customer', $f[0], true ); ?>
<tr>
	<th><?php echo $c[0]; ?></th>
	<td>
	<?php
	echo $this->makeInput (
		$c[1],
		$c[2]
		);
	?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'start' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Search'); ?>">
</DIV>