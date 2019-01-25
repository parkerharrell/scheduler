<?php
class ntsRandomGenerator {
	var $useLetters;
	var $useCaps;
	var $useDigits;

	function ntsRandomGenerator(){
		$this->lettersSalt = 'abcdefghijklmnopqrstuvxyz';
		$this->capsSalt = 'ABCDEFGHIJKLMNOPQRSTUVXYZ';
		$this->digitsSalt = '0123456789';

		$this->useLetters( true );
		$this->useCaps( true );
		$this->useDigits( true );
		}

	function useLetters( $use = true ){
		$this->useLetters = $use;
		}

	function useCaps( $use = true ){
		$this->useCaps = $use;
		}

	function useDigits( $use = true ){
		$this->useDigits = $use;
		}

	function generate( $len ){
		$salt = '';
		if( $this->useLetters )
			$salt .= $this->lettersSalt;
		if( $this->useDigits )
			$salt .= $this->digitsSalt;
		if( $this->useCaps )
			$salt .= $this->capsSalt;

		srand( (double) microtime() * 1000000 );
		$pass = '';
		$i = 1;
		while ( $i <= $len ){
			$num = rand() % strlen($salt);
			$tmp = substr($salt, $num, 1);
			$pass .= $tmp;
			$i++;
			}

		return $pass;
		}
	}
?>