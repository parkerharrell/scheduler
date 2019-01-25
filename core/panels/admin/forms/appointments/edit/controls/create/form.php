<?php
/* form params - used later for validation */
$this->setParams(
	array(
		'formId'	=> $this->getValue('form_id'),
		)
	);
$formId = $this->getValue('form_id');
?>
<TABLE>
<TR>
	<TH><?php echo M('System Name'); ?> *</TH>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'name',
			'attr'		=> array(
				'size'	=> 16,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required field'),
				),
			array(
				'code'		=> 'lowercaseLetterNumberUnderscore.php', 
				'error'		=> M('Only lowercase English letters, numbers, and underscores please!'),
				),
			array(
				'code'		=> 'checkUniqueProperty.php',
				'error'		=> M('Already in use'),
				'params'	=> array(
					'class'	=> 'form_control',
					'prop'	=> 'name',
					'also'	=> array(
						'form_id'	=> " = $formId"
						),
					),
				),
			)
		);
	?>
	<i><?php echo M('Only lowercase English letters, numbers, and underscores please!'); ?>
	</TD>
</TR>

<TR>
	<TH><?php echo M('Title'); ?> *</TH>
	<TD>
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
					'class'	=> 'form_control',
					'prop'	=> 'title',
//					'skipMe'	=> 1,
					'also'	=> array(
						'form_id'	=> "= $formId"
						),
					),
				),
			)
		);
	?>
	</TD>
</TR>

<TR>
	<TH><?php echo M('Help Text'); ?> (<?php echo M('Optional'); ?>)</TH>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'description',
			'attr'		=> array(
				'size'	=> 42,
				),
			)
		);
	?>
	</TD>
</TR>

<TR>
	<TH><?php echo M('External User Access'); ?></TH>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'accessType',
	/* attributes */
		array(
			'id'		=> 'ext_access',
			)
		);
	?>
	</TD>
</TR>
</TABLE>

<p>
<table>
<TR>
	<TH><?php echo M('Type'); ?></TH>
	<td>
<?php
echo $this->makeInput (
/* type */
	'fieldType',
/* attributes */
	array(
		'id'	=> 'type',
		)
	);
?>
</td>
</tr>
</table>

<p>
<?php
$fieldTypeOptionsFile = NTS_APP_DIR . '/lib/form/inputs/requireFieldTypeOptions.php';
require( $fieldTypeOptionsFile );
?>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'create', array('formId' => $formId) ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Create'); ?>">
</DIV>

<SCRIPT LANGUAGE="JavaScript">
	toggleSizeControl( document.forms["<?php echo $this->getName(); ?>"].type.value );
</SCRIPT>