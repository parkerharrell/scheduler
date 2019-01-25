<?php
class ntsEncryptor {
	var $key;
	function ntsEncryptor($key = ''){
		$this->key = $key;
		}

	function encrypt($data){
		$data = trim( $data );
		$out = '';
		if( strlen($this->key)>0 ){ // real encryption
			$current_key = md5($this->key);
			$key_len = strlen($current_key);
			$data_len = strlen($data);
			if( $data_len < 8 ){
				$data = str_pad( $data, 8, ' ' );
				$data_len = strlen($data);
				}

			$iterations = ceil( $data_len / $key_len);

		/* init keys */
			for($i = $iterations - 1; $i >= 0; $i--){
				$current_key = md5( $current_key );
				$keys[ $i ] = $current_key;
				}

		/* encrypt */
			for($i = 0; $i < $iterations; $i++){
				$current_key = $keys[ $i ];
				$out .= substr($data, $i * $key_len, $key_len) ^ $current_key;
				}
			}
		else { //hashing - for passwords
			$out = md5($data);
			}

		$out = base64_encode( $out );
		return $out;
		}

	function decrypt($data){
		$data = str_replace(' ','+',$data);
		$data = base64_decode( $data );

		$out = '';
		$current_key = md5($this->key);
		$key_len = strlen($current_key);
		$data_len = strlen($data);

		$iterations = ceil( $data_len / $key_len);

	/* init keys */
		for($i = $iterations - 1; $i >= 0; $i--){
			$current_key = md5( $current_key );
			$keys[ $i ] = $current_key;
			}

		for($i = 0; $i < $iterations; $i++){
			$current_key = $keys[ $i ];
			$out .= substr($data, $i * $key_len, $key_len) ^ $current_key;
			}

		$out = trim( $out );
		return $out;
		}
	}
?>