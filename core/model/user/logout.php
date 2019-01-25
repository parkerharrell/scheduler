<?php
if( isset($_SESSION['nts_sos_user_id']) )
	unset($_SESSION['nts_sos_user_id']);

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();
$integrator->logout();
?>