<?php
$conf['options'] = array(
	array('text',		M('Text') ),
	array('checkbox',	M('Yes/No') ),
	array('textarea',	M('Textarea') ),
	array('select',		M('Select') ),
	);
$conf['attr']['onChange'] = 'toggleSizeControl( this.value );';

require( dirname(__FILE__) . '/select.php' );

switch( $inputAction ){
	case 'display':
		$jsFile = NTS_BASE_DIR . '/lib/js/functions.js';
		$jsCode = ntsLib::fileGetContents( $jsFile );
		$input .=<<<EOT

<script language="Javascript">
$jsCode
</script>

<SCRIPT LANGUAGE="JavaScript">
function toggleSizeControl( typeSelected ){
	switch( typeSelected ){
	// TEXT
		case 'text':
			ntsElementDelete( 'requireOptions-Textarea' );
			ntsElementDelete( 'requireOptions-Select' );
			ntsElementDelete( 'requireOptions-Checkbox' );
			ntsElementDelete( 'validators_select' );
			ntsElementRestore( 'requireOptions-Text' );
			ntsElementRestore( 'validators' );
			break;

	// YES/NO
		case 'checkbox':
			ntsElementDelete( 'requireOptions-Textarea' );
			ntsElementDelete( 'requireOptions-Text' );
			ntsElementDelete( 'validators' );
			ntsElementDelete( 'requireOptions-Select' );
			ntsElementDelete( 'validators_select' );
			ntsElementRestore( 'requireOptions-Checkbox' );
			break;

	// TEXTAREA
		case 'textarea':
			ntsElementDelete( 'requireOptions-Text' );
			ntsElementDelete( 'requireOptions-Select' );
			ntsElementDelete( 'requireOptions-Checkbox' );
			ntsElementDelete( 'validators_select' );
			ntsElementRestore( 'requireOptions-Textarea' );
			ntsElementRestore( 'validators' );
			break;

	// SELECT
		case 'select':
			ntsElementRestore( 'requireOptions-Select' );
			ntsElementRestore( 'validators_select' );
			ntsElementDelete( 'requireOptions-Textarea' );
			ntsElementDelete( 'requireOptions-Text' );
			ntsElementDelete( 'requireOptions-Checkbox' );
			ntsElementDelete( 'validators' );
			break;
		}
	}
</SCRIPT>

EOT;
		break;
	}
?>