<?php
class ntsSms {
	var $body;
	var $from;
	var $debug;
	var $disabled = false;
	var $error;

	function addLog(){
		}

	function _realSend( $to, $msg, $from = '' ){
		$return = 0;

		$this->setError( '' );
		$plugin = 'sms';

		$plm =& ntsPluginManager::getInstance();
		$gateway = $plm->getPluginSetting( $plugin, 'gateway' );

	/* the send file should return $success and $response vars */
		$sendFile = dirname(__FILE__) . '/../gateways/' . $gateway . '/send.php';
		require( $sendFile );

	/* add log */
		$ntsdb =& dbWrapper::getInstance();
		$tblName = 'smslog';
		$paramsArray = array(
			'sent_at'	=> time(),
			'to_number'		=> $to,
			'from_number'	=> $from,
			'message'	=> $msg,
			'success'	=> $success,
			'response'	=> $response,
			'gateway'	=> $gateway,
			);

		$ntsdb->insert( $tblName, $paramsArray, array('to' => 'string', 'from' => 'string') );
	/* end of log */

		return $success;
		}

	function ntsSms(){
		$plugin = 'sms';
		$this->body = '';
		$this->error = '';

		$this->disabled = false;

	/* from, from name, and debug settings */
		$plm =& ntsPluginManager::getInstance();
		$this->disabled = ( $plm->getPluginSetting( $plugin, 'disabled' ) ) ? true : false;
		$this->debug = ( $plm->getPluginSetting( $plugin, 'debug' ) ) ? true : false;
		}

	function setBody( $body ){
		$this->body = $body;
		}
	function setFrom( $from ){
		$this->from = $from;
		}

	function sendToOne( $toEmail ){
		$toArray = array( $toEmail );
		return $this->_send( $toArray );
		}

	function getBody(){
		return $this->body;
		}

	function _send( $toArray = array() ){
		if( $this->disabled )
			return true;

		$plugin = 'sms';
		$plm =& ntsPluginManager::getInstance();
		$settings = $plm->getPluginSettings( $plugin );
		if( isset($settings['from']) ){
			$this->setFrom( $settings['from'] );
			}

		$from = $this->from;
		$msg = $this->getBody();

		reset( $toArray );
		if( $this->debug ){
			echo '<PRE>';
			echo "<BR>-------------------------------------------<BR>";
			echo "SMS MESSAGE";
			echo "<BR>-------------------------------------------<BR>";
			foreach( $toArray as $to ){
				echo "To:<I>$to</I><BR>";
				}
			echo "From:<I>$from</I><BR>";
			echo 'Msg:<BR><I>' . $msg . '</I><BR>';
			echo "<BR>-------------------------------------------<BR>";
			echo '</PRE>';
			}
		else {
			foreach( $toArray as $to ){
				$this->_realSend( $to, $msg, $from );
				}
			}

		return true;
		}

	function isError(){
		$return = $this->error ? true : false;
		return $return;
		}

	function getError(){
		return $this->error;
		}

	function setError( $error ){
		$this->error = $error;
		}
	}
?>