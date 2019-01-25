<?php
function _print_r( $thing ){
	echo '<pre>';
	print_r( $thing );
	echo '</pre>';
	}

function ntsJoinArray( $array ){
	reset( $array );
	foreach( $array as $k => $v ){
		$returnStrings[] = '';
		}
	}

function ntsExplodeArray( $string ){
	}

class ntsHttpClient {
	var $error;
	function ntsHttpClient(){
		$this->setError( '' );
		}

	function get( $url2get ){
		$timeout = 3;
		$old = ini_set('default_socket_timeout', $timeout);

		ob_start();
		if(intval(get_cfg_var('allow_url_fopen')) && function_exists('readfile')){
//			if( ! ($return = @file($url2get)) ){
//				$this->setError( $php_errormsg );
//				}
			if( ! @readfile($url2get) ){
				$this->setError( $php_errormsg );
				}
			}
		elseif(function_exists('curl_init')) {
			$ch = curl_init( $url2get );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_exec( $ch );
			if( $error = curl_error($ch)){
				$this->setError( $error );
				}
			curl_close ($ch);
			}
		else {
			$error = "outside connections are not allowed";
			$this->setError( $error );
			}
		$return = ob_get_contents();
		ob_end_clean();

		ini_set('default_socket_timeout', $old);		
		return $return;
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

class ntsLib{
	function log( $msg ){
		$outFile = realpath( NTS_APP_DIR . '/../halog.txt' );
		$date = date( "F j, Y, g:i a", time() );
		$fp = fopen( $outFile, 'a' );
		fwrite( $fp, $date . "\n" . $msg . "\n" );
		fclose($fp);
		}

	function fileInCoreDirs( $file ){
		global $NTS_CORE_DIRS;
		$return = '';
		reset( $NTS_CORE_DIRS );
		foreach( $NTS_CORE_DIRS as $d ){
			$finalFile = $d . '/' . $file;
			if( file_exists($finalFile) ){
				$return = $finalFile;
				break;
				}
			}
		return $return;
		}

	function requireMenuFile( $menuFile ){
		global $req, $NTS_VIEW, $NTS_CURRENT_USER;
		static $MENU_FILES;
		if( ! isset($MENU_FILES[$menuFile]) ){
			$title = '';
			$sequence = 100;
			$params = array();
			$directLink = '';
			$ajax = false;

			require( $menuFile );

			$MENU_FILES[$menuFile] = array( $title, $sequence, $params, $directLink, $ajax );
			}
		return $MENU_FILES[$menuFile];
		}

	function requireSubheaderFile( $subheaderFile ){
		global $req, $NTS_VIEW, $NTS_CURRENT_USER;
		static $SUBHEADER_FILES;
		if( ! isset($SUBHEADER_FILES[$subheaderFile]) ){
			$title = '';

			require( $subheaderFile );

			$SUBHEADER_FILES[$subheaderFile] = array( $title );
			}
		return $SUBHEADER_FILES[$subheaderFile];
		}

	function parseVersionNumber( $string ){
		list( $v1, $v2, $v3 ) = explode( '.', $string );
		$return = $v1 . $v2 . sprintf('%02d', $v3 );
		return $return;
		}

	function getServicePrice( $service, $seats ){
		$price = $service->getProp( 'price' );

		if( ! strlen($price) ){
			$return = '';
			}
		else {
			$return = $seats * $price;
			}
		return $return;
		}

	function processGroupAppointments( $srcArray ){
		$finalEntries = array();
		$finalIndex = 0;
		$finalMatrix = array();
		$sourceCount = count($srcArray);

		for( $sourceIndex = 0; $sourceIndex < $sourceCount; $sourceIndex++ ){
			$app = $srcArray[$sourceIndex];
			$serviceId = $app->getProp('service_id');
			$startsAt = $app->getProp('starts_at');
			$locationId = $app->getProp('location_id');
			$resourceId = $app->getProp('resource_id');

			$service = ntsObjectFactory::get( 'service' );
			$service->setId( $serviceId );

			$appIndex = $serviceId . '-' . $locationId . '-' . $resourceId . '-' . $startsAt;
			$classType = $service->getProp( 'class_type' );
			if( isset($finalMatrix[$appIndex]) ){
				if( ! is_array($finalEntries[$finalMatrix[$appIndex]]) )
					$finalEntries[$finalMatrix[$appIndex]] = array( $finalEntries[$finalMatrix[$appIndex]] );
				$finalEntries[ $finalMatrix[$appIndex] ][] = $sourceIndex;
				}
			else {
				if( $classType )
					$finalEntries[ $finalIndex ] = array( $sourceIndex );
				else 
					$finalEntries[ $finalIndex ] = $sourceIndex;

				$finalMatrix[ $appIndex ] = $finalIndex;
				$finalIndex++;
				}
			}
		return $finalEntries;
		}

	/*
	return array( arrDiscontedPrices, arrFullPrices, totalDiscountedPrice, totalFullPrice )
	*/
	function getPackPrice( $currentRequest, $pack = null ){
		$returnDiscountedPrices = array();
		$returnFullPrices = array();

		$totalFullPrice = '';
		$totalDiscountedPrice = '';

		$rowsCount = count( $currentRequest );
		for( $i = 0; $i < $rowsCount; $i++ ){
			if( $currentRequest[$i]['service'] && $currentRequest[$i]['seats'] ){
				$thisPrice = ntsLib::getServicePrice( $currentRequest[$i]['service'], $currentRequest[$i]['seats'] );
				}
			else {
				$thisPrice = '';
				}

			$returnDiscountedPrices[] = $thisPrice;
			$returnFullPrices[] = $thisPrice;

			if( strlen($thisPrice) ){
				if( ! strlen($totalDiscountedPrice) )
					$totalDiscountedPrice = 0;
				if( ! strlen($totalFullPrice) )
					$totalFullPrice = 0;

				$totalFullPrice += $thisPrice;
				$totalDiscountedPrice += $thisPrice;
				}
			}

		if( $pack ){
			$rawValue = $pack->getProp( 'discount' );
			$packValue = array();
			if( preg_match('/discount/', $rawValue)){
				$packValue = explode( ':', $rawValue );
				}
			elseif( preg_match('/price/', $rawValue)){
				$packValue = explode( ':', $rawValue );
				}
			else{
				$packValue = array( $rawValue, '' );
				}

			switch( $packValue[0] ){
				case 'onefree':
					$minPrice = $returnFullPrices[0];
					
					$zeroPriceId = -1;
					for( $i = 0; $i < $rowsCount; $i++ ){
						// skip if not all yet configured
						if( ! ($currentRequest[$i]['service'] && $currentRequest[$i]['seats']) ){
							$minPrice = 0;
							$zeroPriceId = -1;
							break;
							}

						$thisPrice = $returnFullPrices[$i];
						if( strlen($thisPrice) && ( $thisPrice <= $minPrice ) ){
							$minPrice = $thisPrice;
							$zeroPriceId = $i;
							}
						}

					if( $zeroPriceId >= 0 ){
						$returnDiscountedPrices[$zeroPriceId] = 0;
						$totalDiscountedPrice = $totalFullPrice - $minPrice;
						}
					break;

				case 'price':
					if( $currentRequest[0]['seats'] > 1 )
						$totalDiscountedPrice = $currentRequest[0]['seats'] * $packValue[1];
					else
						$totalDiscountedPrice = $packValue[1];
					break;

				case 'discount':
					if( $packValue[1] ){
						if( strlen($totalFullPrice) )
							$totalDiscountedPrice = ( 1 - ($packValue[1]/100) ) * $totalFullPrice;
						}
					break;
				}

			if( strlen($totalDiscountedPrice) ){
				$totalDiscountedPrice = sprintf("%01.2f", $totalDiscountedPrice );
				}

		// build individual prices
			$tempTotal = 0;
			for( $i = 0; $i < $rowsCount; $i++ ){
				switch( $packValue[0] ){
					case 'onefree':
						$returnDiscountedPrices[$zeroPriceId] = 0;
						break;

					case 'price':
						if( strlen($totalDiscountedPrice) && strlen($returnDiscountedPrices[$i]) ){
							if( $i == ($rowsCount - 1) ){
								$returnDiscountedPrices[$i] = $totalDiscountedPrice - $tempTotal;
								}
							else {
								if( $totalFullPrice ){
									$returnDiscountedPrices[$i] = ($returnFullPrices[$i] * $totalDiscountedPrice) / $totalFullPrice;
									}
								}
							$tempTotal += $returnDiscountedPrices[$i];
							}
						else {
							$returnDiscountedPrices[$i] = '';
							}
						break;

					case 'discount':
						if( strlen($totalDiscountedPrice) && strlen($returnDiscountedPrices[$i]) ){
							if( $i == ($rowsCount - 1) ){
								$returnDiscountedPrices[$i] = $totalDiscountedPrice - $tempTotal;
								}
							else {
								if( $packValue[1] ){
									if( strlen($returnFullPrices[$i]) )
										$returnDiscountedPrices[$i] = ( 1 - ($packValue[1]/100) ) * $returnFullPrices[$i];
									}
								}
							$tempTotal += $returnDiscountedPrices[$i];
							}
						else {
							$returnDiscountedPrices[$i] = '';
							}
						break;
					}

				if( strlen($returnDiscountedPrices[$i]) ){
					$returnDiscountedPrices[$i] = sprintf("%01.2f", $returnDiscountedPrices[$i]);
					}
				if( strlen($returnFullPrices[$i]) ){
					$returnFullPrices[$i] = sprintf("%01.2f", $returnFullPrices[$i]);
					}
				}
			}
		return array( $returnDiscountedPrices, $returnFullPrices, $totalDiscountedPrice, $totalFullPrice );
		}

	function splitPackServicesString( $servicesString ){
		$return = array();
		$servicesArray = explode( '|', $servicesString );
		reset( $servicesArray );
		foreach( $servicesArray as $s ){
			$s = trim( $s );
			$thisArray = ( $s ) ? explode( '-', $s ) : array();
			if( $thisArray )
				$return[] = $thisArray;
			else
				break;
			}
		return $return;
		}

	function makePackSessionsString( $packsArray ){
		$totalArray = array();
		reset( $packsArray );
		foreach( $packsArray as $pa ){
			$totalArray[] = join( '-', $pa );
			}
		$string = join( '|', $totalArray );
		return $string;
		}

	function allPackServices( $servicesString ){
		$return = array();
		$services = ntsLib::splitPackServicesString( $servicesString );
		foreach( $services as $sa ){
			$return = array_merge( $return, $sa );
			}
		$return = array_unique( $return );
		return $return;
		}

	function buildCsv( $array ){
		$conf =& ntsConf::getInstance();
		$csvDelimiter = $conf->get('csvDelimiter');

		$processedArray = array();
		reset( $array );
		foreach( $array as $a ){
			if( strpos($a, '"') !== false ){
				$a = str_replace( '"', '""', $a );
				}

			if( strpos($a, $csvDelimiter) !== false ){
				$a = '"' . $a . '"';
				}
			$processedArray[] = $a;
			}

		$return = join( $csvDelimiter, $processedArray );
		return $return;
		}

	function pickRandom( $array, $many = 1 ){
		if( $many > 1 ){
			$return = array();
			$ids = array_rand($array, $many );
			foreach( $ids as $id )
				$return[] = $array[$id];
			}
		else {
			$id = array_rand($array);
			$return = $array[$id];
			}
		return $return;
		}

	function sessionGet( $k ){
		$return = isset($_SESSION[$k]) ? $_SESSION[$k] : null;
		return $return;
		}

	function sessionSet( $k, $v ){
		$_SESSION[ $k ] = $v;
		}

	function currentPageUrl(){
		$pageURL = 'http';
		if( isset($_SERVER['HTTPS']) && ( $_SERVER['HTTPS'] == 'on' ) ){
			$pageURL .= 's';
			}
		$pageURL .= "://";
		if( isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80'){
			$pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
			}
		else {
			$pageURL .= $_SERVER['SERVER_NAME'];
			}

		if ( ! empty($_SERVER['REQUEST_URI']) )
			$pageURL .= $_SERVER['REQUEST_URI'];
		else
			$pageURL .= $_SERVER['SCRIPT_NAME'];
		return $pageURL;
		}

	function pureUrl( $url ){
		preg_match( "/(.+)\?.*$/", $url, $matches );
		if( isset($matches[ 1 ]) ) 
			$url = $matches[ 1 ];
		return $url;
		}

	function urlParamsPart( $url ){
		preg_match( "/(.+)(\?.*)$/", $url, $matches );
		if( isset($matches[ 2 ]) ) 
			$url = $matches[ 2 ];
		return $url;
		}

	function webDirName( $fullWebPage ){
		preg_match( "/(.+)\/.*$/", $fullWebPage, $matches );
		if ( isset($matches[1]) )
			$webDir = $matches[1];
		else
			$webDir = '';
		return $webDir;
		}

	function pushDownload( $localFileName, $pushName ){
		if( ob_get_contents() )
			ob_end_clean();
		$fileSize = filesize( $localFileName );

		header("Type: application/force-download");
		header("Content-Type: application/force-download");
		header("Content-Length: $fileSize");

		header("Content-Transfer-Encoding: binary");
		header("Content-Disposition: attachment; filename=\"$pushName\"");

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Connection: close");

		readfile( $localFileName );
		exit;
		}

	function pushDownloadContent( $content, $pushName, $contentType = 'application/force-download' ){
		if( ob_get_contents() )
			ob_end_clean();
		$fileSize = strlen( $content );

		header("Type: $contentType");
		header("Content-Type: $contentType");
		header("Content-Length: $fileSize");

		header("Content-Transfer-Encoding: binary");
		header("Content-Disposition: attachment; filename=\"$pushName\"");

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Connection: close");

		readfile( $localFileName );
		exit;
		}

	function startPushDownloadContent( $pushName ){
		if( ob_get_contents() )
			ob_end_clean();
//		$fileSize = strlen( $content );

		header("Type: application/force-download");
		header("Content-Type: application/force-download");
//		header("Content-Length: $fileSize");

		header("Content-Transfer-Encoding: binary");
		header("Content-Disposition: attachment; filename=\"$pushName\"");

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Connection: close");
		}

	function filesizeReadable($size, $max = null, $system = 'si', $retstring = '%01.2f %s'){
		// Pick units
		$systems['si']['prefix'] = array('B', 'K', 'MB', 'GB', 'TB', 'PB');
		$systems['si']['size']   = 1000;
		$systems['bi']['prefix'] = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
		$systems['bi']['size']   = 1024;
		$sys = isset($systems[$system]) ? $systems[$system] : $systems['si'];

		// Max unit to display
		$depth = count($sys['prefix']) - 1;
		if ($max && false !== $d = array_search($max, $sys['prefix'])){
			$depth = $d;
			}

		// Loop
		$i = 0;
		while ($size >= $sys['size'] && $i < $depth) {
			$size /= $sys['size'];
			$i++;
			}
		return sprintf($retstring, $size, $sys['prefix'][$i]);
		}

	function ntsDirname( $path ){
		$myDirname = dirname( $path );
		if( in_array($myDirname, array('', "\\", "/")) )
			$myDirname = '/';
		return $myDirname;
		}
	function isRootDir( $path ){
		if( in_array($path, array('', "\\", "/")) )
			return true;
		else
			return false;
		}

	// A generic function to create and fetch static objects
	function &singletonFunction( $class, $fileName = '' ) {
	    // Declare a static variable to hold the object instance
	    static $instances;

	    // If the instance is not there, create one
	    if( ! isset($instances[$class]) ){
	    	if( ! class_exists($class) ){
				echo "cannot create '$class' object!";
				return null;
	   			}
			$instances[$class] = new $class;
	    	}
	    return $instances[$class];
		}

	function getUniqueID(){
		static $id = 0;
		$id++;
		return $id;
		}

	function fileGetContents( $fileName ){
		$content = join( '', file($fileName) );
		return $content;
		}

	function fileGetFirstLine( $fileName ){
		$line = array_shift( file($fileName) );
		return $line;
		}

	function fileSetContents( $fileName, $content ){
		$length = strlen( $content );
		$return = 1;

		if(! $fh = fopen($fileName, 'w') ){
			echo "can't open file <B>$fileName</B> for wrinting.";
			exit;
			}
		rewind( $fh );
		$writeResult = fwrite($fh, $content, $length);
		if( $writeResult === FALSE )
			$return = 0;

		return $return;
		}

	function numberCompare( $a, $b ){
		if( $a > $b )
			return 1;
		elseif( $a < $b )
			return -1;
		else
			return 0;
		}

	function stringCompare( $a, $b ){
		return strcmp( strtolower($a), strtolower($b) );
		}

	function getParentPath( $path ){
		if( substr($path, -1) == '/' )
			$path = substr($path, 0, -1);
		if( preg_match("/^(.+)\/.+$/", $path, $ma) )
			$parent = $ma[1];
		else
			$parent = '';
		return $parent;
		}

	function listSubfolders( $dirName ){
		if( ! is_array($dirName) )
			$dirName = array( $dirName );

		$return = array();
		reset( $dirName );
		foreach( $dirName as $thisDirName ){
			if ( file_exists($thisDirName) && ($handle = opendir($thisDirName)) ){
				while ( false !== ($f = readdir($handle)) ){
					if( substr($f, 0, 1) == '.' )
						continue;
					if( is_dir( $thisDirName . '/' . $f ) ){
						if( ! in_array($f, $return) )
							$return[] = $f;
						}
					}
				closedir($handle);
				}
			}

		sort( $return );
		return $return;
		}

	function listFiles( $dirName, $extension = '' ){
		if( ! is_array($dirName) )
			$dirName = array( $dirName );

		$files = array();
		foreach( $dirName as $thisDirName ){
	
			if ( file_exists($thisDirName) && ($handle = opendir($thisDirName)) ){
				while ( false !== ($f = readdir($handle)) ){
					if( substr($f, 0, 1) == '.' )
						continue;

					if( is_file( $thisDirName . '/' . $f ) ){
						if( (! $extension ) || ( substr($f, - strlen($extension)) == $extension ) )
							$files[] = $f;
						}
					}
				closedir($handle);
				}
			}

		sort( $files );
		return $files;
		}

	function utime() {
		$time = explode( ' ', microtime() );
		$usec = (double)$time[0];
		$sec = (double)$time[1];
		$return = $sec + $usec;
		return $return;
		}

	function getCurrentExecutionTime(){
		global $NTS_EXECUTION_START;
		$return = ntsLib::utime() - $NTS_EXECUTION_START;
		return $return;
		}
	
	function printCurrentExecutionTime(){
		printf("done in %.2f sec", ntsLib::getCurrentExecutionTime() );
		}

	function differenceInDays( $startTimestamp, $endTimestamp ){
		$inTimeRawDiff = ( abs($endTimestamp - $startTimestamp) + 2*60*60)/(24*60*60); // 2 hours to get over daylight
		$inTimeDays = floor( $inTimeRawDiff );
		return $inTimeDays;
		}

	function compareInDays( $endTimestamp, $startTimestamp ){
		$diff = ntsLib::differenceInDays( $endTimestamp, $startTimestamp );
		if( $endTimestamp > $startTimestamp )
			$return = $diff;
		else
			$return = - $diff;
		return $return;
		}

	function csvFile( $fileName, $sep = ';', $titles = true ){
		$return = array();
		$lines = file( $fileName );

		if( $titles ){
		/* first line with titles */
			$line = array_shift( $lines );
			$line = trim( $line );
			$line = strtolower( $line );
			$propNames = explode( ';', $line );
			$propCount = count( $propNames );
			}

		$count = 0;
		$created = 0;
		reset( $lines );
		foreach( $lines as $line ){
			$line = trim( $line );
			if( ! $line )
				continue;

			$count++;
			$rawValues = explode( ';', $line );
			if( $titles ){
				for( $i = 0; $i < $propCount; $i++ ){
					if( ! isset($rawValues[$i]) )
						$rawValues[$i] = '';
					$values[ $propNames[$i] ] = $rawValues[$i];
					}
				$return[] = $values;
				}
			else {
				$return[] = $rawValues;
				}
			}
		return $return;
		}

	function sanitizeTitle($title) {
		$title = strip_tags($title);
		// Preserve escaped octets.
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
		// Remove percent signs that are not part of an octet.
		$title = str_replace('%', '', $title);
		// Restore octets.
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

		$title = ntsLib::removeAccents($title);
		if (ntsLib::seemsUtf8($title)) {
			if (function_exists('mb_strtolower')) {
				$title = mb_strtolower($title, 'UTF-8');
				}
			$title = ntsLib::utf8UriEncode($title, 200);
			}

		$title = strtolower($title);

//		$title = preg_replace('/&.+?;/', '', $title); // kill entities
		$title = str_replace('.', '-', $title);
		$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
		$title = preg_replace('/\s+/', '-', $title);
		$title = preg_replace('|-+|', '-', $title);
		$title = trim($title, '-');

		return $title;
		}

	function sanitizeFileName( $filename ) {
		$filename_raw = $filename;
		$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
		$filename = str_replace($special_chars, '', $filename);
		$filename = preg_replace('/[\s-]+/', '-', $filename);
		$filename = trim($filename, '.-_');
		return $filename;
		}

	function sanitizeSqlName( $filename ) {
		$filename = ntsLib::sanitizeFileName( $filename );
		$filename = str_replace('-', '_', $filename);
		return $filename;
		}

	function seems_utf8($str) {
		$length = strlen($str);
		for ($i=0; $i < $length; $i++) {
			$c = ord($str[$i]);
			if ($c < 0x80) $n = 0; # 0bbbbbbb
			elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
			elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
			elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
			else return false; # Does not match any model
			for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
				}
			}
		return true;
		}

	function removeAccents($string) {
		if ( !preg_match('/[\x80-\xff]/', $string) )
			return $string;

		if (ntsLib::seems_utf8($string)) {
			$chars = array(
			// Decompositions for Latin-1 Supplement
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
			chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
			chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
			chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
			chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
			chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
			chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
			chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
			chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
			chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
			chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
			chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
			chr(195).chr(191) => 'y',
			// Decompositions for Latin Extended-A
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
			// Euro Sign
			chr(226).chr(130).chr(172) => 'E',
			// GBP (Pound) Sign
			chr(194).chr(163) => '');

			$string = strtr($string, $chars);
			} 
		else {
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
				.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
				.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
				.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
				.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
				.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
				.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
				.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
				.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
				.chr(252).chr(253).chr(255);

			$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

			$string = strtr($string, $chars['in'], $chars['out']);
			$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
			$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
			}
		return $string;
		}

	function seemsUtf8($str) {
		$length = strlen($str);
		for ($i=0; $i < $length; $i++) {
			$c = ord($str[$i]);
			if ($c < 0x80) $n = 0; # 0bbbbbbb
			elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
			elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
			elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
			else return false; # Does not match any model
			for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
				}
			}
		return true;
		}

	function utf8UriEncode( $utf8_string, $length = 0 ) {
		$unicode = '';
		$values = array();
		$num_octets = 1;
		$unicode_length = 0;

		$string_length = strlen( $utf8_string );
		for ($i = 0; $i < $string_length; $i++ ) {
			$value = ord( $utf8_string[ $i ] );
			if ( $value < 128 ) {
				if ( $length && ( $unicode_length >= $length ) )
					break;
				$unicode .= chr($value);
				$unicode_length++;
				} 
			else {
				if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;

				$values[] = $value;

				if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
					break;
				if ( count( $values ) == $num_octets ) {
					if ($num_octets == 3) {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
						$unicode_length += 9;
					} else {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
						$unicode_length += 6;
					}

					$values = array();
					$num_octets = 1;
					}
				}
			}
		return $unicode;
		}

	function getRemoteUrl( $url2get ){
		ob_start();
		if( intval(get_cfg_var('allow_url_fopen')) && function_exists('readfile') ){
			readfile( $url2get );
			}
		elseif(intval(get_cfg_var('allow_url_fopen')) && function_exists('file')){
			if( $content = @file($url2get) )
				print @join('', $content);
			}
		elseif(function_exists('curl_init')) {
			$ch = curl_init( $url2get );
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_exec ($ch);

			if(curl_error($ch))
				print "Error processing request";

			curl_close ($ch);
			}
		else {
			print "It appears that your web host has disabled all functions for handling remote pages and as a result the BackLinks software will not function on your web page. Please contact your web host for more information.";
			}

		$code = ob_get_contents();
		ob_end_clean();
		echo $code;
		}
	}
?>
