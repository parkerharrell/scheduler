<?php require_once( dirname(__FILE__) . '/../common/grab.php' ); ?>
<?php
$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$loginForm =& $ff->makeForm( $formFile );
?>

<!-- APPOINTMENT REQUEST FLOW -->
<?php require( dirname(__FILE__) . '/../common/flow.php' ); ?>

<p>
<?php echo M("Don't have an account yet?"); ?> <a href="<?php echo ntsLink::makeLink('-current-/../register'); ?>"><?php echo M('Please register'); ?></a>

<p>
<h2><?php echo M('Please login'); ?></h2>

<p>
<?php $loginForm->display(); ?>