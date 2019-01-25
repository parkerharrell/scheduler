<ul id="nts-user-menu">
<?php
$profilePanel = 'customer/profile';
?>
<li><?php echo M('Welcome'); ?> <b><a href="<?php echo ntsLink::makeLink($profilePanel); ?>"><?php echo $NTS_CURRENT_USER->getProp('first_name'); ?> <?php echo $NTS_CURRENT_USER->getProp('last_name'); ?></a></b></li>

<li><a href="<?php echo ntsLink::makeLink('customer'); ?>"><?php echo M('New Appointment'); ?></a></li>
<li><a href="<?php echo ntsLink::makeLink('customer/appointments/browse'); ?>"><?php echo M('My Appointments'); ?></a></li>

<?php if( file_exists(NTS_EXTENSIONS_DIR . '/more-links-customer.php') ) : ?>
<?php	require(NTS_EXTENSIONS_DIR . '/more-links-customer.php'); ?>
<?php endif; ?>

<li><a href="<?php echo ntsLink::makeLink('user/logout'); ?>"><?php echo M('Logout'); ?></a></li>
</ul>