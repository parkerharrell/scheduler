<ul id="nts-user-menu">
<?php ### Customized by RAH 5/25/11 - comment out echo so Home link doesn't go anywhere ?>
<li><a href="<?php //echo ntsLink::makeLink(); ?>"><?php echo M('Home'); ?></a></li>
<?php if( $NTS_CURRENT_PANEL != 'anon/login') : ?>
	<li><a href="<?php echo ntsLink::makeLink('anon/login'); ?>"><?php echo M('Login'); ?></a></li>
<?php endif; ?>
<?php if( $NTS_CURRENT_PANEL != 'anon/register') : ?>
	<?php if( NTS_ENABLE_REGISTRATION ) : ?>
		<li><a href="<?php echo ntsLink::makeLink('anon/register'); ?>"><?php echo M('Register'); ?></a></li>
	<?php endif; ?>
<?php endif; ?>
</ul>