<?php require_once( dirname(__FILE__) . '/../common/grab.php' ); ?>
<?php
$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$registerForm =& $ff->makeForm( $formFile );
?>

<!-- APPOINTMENT REQUEST FLOW -->
<?php require( dirname(__FILE__) . '/../common/flow.php' ); ?>

<p>
<?php echo M('Already have an account?'); ?> <a href="<?php echo ntsLink::makeLink('-current-/../login'); ?>"><?php echo M('Please login'); ?></a>

<p>
<h2><?php echo M('Please provide your contact details'); ?></h2>

<p>
<?php $registerForm->display(); ?>