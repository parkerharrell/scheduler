<?php
/* custom fields */
$om =& objectMapper::getInstance();
$fields = $om->getFields( 'customer', 'internal', true );

// replace first and last name by full name
$fieldsCount = count( $fields );
for( $i = ($fieldsCount - 1); $i >= 0; $i-- ){
	if( $fields[$i][0] == 'first_name' ){
		$fields[$i][0] = 'full_name';
		$fields[$i][1] = M('Full Name');
		}
	if( $fields[$i][0] == 'last_name' ){
		array_splice( $fields, $i, 1 );
		}
	}

/* status options */
$fields[] = array('nts_user_status',	M('User Status') );

reset( $fields );
?>
<?php

$headers = array();
foreach( $fields as $f )
	$headers[] = $f[1];
echo ntsLib::buildCsv( $headers );
echo "\n";

foreach( $NTS_VIEW['entries'] as $e ){
	$obj = new ntsUser();
	$obj->setByArray( $e );
	$restrictions = $obj->getProp('_restriction');

	$values = array();
	reset( $fields );
	foreach( $fields as $f ){
		switch( $f[0] ){
			case 'full_name':
				$value = $obj->getProp('first_name') . ' ' . $obj->getProp('last_name');
				break;

			case 'nts_user_status':
				if( $restrictions ){
					$statusOk = false;
					if( in_array('email_not_confirmed', $restrictions) )
						$value = M('Email Not Confirmed');
					elseif( in_array('not_approved', $restrictions) )
						$value = M('Not Approved');
					elseif( in_array('suspended', $restrictions) )
						$value = M('Suspended');
					else
						$value = M('N/A');
					}
				else {
					$value = M('Active');
					}
				break;

			default:
				$value = $obj->getProp( $f[0] );
				break;
			}
		$values[] = $value;
		}
	echo ntsLib::buildCsv( $values );
	echo "\n";
	}
?>