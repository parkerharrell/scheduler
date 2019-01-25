<?php
class ntsValidatorManager {
	var $validators = array();
	
	function ntsValidatorManager(){
		$this->validators = array();
		$this->loadValidators();
		}

	function getValidators(){
		return $this->validators;
		}

	function getValidatorsFor( $for ){
		$return = array();
		reset( $this->validators );
		foreach( $this->validators as $vi ){
			$appliedFor = $vi[2];
			if( in_array($for, $appliedFor) )
				$return[] = $vi;
			}
		return $return;
		}

	function getValidatorInfo( $key ){
		$return = array();
		reset( $this->validators );
		foreach( $this->validators as $va ){
			if( $va[0] == $key ){
				$return = $va;
				break;
				}
			}
		return $return;
		}

	function getHelpText( $vn ){
		$validatorHelp = '';

		$shortName = $vn . '.php';
		$realFileName = $this->getFileName( $vn );

		if( ! $realFileName ){
			echo $shortName . ' file does not exist!<BR';
			return $validatorHelp;
			}

		$validatorAction = 'help';
		require( $realFileName );

		return $validatorHelp;
		}

	function getFileName( $vn ){
	// CHECK IF WE CAN FIND THIS VALIDATOR FILE
		$shortName = $vn . '.php';
		$inputFile = Application::getFileFullName( $shortName );
		$realFileName = '';

	/* CUSTOM */
		$dirName = APP_ROOT_DIR . '/validators';
		$fullFileName = $dirName . '/' . $shortName;
		if( file_exists($fullFileName) )
			$realFileName = $fullFileName;

	/* APPLICATION */
		if( ! $realFileName ){
			$dirName = APP_DIR . '/base/code/form/validators';
			$fullFileName = $dirName . '/' . $shortName;
			if( file_exists($fullFileName) )
				$realFileName = $fullFileName;
			}

		if( ! $realFileName ){
	/* HCLIB */
			$dirName = HCLIB_DIR . '/form/validators';
			$fullFileName = $dirName . '/' . $shortName;
			if( file_exists($fullFileName) )
				$realFileName = $fullFileName;
			}

		return $realFileName;
		}

	function loadValidators(){
		global $NTS_CORE_DIRS;

		reset( $NTS_CORE_DIRS );
		foreach( $NTS_CORE_DIRS as $ncd ){
			$mainDir = $ncd . '/lib/form/validators';
			if( ! file_exists($mainDir) )
				continue;
			$listing = ntsLib::listFiles( $mainDir, '.php' );
			reset( $listing );
			foreach( $listing as $fileName ){
				$fullFileName = $mainDir . '/' . $fileName;
//				echo "ffn = $fullFileName<BR>";

				preg_match( '/(.+)\.php/', $fileName, $ma );
				$validatorName = $ma[ 1 ];
				$validatorTitle = $validatorName;

				$validatorAction = 'display';
			// SHOULD DEFINE $validatorTitle IN THAT FILE
				$validatorSkip = false;
				$validatorAppliedOn = array();

				require( $fullFileName );
				if( ! $validatorSkip ){
					$this->validators[] = array( $validatorName, $validatorTitle, $validatorAppliedOn );
					}
				}
			}
		return $this->validators;
		}

	// Singleton stuff
	function &getInstance(){
		return ntsLib::singletonFunction( 'ntsValidatorManager' );
		}
	}
?>