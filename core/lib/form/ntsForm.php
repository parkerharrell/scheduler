<?php
class ntsForm {
	function ntsForm( $formFile, $defaults = array() ){
		$this->formFile = $formFile;

		$this->defaults = $defaults;
		$this->inputs = array();
		$this->groupValidators = array();

		$this->errors = array();
		$this->values = array();
		$this->params = array();

		$this->requiredFields = 0;
		$this->formAction = 'display';
		$this->readonly = false;

		$this->skipRequiredAlert = false;
		$this->activator = array();
		$this->valid = true;
		$this->formId = '';
		}

	function setParams( $params ){
		$this->params = $params;
		}

/* Builds the set of hidden fields for panel, action, and params */
	function makePostParams( $panel, $action = '', $params = array() ){
		global $NTS_PERSISTENT_PARAMS, $NTS_CURRENT_PANEL;

		if( preg_match('/^\-current\-/', $panel) ){
			$replaceFrom = '-current-';
			$replaceTo = $NTS_CURRENT_PANEL;

			if( preg_match('/\/\.\./', $panel) ){
				$downCount = substr_count( $panel, '/..' );
				$re = "/^(.+)(\/[^\/]+){" . $downCount. "}$/U";
				preg_match($re, $replaceTo, $ma);
				$replaceFrom = '-current-' . str_repeat('/..', $downCount);
				$replaceTo = $ma[1];
				}

			$panel = str_replace( $replaceFrom, $replaceTo, $panel );
			}

		$params[ NTS_PARAM_PANEL ] = $panel;
		$params[ NTS_PARAM_ACTION ] = $action;

		if( $NTS_PERSISTENT_PARAMS ){
			reset( $NTS_PERSISTENT_PARAMS );
		/* global */
			if( isset($NTS_PERSISTENT_PARAMS['/']) ){
				reset( $NTS_PERSISTENT_PARAMS['/'] );
				foreach( $NTS_PERSISTENT_PARAMS['/'] as $p => $v ){
					$params[ $p ] = $v;
					}
				}
		/* above panel */
			reset( $NTS_PERSISTENT_PARAMS );
			foreach( $NTS_PERSISTENT_PARAMS as $pan => $pampam ){
				if( substr($panel, 0, strlen($pan) ) != $pan )
					continue;
				reset( $pampam );
				foreach( $pampam as $p => $v )
					$params[ $p ] = $v;
				}
			}

		reset( $params );
		$postParts = array();
		foreach( $params as $p => $v ){
			if( is_array($v) ){
				foreach( $v as $va )
					$postParts[] = '<INPUT TYPE="hidden" NAME="' . $p . '[]" VALUE="' . $va . '">';
				}
			else
				$postParts[] = '<INPUT TYPE="hidden" NAME="' . $p . '" VALUE="' . $v . '">';
			}

		$post = join( "\n", $postParts );
		return $post;
		}

	function getName(){
		return $this->formId;
		}

/* Displays form */
	function useActivator( $activatorProps = array() ){
		$this->activator = $activatorProps;
		}

	function display( $vars = array(), $skipEnd = false, $skipStart = false ){
		$this->requiredFields = 0;
		$this->formAction = 'display';

	// START UP HTML
		$this->formId = 'nts_form_' . ntsLib::getUniqueId();
		$startUp = '';

	// NOW DISPLAY CONTROLS
		$displayFile = $this->formFile . '.php';
		ob_start();
		require( $displayFile );
		$formContent = ob_get_contents();
	// SHOW REQUIRED TEXT
		if( $this->requiredFields > 0 && ( ! $this->skipRequiredAlert) ){
			$formContent = "\n<P>" . '<i>' . '* ' . M('Required field') . '</i>' . $formContent;
			}
		ob_end_clean();

		if( $this->activator ){
			$activatorClass = isset($this->activator['class']) ? $this->activator['class'] : '';
			$activatorTitle = isset($this->activator['title']) ? $this->activator['title'] : '';
			$activatorId = $this->formId . '_activator';
			$activatorView = "<a href=\"#\" class=\"$activatorClass\" id=\"$activatorId\">$activatorTitle</a>";
			$formWrapperId = $this->formId . '_wrapper'; 

			$formContent =<<<EOT
$activatorView
<div id="$formWrapperId" style="display: none;">
$formContent
</div>
EOT;

			if( $this->valid ){
				$formContent .=<<<EOT
<script language="JavaScript">
jQuery("#$activatorId").click(function(){
	jQuery("#$formWrapperId").show();
	jQuery("#$activatorId").hide();
	}
	);
</script>
EOT;
				
				}
			else {
				$formContent .=<<<EOT
<script language="JavaScript">
jQuery("#$formWrapperId").show();
jQuery("#$activatorId").hide();
</script>
EOT;
				}
			}

		if( ! $skipStart ){
			$startUp .= "\n" . '<FORM METHOD="post"';
			$startUp .= ' ACTION="' . NTS_ROOT_WEBPAGE . '"';
			$startUp .=  ' ENCTYPE="multipart/form-data"';
			$startUp .= ' NAME="' . $this->formId . '"';
			$startUp .= ">\n";
			}

		echo $startUp;
		echo $formContent;

	// END HTML
		if( ! $skipEnd ){
			$end = '';
			$end .= '</FORM>';
			echo $end;
			}
		}

/* registers input */
	function registerInput( $type, $inputArray, $validators = array() ){
		$conf = array_merge( $this->_inputDefaults(), $inputArray );
		$conf[ 'type' ] = $type;
		$conf[ 'validators' ] = $validators;
		$this->inputs[] = $conf;
		}
	
	function registerGroupValidator( $validatorId, $validatorArray ){
		$this->groupValidators[ $validatorId ] = $validatorArray;
		}

/* builds input HTML code */
	function makeInput( $type, $inputArray, $validators = array() ){
		if( $this->readonly )
			$inputArray[ 'readonly' ] = 1;

		if( $this->formAction == 'validate' ){
			return $this->registerInput( $type, $inputArray, $validators );
			}

		$conf = array_merge( $this->_inputDefaults(), $inputArray );

		if( $type == 'radio' ){
			$conf['groupValue'] = $this->getValue( $conf['id'], $conf['default'] );
			}

		if( ! isset($conf['value']) )
			$conf['value'] = $this->getValue( $conf['id'], $conf['default'] );

	/* if it is one entry only */
		reset( $validators );
		foreach( $validators as $va ){
			$shortValidatorName = basename( $va['code'], '.php' );
			if( ($shortValidatorName == 'oneEntryOnly') && ( strlen($conf['value']) > 0 ) ){
				$conf['attr']['readonly'] = 'readonly';
				$conf['attr']['disabled'] = 'disabled';
				}
			}

		$conf['error'] = $this->_getErrorForInput( $conf['id'] );
		$input = '';
		$inputAction = 'display';
		if( ! isset($conf['htmlId']) ){
			$conf['htmlId'] = $this->formId . '-' . $conf['id'];
			}

	// INCLUDE THE RIGHT INPUT FILE
		$inputFile = 'lib/form/inputs/' . $type . '.php';
		$inputFile = ntsLib::fileInCoreDirs( $inputFile );
		if( $inputFile )
			require( $inputFile );
		else
			echo $shortName . ' file does not exist!<BR';

		if( $conf['help'] )
			$input .= '<br /><i>' . $conf['help'] . '</i>';

	// TRANSLATE TO ESCAPE LONG PARAM NAMES	
		if( isset($conf['translated']) && $conf['translated'] ){
			$translatedName = '_real_translated_' . $this->translatedCount;
			$input .= $this->makeInput(
				'hidden',
				array(
					'id'	=> $translatedName,
					'value'	=> $realHandle,
					)
				);
			}

	// COMPILE OUTPUT
		$return = '';
		if( $conf['error'] )
			$return .= '<strong class="alert">' . $conf['error'] . '</strong><br />';
		$return .= $input;

		if( $conf['required'] )
			$this->requiredFields++;

		return $return;
		}

/* Validates form - group validator for several inputs */
	function makeValidator( $validatorId, $validatorArray ){
		if( $this->formAction == 'validate' ){
			return $this->registerGroupValidator( $validatorId, $validatorArray );
			}

	// COMPILE OUTPUT
		$return = '';
		if( isset($this->groupErrors[$validatorId]) )
			$return .= '<strong class="alert" style="display: block;">' . $this->groupErrors[$validatorId] . '</strong>';

		return $return;
		}

/* Validates form */
	function validate( $req, $removeValidation = array() ){
		$formValid = true;

		$this->inputs = array();
		$this->errors = array();
		$this->values = array();

		ob_start();
		$formFile = $this->formFile . '.php';
		$this->formAction = 'validate';
		require( $formFile );
		ob_end_clean();

	// NOW GRAB
		reset( $this->inputs );
		$supplied = array();

	// IF WE HAVE TRANSLATED THINGS
		$translatedCount = $req->getParam( '_translated_count' );
		if( $translatedCount ){
			$translated = array();
			for( $i = 1; $i <= $translatedCount; $i++ ){
				$translatedName = '_translated_' . $i;
				$realName = $req->getParam( '_real_translated_' . $i );
				$translated[ $realName ] = $translatedName;
				}
			}

		foreach( $this->inputs as $controlConf ){
			$realHandle = $controlConf['id'];
			if( $translatedCount && isset($translated[$realHandle]) ){
				$controlConf['id'] = $translated[$realHandle];
				}

		// GRAB WITH THE TRANSLATED NAME
			if( ! $this->inputSupplied($req, $controlConf['id'], $controlConf['type']) ){
				continue;
				}

			$suppliedValue = $this->grabValue( $req, $controlConf['id'], $controlConf['type'] );

		// SWITCH BACK TO REAL HANDLE IF TRANSLATED
			$controlConf['id'] = $realHandle;

			$this->values[ $controlConf['id'] ] = $suppliedValue;
			$supplied[] = $controlConf['id'];
			}

	// NOW VALIDATE
		reset(  $this->inputs );
		$formValues = array_merge( $this->defaults, $this->values );

		foreach(  $this->inputs as $controlConf ){
			if( ! in_array($controlConf['id'], $supplied) )
				continue;

			$checkValue = $this->values[ $controlConf['id'] ];

		/* built-in control validation */
			$inputAction = 'validate';
			$validationFailed = false;
			$validationError = '';

			$shortName = 'inputs/' . $controlConf['type'] . '.php';
			$handle = $controlConf['id'];

			$inputFile = 'lib/form/inputs/' . $controlConf['type'] . '.php';
			$inputFile = ntsLib::fileInCoreDirs( $inputFile );
			if( $inputFile )
				require( $inputFile );
			else
				echo $shortName . ' file does not exist!<BR';

			if( $validationFailed ){
				$this->errors[ $controlConf['id'] ] = $validationError;
				$formValid = false;
				break;
				}

			if( ! isset($controlConf['validators']) )
				continue;
	
		/* external validation */
			$checkValue = $this->values[ $controlConf['id'] ];
			if( (! $removeValidation) || (! in_array($controlConf['id'],$removeValidation) ) ){
				reset( $controlConf['validators'] );
				foreach( $controlConf['validators'] as $validatorInfo ){
					if( ! isset($validatorInfo['error']) )
						$validatorInfo['error'] = '';

					if( isset($validatorInfo['directCode']) ){
						$realCodeFile = $validatorInfo['directCode'];
						if( ! file_exists($realCodeFile) ){
							echo "validator file '$realCodeFile' doesn't exist!<BR>";
							continue;
							}
						}
					else {
						$codeFile = $validatorInfo['code'];
						$realCodeFile = ntsLib::fileInCoreDirs( '/lib/form/validators/' . $codeFile );
					/* include validator file if exists */
						if( ! $realCodeFile ){
							echo "validator file '$codeFile' doesn't exist!<BR>";
							continue;
							}
						}

					$validationFailed = false;
					$validationError = '';
					$validationParams = ( isset($validatorInfo['params']) ) ? $validatorInfo['params'] : array();
					$formParams = $this->params;

				/* include validator file */
					$validatorAction = 'validate';
					require( $realCodeFile );

					if( $validationFailed ){
						if( ! $validationError )
							$validationError = $validatorInfo['error'];
							
						$this->errors[ $controlConf['id'] ] = $validationError;

						$formValid = false;
						break;
						}
					}
				}
			}

	/* now check the group validators if any */
		reset( $this->groupValidators );
		foreach( $this->groupValidators as $gvId => $gv ){
			if( ! isset($gv['error']) )
				$gv['error'] = '';

			if( isset($gv['directCode']) ){
				$realCodeFile = $gv['directCode'];
				if( ! file_exists($realCodeFile) ){
					echo "validator file '$realCodeFile' doesn't exist!<BR>";
					continue;
					}
				}
			else {
				$codeFile = $gv['code'];
				$realCodeFile = ntsLib::fileInCoreDirs( '/lib/form/validators/' . $codeFile );
				}

		/* include validator file if exists */
			if( ! $realCodeFile ){
				echo "validator file '$codeFile' doesn't exist!<BR>";
				continue;
				}

			$formValues = array_merge( $this->defaults, $this->values );
			$validationFailed = false;
			$validationError = '';
			$validationParams = ( isset($gv['params']) ) ? $gv['params'] : array();

		/* include validator file */
			require( $realCodeFile );

			if( $validationFailed ){
				if( ! $validationError )
					$validationError = $gv['error'];
				$this->groupErrors[ $gvId ] = $validationError;

				$formValid = false;
				break;
				}
			}

		$this->valid = $formValid;
		return $formValid;
		}

	function setValue( $ctlId, $ctlValue ){
		$this->values[ $ctlId ] = $ctlValue;
		}

	function getValues(){
		return $this->values;
		}

/* Prefills an input attributes */
	function _inputDefaults(){
		$def = array(
			'id'		=> ntsLib::getUniqueID(),
			'label'		=> '',
			'default'	=> '',
			'help'		=> '',
			'attr'		=> array(),
			'required'	=> 0,
			);
		return $def;
		}

	function getDefaults(){
		return $this->defaults;
		}

/* Checks if a value has been supplied, or returns default otherwise */
	function getValue( $name, $defaultValue = '' ){
		if( isset($this->values[$name]) ){
			$value = $this->values[$name];
			}
		elseif( isset($this->defaults[$name]) ){
			$value = $this->defaults[$name];
			}
		else {
			$value = $defaultValue;
			}
		return $value;
		}

/* Checks if a validation error happend for this input */
	function _getErrorForInput( $name ){
		$error = ( isset($this->errors[$name]) ) ? $this->errors[$name] : '';
		return $error;
		}

/* Builds HTML string with input attributes */
	function _makeInputParams( $params = array() ){
		$paramsCode = array();
		reset( $params );
		foreach( $params as $key => $value ){
			if( $key == '_class' )
				continue;
			$paramsCode[] = $key . '="' . htmlspecialchars($value) . '"';
			}
		$return = join( ' ', $paramsCode );
		return $return;
		}

/* Grabs an input value - actual code in the input file */
	function grabValue( $req, $handle, $type = '' ){
		$input = '';
		$inputAction = 'submit';

	// INCLUDE THE RIGHT INPUT FILE
		$shortName = 'inputs/' . $type . '.php';

		$inputFile = 'lib/form/inputs/' . $type . '.php';
		$inputFile = ntsLib::fileInCoreDirs( $inputFile );
		if( $inputFile )
			require( $inputFile );
		else
			echo $shortName . ' file does not exist!<BR';

	/* if not admin then strip HTML tags */
		global $NTS_CURRENT_USER;
		if( ! $NTS_CURRENT_USER->hasRole('admin') ){
			if( is_array($input) ){
				}
			else {
				$input = strip_tags( $input );
				}
			}

		return $input;
		}

/* Checks if an input has been really supplied - actual code in the input file */
	function inputSupplied( $req, $handle, $type = '' ){
		$input = '';
		$inputAction = 'check_submit';

	// INCLUDE THE RIGHT INPUT FILE
		$shortName = 'inputs/' . $type . '.php';

		$inputFile = 'lib/form/inputs/' . $type . '.php';
		$inputFile = ntsLib::fileInCoreDirs( $inputFile );
		if( $inputFile )
			require( $inputFile );
		else
			echo $shortName . ' file does not exist!<BR';

		return $input;
		}
	}
?>