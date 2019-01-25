<?php if( NTS_CURRENT_USERID ) : ?>
	<?php if( $NTS_CURRENT_USER->hasRole('superadmin' ) ) : ?>
		<?php require( dirname(__FILE__) . '/user-info-superadmin.php' ); ?>
	<?php elseif( $NTS_CURRENT_USER->hasRole('admin') ) : ?>
		<?php require( dirname(__FILE__) . '/user-info-admin.php' ); ?>
	<?php elseif( $NTS_CURRENT_USER->hasRole('customer') ) : ?>
		<?php require( dirname(__FILE__) . '/user-info-customer.php' ); ?>
	<?php endif; ?>
<?php else: ?>
	<?php require( dirname(__FILE__) . '/user-info-anon.php' ); ?>
<?php endif; ?>