<?php
if( ! isset($headerAddon) )
	$headerAddon = '';
if( ! isset($rowAddon) )
	$rowAddon = '';

switch( $inputAction ){
	case 'display':
		$formId = $this->getName();
		$strAdd = M('Add');
		$strLoading = M('Loading') . '...';
		$thisId = $conf['id'];

		$containerId = $conf['id'] . '_Container';
		$addContainerId = $conf['id'] . '_AddContainer';
		$newContainerId = $conf['id'] . '_NewContainer';
		$readonly = ( isset($conf['readonly']) && $conf['readonly'] ) ? true : false;

// common Javascript functions

if( ! defined('NTS_DYNALIST_INCLUDED') ){
	define('NTS_DYNALIST_INCLUDED', 1);
	$jsLink = ntsLink::makeLink('system/pull', '', array('what' => 'js', 'files' => 'ntsDynaList.js'));

	$input .= "\n<script language=\"JavaScript\" src=\"$jsLink\"></script>\n";
	}

		$input .=<<<EOT
<input type="hidden" id="$thisId" name="$thisId" value=""><div id="$containerId"></div>
EOT;

		if( ! $readonly ){
			$input .=<<<EOT
<div id="$newContainerId" style="padding: 0.5em 0.5em; display: none;"></div><div id="$addContainerId" style="padding: 0.5em 0.5em;"><a name="${addContainerId}_link" id="${addContainerId}_link" class="ok" href="#${addContainerId}_link" onClick="return false;">$strAdd</a></div>
EOT;
		}

		$input .=<<<EOT

<script language="JavaScript">
var dynaList${thisId} = new ntsDynaList();
dynaList${thisId}.allItemsCount = $allItemsCount;
dynaList${thisId}.formId = "$formId";
dynaList${thisId}.controlId = "$thisId";
dynaList${thisId}.newItemsUrl = "$newItemsUrl";
dynaList${thisId}.containerId = "$containerId";
dynaList${thisId}.addContainerId = "$addContainerId";
dynaList${thisId}.newContainerId = "$newContainerId";

dynaList${thisId}.headerAddon = "$headerAddon";
dynaList${thisId}.rowAddon = "$rowAddon";
dynaList${thisId}.readonly = "$readonly";

EOT;

		if( isset($inputAddons) && $inputAddons ){
			reset( $inputAddons );
			foreach( $inputAddons as $ia ){
		$input .=<<<EOT

dynaList${thisId}.inputAddons.push( '$ia' );
EOT;

				}
			}

		if( isset($ntsDynaList_AllowEmpty) && $ntsDynaList_AllowEmpty ){
			$input .=<<<EOT
dynaList${thisId}.allowEmpty = true;

EOT;
			}

		if( isset($ntsDynaList_SortOptions) && $ntsDynaList_SortOptions ){
			$input .=<<<EOT
dynaList${thisId}.sortOptions = true;

EOT;
			}

		foreach($conf['options'] as $co ){
			$strId = $co[0];
			$strTitle = addslashes( $co[1] );

			$input .=<<<EOT

cat = new Object();
cat.id = $strId;
cat.title = "$strTitle";
EOT;

			if( isset($co[2]) && $co[2] ){
				reset($co[2]);
				foreach( $co[2] as $k => $v ){
					$strV = addslashes( $v );
					$input .= "\n" . "cat.$k = \"$strV\";\n";
					}
				}

			$input .=<<<EOT

dynaList${thisId}.currentOptions.push( cat );

EOT;

			}
//jQuery("#${newContainerId}_select").live("click", function(){ dynaList${thisId}.toggleNewForm(true); });

		$input .=<<<EOT

jQuery("#${addContainerId}_link").click(function(){ dynaList${thisId}.toggleNewForm(true); });
dynaList${thisId}.buildDisplay();
jQuery("#${newContainerId}_select").live("change", function(){ dynaList${thisId}.addOption(this.value) });
jQuery("a[id^=${thisId}_Delete_]").live("click", function(){ var objId = this.id.substring( "${thisId}_Delete_".length ); dynaList${thisId}.deleteOption(objId); });

EOT;

		if( isset($inputAddons) && $inputAddons ){
			reset( $conf['value'] );
			foreach( $conf['value'] as $k => $va ){
				reset( $inputAddons );
				foreach( $inputAddons as $ia ){
					if( isset($va[$ia]) ){
						$addonHandle = $conf['id'] . '_' . $ia . '_' . $k;
						$addonValue = $va[$ia];
						$input .=<<<EOT
//alert( '$addonHandle' + ': ' + "$addonValue");
document.forms[ "$formId" ][ "$addonHandle" ].value = "$addonValue";
EOT;
						}
					}
				}
			}

		$input .=<<<EOT

</script>

EOT;
		break;

	case 'submit':
		$input = $req->getParam( $handle );
		if( $input ){
			$input = explode( '||', $input );
			}
		else {
			$input = array();
			}

		// check also addons
		if( isset($inputAddons) && $inputAddons ){
			$finalInput = array();

			reset( $input );
			foreach( $input as $in ){
				$finalInput[$in] = array();
				reset( $inputAddons );
				foreach( $inputAddons as $ia ){
					$addonHandle = $handle . '_' . $ia . '_' . $in;
					$addonInput = $req->getParam( $addonHandle );
					$finalInput[ $in ][ $ia ] = $addonInput;
					}
				}
			$input = $finalInput;
			}
		break;

	case 'check_submit':
		$input = isset( $_POST[$handle] ) ? true : false;
		break;
	}
?>