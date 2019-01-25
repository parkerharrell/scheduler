<?php
/* form params - used later for validation */
$this->setParams(
	array(
		'myId'		=> $this->getValue('id'),
		'formId'	=> $this->getValue('form_id'),
		)
	);
$formId = $this->getValue('form_id');
$addValidator = $this->getValue('add_validator');
?>
<TABLE>
<TR>
	<TH><?php echo M('System Name'); ?></TH>
	<TD>
	<?php echo $this->getValue( 'name' ); ?>
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
				'size'	=> 32,
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
					'skipMe'	=> 1,
					'also'	=> array(
						'form_id'	=> " = $formId"
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

<?php $myType = $this->getValue('type'); ?>
<?php if( 1 || $myType != 'checkbox' ) : ?>

<p>
<h3><?php echo M('Input Validation'); ?></h3>
<p>
<table>
<tr class="listing-header">
	<th><?php echo M('Validation'); ?></th>
	<th><?php echo M('Error Message If Validation Fails'); ?></th>
	<th>&nbsp;</th>
</tr>
<?php
$vm =& ntsValidatorManager::getInstance();
$allValidators = $vm->getValidatorsFor( $this->getValue('type') );
//_print_r( $allValidators );

$currentValidators = $this->getValue('validators');
//_print_r( $currentValidators );

$currentValidatorsNames = array_map( create_function('$a', 'return $a["code"];'), $currentValidators );
$remainValidators = array();
reset( $allValidators );
foreach( $allValidators as $va ){
	if( ! in_array($va[0], $currentValidatorsNames) )
	$remainValidators[] = $va;
	}
//_print_r( $remainValidators );

reset( $currentValidators );
$vCount = 0;
?>
<?php $count = 0; ?>
<?php foreach( $currentValidators as $cv ) : ?>
<tr class="<?php echo (($count++) % 2) ? 'even' : 'odd'; ?>">
	<?php
	$vi = $vm->getValidatorInfo( $cv['code'] );
	if( ! $vi )
		continue;
	$vCount++;
	$title = $vi[1];
	?>
	<td>
		<b><?php echo $title; ?></b>
	</td>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'hidden',
	/* attributes */
		array(
			'id'		=> 'vld-' . 	$vCount,
			'default'	=> $cv['code'],
			)
		);
	?>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'error-' . 	$vCount,
			'default'	=> $cv['error'],
			'attr'		=> array(
				'size'	=> 42,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> 'Please enter the error message text',
				),
			)
		);
	?>
	</td>
	<td>
		<a class="alert" href="<?php echo ntsLink::makeLink('-current-', 'delete_validator', array('id' => $this->getValue('id'), 'validator' => $vCount) ); ?>"><?php echo M('Delete'); ?></a>
	</td>
</tr>
<?php endforeach; ?>
<tr>
<?php if( $addValidator ) : ?>
	<?php 
		$vCount++;
		reset( $remainValidators );
		$chooseOptions = array();
		foreach( $remainValidators as $rv ){
			$chooseOptions[] = array( $rv[0], $rv[1] );
			}
	?>
	<th>
		<?php
		echo $this->makeInput (
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> 'vld-' . $vCount,
				'options'	=> $chooseOptions,
				'required'	=> 1,
				),
		/* validators */
			array(
				array(
					'code'		=> 'notEmpty.php', 
					'error'		=> 'Please enter the error message text',
					),
				)
			);
		?>
	</th>
	<td>
		<?php
		echo $this->makeInput (
		/* type */
			'text',
		/* attributes */
			array(
				'id'		=> 'error-' . 	$vCount,
				'attr'		=> array(
					'size'	=> 42,
					),
				'required'	=> 1,
				),
		/* validators */
			array(
				array(
					'code'		=> 'notEmpty.php', 
					'error'		=> 'Please enter the error message text',
					),
				)
			);
		?>
	</td>
	<td>
		&nbsp;
	</td>	
<?php else : ?>
	<td colspan="3">
		<?php if( count($remainValidators) ) : ?>
			<a class="ok" href="<?php echo ntsLink::makeLink('-current-', '', array('id' => $this->getValue('id'), 'add_validator' => 1) ); ?>"><?php echo M('Add'); ?></a>
		<?php endif; ?>
	</td>
<?php endif; ?>
</tr>
</table>

<?php endif; ?>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update', array('id' => $this->getValue('id'), 'add_validator' => $addValidator) ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Save'); ?>">
</DIV>

<SCRIPT LANGUAGE="JavaScript">
	toggleSizeControl( document.forms["<?php echo $this->getName(); ?>"].type.value );
</SCRIPT>