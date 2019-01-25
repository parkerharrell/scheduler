<?php
$apn =& ntsAdminPermissionsManager::getInstance();

switch( $inputAction ){
	case 'display':
		$keys = $apn->getPanelsDetailed();
?>
<?php foreach( $keys as $ka ) : ?>
<?php	if( isset($ka[2]) && $ka[2] ) : ?>
		<h3><?php echo $ka[1]; ?></h3>
<?php	else : ?>
<?php
			$thisValue = in_array($ka[0], $conf['value']) ? 0 : 1;
			echo $this->makeInput(
				'checkbox',
				array(
					'id'	=> $conf['id'] . '-' . $ka[0],
					'value'	=> $thisValue,
					)
				);
?>
<?php		echo $ka[1]; ?>&nbsp;
<?php	endif; ?>
<?php endforeach; ?>
<?php
		break;

	case 'submit':
		$input = array();
		$keys = $apn->getPanels();
		foreach( $keys as $k ){
			$isAllowed = $req->getParam( $handle . '-' . $k );
			if( ! $isAllowed )
				$input[] = $k;
			}
		break;

	case 'check_submit':
		$input = true;
		break;
	}
?>