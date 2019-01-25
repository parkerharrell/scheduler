<table>
<tr>
	<th><?php echo M('Title'); ?> *</th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'title',
			'attr'		=> array(
				'size'	=> 42,
				),
			'default'	=> '',
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required field'),
				),
			array(
				'code'		=> 'checkUniqueProperty.php', 
				'error'		=> M('Already in use'),
				'params'	=> array(
					'prop'	=> 'title',
					'class'	=> 'location',
					),
				),
			)
		);
	?>
	</td>
</tr>

<tr>
	<th colspan="2"><?php echo M('Description'); ?></th>
</tr>
<tr>
	<td colspan="2">
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'description',
			'attr'		=> array(
				'cols'	=> 42,
				'rows'	=> 6,
				),
			'default'	=> '',
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>

<?php
// additional fields if any
$className = 'location';
$om =& objectMapper::getInstance();
$fields = $om->getFields( $className, 'internal', true );
reset( $fields );
?>

<?php foreach( $fields as $f ) : ?>
<?php $c = $om->getControl( $className, $f[0], false ); ?>
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

</table>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'create' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Create'); ?>">
</DIV>