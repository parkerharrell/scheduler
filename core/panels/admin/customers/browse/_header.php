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
		continue;
		}
	if( $fields[$i][0] == 'last_name' ){
		array_splice( $fields, $i, 1 );
		continue;
		}

	// remove email
	if( ! NTS_EMAIL_AS_USERNAME ){
		if( $fields[$i][0] == 'email' ){
			array_splice( $fields, $i, 1 );
			continue;
			}
		}
	}

/* status options */
$fields[] = array('nts_user_status',	M('Status') );

$values = array();
$values[ '-any-' ] = M('- Any -');
$values[ 'email_not_confirmed' ] = M('Email Not Confirmed');
$values[ 'not_approved' ] = M('Not Approved');
$values[ 'suspended' ] = M('Suspended');

$fieldNames = array();
reset( $fields );

foreach( $fields as $f ){
	$fieldNames[ $f[0] ] = $f[1];
	}
$fieldNames['last_name'] = M('Last Name');
$fieldNames['first_name'] = M('First Name');
?>

<?php if( ($NTS_VIEW['action'] == 'search') && $NTS_VIEW['searchParams'] ) : ?>
	<b><?php echo M('Search'); ?></b>: 
	<?php foreach( $NTS_VIEW['searchParams'] as $k => $v ) : ?>
		<?php echo isset($fieldNames[$k]) ? $fieldNames[$k] : $k; ?>: <b><?php echo isset($values[$v]) ? $values[$v] : $v; ?></b> 
	<?php endforeach; ?>
<?php else : ?>
<?php endif; ?>

<?php if( ($NTS_VIEW['action'] == 'search') && (! $NTS_VIEW['entries'] ) ) : ?>
	<p>
	<?php echo M('No users found matching your search'); ?>
	<a href="<?php echo ntsLink::makeLink('-current-/search' ); ?>"><?php echo M('Revise Search?'); ?></a>
	<?php $skipList = true; ?>
<?php endif; ?>