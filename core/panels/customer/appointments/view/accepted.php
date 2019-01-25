<?php
$lm =& ntsLanguageManager::getInstance();
$etm =& ntsEmailTemplateManager::getInstance();
$om =& objectMapper::getInstance();

$defaultLanguage = $lm->getDefaultLanguage();
$userLang = $defaultLanguage;

$key = 'appointment-approve-customer';
$templateInfo = $etm->getTemplate( $userLang, $key );

reset( $object );
?>
<?php foreach( $object as $obj ) : ?>
<?php
	$tags = $om->makeTags_Appointment( $obj, 'external' );
	$subject = str_replace( $tags[0], $tags[1], $templateInfo['subject'] );
	$body = str_replace( $tags[0], $tags[1], $templateInfo['body'] );
?>
	<p>
	<H2><?php echo nl2br( $subject ); ?></H2>

	<p>
	<?php echo nl2br( $body );
   ### Customized by RAH - Clear this sesion variable to allow additional appointment requests for other family members
	###	Without clearing this variable, subsequent requests assumed it was for the same person and skipped the demographics input screen
	unset($_SESSION['temp_customer_id']);
	?><?php endforeach; ?>
