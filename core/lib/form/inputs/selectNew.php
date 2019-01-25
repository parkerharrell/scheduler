<?php
switch( $inputAction ){
	case 'display':
		$newControlName = $conf['id'] . '_new';
		$newControlContainer = $newControlName . '_container';
		$controlContainer = $conf['id'] . '_container';

		if( ! isset($conf['options']) )
			$conf['options'] = array();
		$conf['options'][] = array( "-not-in-list", M("Not in list") );

		if( (! isset($conf['value']) || ! strlen($conf['value']) ) && (isset($conf['options'][1]))  ){
			$conf['value'] = $conf['options'][1][0];
			}
		$conf['attr']['onChange'] = 'ntsToggleNewInput_' . $conf['id'] . '(this.value)';

		global $NTS_NEW_ITEM_IN_SELECT;
		if( isset($NTS_NEW_ITEM_IN_SELECT[$conf['id']]) ){
			$conf['options'][] = array( $NTS_NEW_ITEM_IN_SELECT[$conf['id']], $NTS_NEW_ITEM_IN_SELECT[$conf['id']] );
			}

		$input .= "<div id=\"$controlContainer\">";
		break;
	}

require( dirname(__FILE__) . '/select.php' );

switch( $inputAction ){
	case 'display':
		$input .= "</div>";

		$tmpFormName = $this->getName();
		$tmpInputName = $conf['id']; 

		$input .= "<div id=\"$newControlContainer\">";
		$input .= M('New') . ': ';
		$input .= $this->makeInput(
			'text',
			array(
				'id'		=> $conf['id'] . '_new',
				'default'	=> "",
				'attr'	=> array(
					'size' => 24,
					),
				)
			);
		$input .= "</div>";

		$input .=<<<EOT
<SCRIPT LANGUAGE="Javascript">
function ntsToggleNewInput_$tmpInputName( value ){
	if( value == "-not-in-list" ){
		ntsElementShow( "$newControlContainer" );
		}
	else {
		ntsElementHide( "$newControlContainer" );
		}

	if( document.forms["$tmpFormName"].$tmpInputName.length < 2 ){
		ntsElementHide( "$controlContainer" );
		}
	else {
		ntsElementShow( "$controlContainer" );
		}
	}
ntsToggleNewInput_$tmpInputName( document.forms["$tmpFormName"].$tmpInputName.value );
</SCRIPT>
EOT;

		if( count($conf['options']) < 2 ){
		$input .=<<<EOT
<SCRIPT LANGUAGE="Javascript">
	ntsElementHide( "$controlContainer" );
</SCRIPT>
EOT;
			}

		break;

	case 'submit':
		if( $input == "-not-in-list" ){
			$newControlName = $handle . '_new';
			$input = $req->getParam( $newControlName );
			global $NTS_NEW_ITEM_IN_SELECT;
			$NTS_NEW_ITEM_IN_SELECT[ $handle ] = $input;
			}
		break;
	}
?>