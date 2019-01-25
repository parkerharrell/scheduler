<?php
// generate random code for superadmin access
include_once( NTS_BASE_DIR . '/lib/crypt/ntsRandomGenerator.php' );
$gen = new ntsRandomGenerator;
$sosCode = $gen->generate(8);
$now = time();

$sosSetting = $sosCode . ':' . $now;
$conf =& ntsConf::getInstance();
$conf->save( 'sosCode', $sosSetting );

// send to hitAppoint support
include_once( NTS_BASE_DIR . '/lib/email/ntsEmail.php' );
$email = 'support@hitcode.com';
$mailer = new ntsEmail;
$mailer->setSubject( 'hitcode SOS Code: ' . NTS_ROOT_WEBDIR );

$url = NTS_ROOT_WEBDIR . '/?nts-sos=' . $sosCode;
$body = "<a href=\"$url\">$url</a>";
$mailer->setBody( $body );
$mailer->sendToOne( $email );
?>