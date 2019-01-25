<?php
if( ! defined('NTS_HEADER_SENT') ){
	if( isset($NTS_VIEW['headFile']) && $NTS_VIEW['headFile'] && file_exists($NTS_VIEW['headFile']) )
		require( $NTS_VIEW['headFile'] );
	else
		require( dirname(__FILE__) . '/head.php' );
	define( 'NTS_NEED_FOOTER', 1 );
	}
?>
<!-- HITAPPOINT ADMIN JAVASCRIPTS -->
<script language="JavaScript" src="<?php echo ntsLink::makeLink('system/pull', '', array('what' => 'js', 'files' => 'functions.js|jquery-1.4.2.min.js|jquery.simplemodal-1.3.4.min.js') ); ?>">
</script>

<!-- RELOAD IF NEEDED -->
<?php if( $url = ntsView::isReloadParent() ) : ?>
	<script language="JavaScript">
	<?php if( $url == 1 ) : ?>
		parent.location.reload();
	<?php else : ?>
		parent.location.href = "<?php echo $url; ?>";
	<?php endif; ?>
	</script>
<?php exit; ?>
<?php endif; ?>

<?php global $NTS_VIEW, $NTS_CURRENT_PANEL, $NTS_CURRENT_USER; ?>

<div id="nts">
<a href="#" class="simplemodal-close" onClick="window.parent.jQuery.modal.close(true);">X</a>

<!-- MENU  -->
<?php if( (! isset($NTS_VIEW['skipMenu']) ) && $NTS_VIEW['subtitle'] ) : ?>
	<h2><?php echo $NTS_VIEW['subtitle']; ?></h2>
<?php endif; ?>

<?php if( (! isset($NTS_VIEW['skipMenu']) ) && $NTS_VIEW['menu2'] ) : ?>
	<ul id="nts-menu2">
	<?php foreach( $NTS_VIEW['menu2'] as $m ) : ?>
		<?php 
			if( isset($m['directLink']) )
				$link = $m['directLink'];
			else
				$link = ntsLink::makeLink($m['panel'], '', $m['params']);
		?>
		<li><a<?php if ($m['panel'] == substr($NTS_CURRENT_PANEL, 0, strlen($m['panel'])) ){ echo ' class="current"'; } ?> href="<?php echo $link; ?>"><?php echo $m['title']; ?></a></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if( ntsView::isAnnounce() ) : ?>
	<ul id="nts-announce">
	<?php $text = ntsView::getAnnounceText();	?>
	<?php foreach( $text as $t ) : ?>
	<?php if( $t[1] == 'error' ) : ?>
		<li class="error">
	<?php else : ?>
		<li>
	<?php endif; ?>
		<?php echo $t[0]; ?>
		</li>
	<?php endforeach; ?>
	</ul>
	<?php ntsView::clearAnnounce(); ?>
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