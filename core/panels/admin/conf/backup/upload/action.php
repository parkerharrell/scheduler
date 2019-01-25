<?php
$conf =& ntsConf::getInstance();
$ntsdb =& dbWrapper::getInstance();

switch( $action ){
	case 'upload':
		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate($req) ){
			$formValues = $form->getValues();
			if( ! $formValues['file']['error'] ){

				$tables = $ntsdb->getTablesInDatabase();
				reset( $tables );
				foreach( $tables as $t ){
					$sql = "DROP TABLE {PRFX}$t";
					$ntsdb->runQuery( $sql );
					}

				$fullFileName = $formValues['file']['tmp_name'];
				$lines = file( $fullFileName );
				reset( $lines );
				$count = 0;
				foreach( $lines as $line ){
					$line = trim( $line );
					if( ! $line )
						continue;
					$ntsdb->runQuery( $line );
					$count++;
					}
				ntsView::setAnnounce( M('Restore') . ': ' . "$count database queries". ': ' . M('OK'), 'ok' );
				}
			else {
				ntsView::setAnnounce( 'Upload Error', 'error' );
				}
			}
		else {
		/* form not valid, continue to create form */
			}
		break;
	
	default:
		break;
	}
?>