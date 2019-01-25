<?php
class ntsPaypal {
   var $last_error;
   var $ipn_log;
   var $ipn_log_file;
   var $ipn_response;
   var $ipn_data = array();

   function ntsPaypal(){
		$this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
		$this->last_error = '';
		$this->ipn_log_file = '.ipn_results.log';
		$this->ipn_log = true; 
		$this->ipn_response = '';
		$this->ipn_data = array();
		}

	function validateIpn() {
		// parse the paypal URL
		$urlParsed = parse_url( $this->paypal_url );

		// generate the post string from the _POST vars aswell as load the
		// _POST vars into an arry so we can play with them from the calling script.
		$post_string = '';
		foreach( $_POST as $field => $value ){
			$this->ipn_data[ $field ] = $value;
			$post_string .= $field . '=' . urlencode(stripslashes($value)) . '&';
			}
		$post_string .= 'cmd=_notify-validate';

		// open the connection to paypal
		$fp = fsockopen( $urlParsed['host'] , '80', $err_num, $err_str, 30);
		if( ! $fp ){
			// could not open the connection.  If loggin is on, the error message will be in the log.
			$this->last_error = "fsockopen error no. $errnum: $errstr";
			return false;
			}
		else {
			// Post the data back to paypal
			fputs($fp, "POST " . $urlParsed['path'] . " HTTP/1.1\r\n"); 
			fputs($fp, "Host: " . $urlParsed['host'] . "\r\n"); 
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
			fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
			fputs($fp, "Connection: close\r\n\r\n"); 
			fputs($fp, $post_string . "\r\n\r\n"); 

			// loop through the response from the server and append to variable
			while(!feof($fp)) { 
				$this->ipn_response .= fgets($fp, 1024); 
				}

			fclose($fp); // close connection
			}

		if ( eregi('VERIFIED', $this->ipn_response) ){
			// Valid IPN transaction.
			return true;
			}
		else {
			// Invalid IPN transaction.  Check the log for details.
			$this->last_error = 'IPN Validation Failed.';
			return false;
			}
		}
	}
?>