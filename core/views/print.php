<?php
if( ! defined('NTS_HEADER_SENT') ){
	if( isset($NTS_VIEW['headFile']) && $NTS_VIEW['headFile'] && file_exists($NTS_VIEW['headFile']) )
		require( $NTS_VIEW['headFile'] );
	else
		require( dirname(__FILE__) . '/head.php' );
	define( 'NTS_NEED_FOOTER', 1 );
	}
?>

<?php global $NTS_VIEW, $NTS_CURRENT_PANEL, $NTS_CURRENT_USER; ?>
<!-- HEADER -->
<?php if( isset($NTS_VIEW['headerFile']) && file_exists($NTS_VIEW['headerFile']) ) : ?>
	<?php require( $NTS_VIEW['headerFile'] ); ?>
<?php endif; ?>

<div id="nts">
<?php if( $NTS_VIEW['subtitle'] ) : ?>
	<h2><?php echo $NTS_VIEW['subtitle']; ?></h2>
<?php endif; ?>

<!-- DISPLAY PAGE -->
<p>
<?php
if( file_exists($NTS_VIEW['displayFile']) )
	require( $NTS_VIEW['displayFile'] );
?>
</div>

<?php
if( defined('NTS_NEED_FOOTER') )
	require( dirname(__FILE__) . '/footer.php' );
?>