<?php
class ntsEmailTemplateManager {
	var $defaultFolder;
	var $languageFolder;
	var $fileName;
	var $dbKeyPrefix;
	var $templates;
	var $tags;

	function ntsEmailTemplateManager(){
		$this->defaultFolder = NTS_APP_DIR . '/defaults/language';
		$this->languageFolder = NTS_EXTENSIONS_DIR . '/languages';
		$this->fileName = 'emails.xml';
		$this->dbKeyPrefix = '';
		
		$this->templates = array();
		$this->tags = array();
		$this->init();
		}

	function init(){
		$om =& objectMapper::getInstance();
		$customerFields_External = array();
		$customerFields_Internal = array();
		$appointmentFields = array();
		$userFields = array();
		$providerFields = array();

		$fields = $om->getFields( 'user' );
		reset( $fields );
		foreach( $fields as $f )
			$userFields[] = $f[0];
		$userFields[] = 'password';

	// customer
		$fields = $om->getFields( 'customer', 'external' );
		reset( $fields );
		foreach( $fields as $f )
			$customerFields_External[] = $f[0];
		$customerFields_External[] = 'password';

		$fields = $om->getFields( 'customer', 'internal' );
		reset( $fields );
		foreach( $fields as $f )
			$customerFields_Internal[] = $f[0];
		$customerFields_Internal[] = 'password';

	// customer
		$appointmentFields = array('starts_at', 'service', 'service.description', 'location', 'location.description', 'resource', 'resource.description' );
		$fields = $om->getFields( 'appointment' );
		reset( $fields );
		foreach( $fields as $f )
			$appointmentFields[] = $f[0];
		$appointmentFields[] = 'price';
		$appointmentFields[] = 'seats';
		$appointmentFields[] = 'link_to_ical';

	// resource
		$fields = $om->getFields( 'provider' );
		reset( $fields );
		foreach( $fields as $f )
			$providerFields[] = $f[0];

	/* common header & footer */
		$this->addTags( 'common-header-footer', array('recipient' => $userFields ) );

	/* customer related emails sent to customer */
		$this->addTags( 'user-*', array('user' => $customerFields_External ) );
		$this->addTags( 'user-require_email_confirmation-user', array('user' => array('CONFIRMATION_LINK') ) );

	/* customer related emails sent to admin */
		$this->addTags( 'user-*-admin', array('user' => $customerFields_Internal ) );

	/* appointment related fields sent to customer */
		$this->addTags( 'appointment-*', array('appointment' => $appointmentFields ) );
		$this->addTags( 'appointment-reject-customer', array('appointment' => array('REJECT_REASON') ) );
		$this->addTags( 'appointment-cancel-customer', array('appointment' => array('CANCEL_REASON') ) );
		$this->addTags( 'appointment-reschedule-*', array('old_appointment' => array('STARTS_AT') ) );

	/* appointment related fields sent to customer */
		$this->addTags( 'appointment-*-customer', array('appointment.customer' => $customerFields_External ) );

	/* appointment related fields sent to provider */
		$this->addTags( 'appointment-*-provider', array('appointment.customer' => $customerFields_External ) );
		$this->addTags( 'appointment-cancel-provider', array('appointment' => array('CANCEL_REASON') ) );

		$this->addTags( 'appointment-require_approval-provider', array('appointment' => array('QUICK_LINK_REJECT', 'QUICK_LINK_APPROVE') ) );
		}

	function addTags( $tpl, $tags ){
		$this->tags[ $tpl ] = $tags;
		}

	function getTags( $tpl ){
		$return = array();
		reset( $this->tags );
		foreach( $this->tags as $key => $tagsArray ){
			$include = false;
			// if wildcard
			if( strpos($key, '*') === false ){
				$include = ( $key == $tpl ) ? true : false;
				}
			else {
				$re = '/' . str_replace( '*', '.+', $key ) . '/';
				$include = preg_match( $re, $tpl ) ? true : false;
				}

			if( ! $include )
				continue;

			reset( $tagsArray );
			foreach( $tagsArray as $className => $subTags ){
				if( count($subTags) > 1 ){
					$thisTag = '{' . strtoupper($className) . '.-ALL-}'; 
					if( ! in_array($thisTag, $return) )
						$return[] = $thisTag;
					}

				reset( $subTags );
				foreach( $subTags as $st ){
					$thisTag = '{' . strtoupper($className) . '.' . strtoupper($st) . '}'; 
					if( ! in_array($thisTag, $return) )
						$return[] = $thisTag;
					}
				}
			}
			
		return $return;
		}

	function getKeys( $lang = 'en' ){
		if( ! isset($this->templates[$lang]) )
			$this->load( $lang );

		$keys = array_keys( $this->templates[$lang] );
		return $keys;
		}

	function getTemplate( $lang, $key ){
		global $SKIP_NOTIFICATIONS;
		if( $SKIP_NOTIFICATIONS ){
			if( in_array($key, $SKIP_NOTIFICATIONS) )
				return null;
			}

		if( ! isset($this->templates[$lang]) ){
			$this->load( $lang );
			}

		if( isset($this->templates[$lang][$key]) ){
			return $this->templates[$lang][$key];
			}
		else {
			if( ! isset($this->templates['en']) )
				$this->load( 'en' );
			if( isset($this->templates['en'][$key]) )
				return $this->templates['en'][$key];
			}
		return null;
		}

	function load( $lang ){
		$templateFiles = array();
		if( $lang == 'en-builtin' ){
			$templateFile = $this->defaultFolder . '/' . $this->fileName;
			$templateFiles[] = $templateFile;
			}
		elseif( $lang == 'en' ){
			$templateFile = $this->languageFolder . '/' . $lang . '/' . $this->fileName;
			if( file_exists($templateFile) ){
				$this->load( 'en-builtin' );
				}
			else {
				$templateFile = $this->defaultFolder . '/' . $this->fileName;
				}
			$templateFiles[] = $templateFile;
			}
		else {
			$templateFile = $this->languageFolder . '/' . $lang . '/' . $this->fileName;
			$templateFiles[] = $templateFile;
			}

		// add from plugins
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		reset( $activePlugins );
		foreach( $activePlugins as $plg ){
			$templateFiles[] = $plm->getPluginFolder( $plg ) . '/defaults/language/' . $this->fileName;
			}

		if( $lang == 'en-builtin' )
			$lang = 'en';

		$this->templates[ $lang ] = array();

		reset( $templateFiles );
		foreach( $templateFiles as $templateFile ){
			if( file_exists($templateFile) ){
				$xmlCode = ntsLib::fileGetContents( $templateFile );

			/* get first line to see if encoding is defined */
				$firstLine = ntsLib::fileGetFirstLine( $templateFile );
				$re = '/encoding\s*=\s*[\'|\"](.+)[\'|\"]/U';
				if( preg_match($re, $firstLine, $ma) ){
					$encoding = $ma[1];
					$parser = new xml_simple( $encoding );
					}
				else {
					$parser = new xml_simple();
					}

				$templateConf = $parser->parse( $xmlCode );

				if( $parser->error ){
				/* template file error */
					$error = "TEMPLATE FILE ERROR:<br>" . $templateFile . "<br>" . $parser->error;
					echo $error;
					return;
					}
				if( ! isset($templateConf['message']) ){
				/* template file error */
					$error = "TEMPLATE FILE ERROR:<br>" . $templateFile . "<br>" . 'Message tag missing';
					echo $error;
					return;
					}

				reset( $templateConf['message'] );
				foreach( $templateConf['message'] as $templConf ){
					if( isset($templConf['key']) && isset($templConf['body']) ){
						$this->templates[ $lang ][ $templConf['key'] ] = array();
						$this->templates[ $lang ][ $templConf['key'] ]['body'] = $templConf['body'];
						if( isset($templConf['subject']) )
							$this->templates[ $lang ][ $templConf['key'] ]['subject'] = $templConf['subject'];
						}
					}
				}
			}

		/* also load from database */
		$ntsdb =& dbWrapper::getInstance();

		$sql = "SELECT template, subject, body FROM {PRFX}templates WHERE lang = '$lang'";
		$result = $ntsdb->runQuery( $sql );
		while( $t = $result->fetch() ){
			if( ( ! $this->dbKeyPrefix ) || ( substr($t['template'], 0, strlen($this->dbKeyPrefix)) == $this->dbKeyPrefix ) ){
				if( $this->dbKeyPrefix )
					$t['template'] = substr( $t['template'], strlen($this->dbKeyPrefix) );
				
				$this->templates[ $lang ][ $t['template'] ] = array(
					'subject'	=> $t['subject'],
					'body'		=> $t['body'],
					);
				}
			}
		}

	function getSaveSql( $lang, $key, $subject, $body ){
		$ntsdb =& dbWrapper::getInstance();

		if( $lang == 'en-builtin' )
			$lang = 'en';

		$sql = "SELECT subject, body FROM {PRFX}templates WHERE template = '$key' AND lang = '$lang'";
		$result = $ntsdb->runQuery( $sql );
		$update = ( $oInfo = $result->fetch() ) ? true : false;

	/* update */
		if( $update ){
			$updateSql = $ntsdb->prepareUpdateStatement( array('subject' => $subject, 'body' => $body) );
			$sql = "UPDATE {PRFX}templates SET $updateSql WHERE template = '$key' AND lang = '$lang'";
			}
	/* insert */
		else {
			$insertSql = $ntsdb->prepareInsertStatement( array('template' => $key, 'lang' => $lang, 'subject' => $subject, 'body' => $body) );
			$sql = "INSERT INTO {PRFX}templates $insertSql";
			}
		return $sql;
		}

	function getResetSql( $lang, $key ){
		$ntsdb =& dbWrapper::getInstance();

		if( $lang == 'en-builtin' )
			$lang = 'en';

		$sql = "DELETE FROM {PRFX}templates WHERE template = '$key' AND lang = '$lang'";
		return $sql;
		}

	// Singleton stuff
	function &getInstance(){
		return ntsLib::singletonFunction( 'ntsEmailTemplateManager' );
		}
	}
?>