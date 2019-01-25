<?php
global $NTS_VIEW;
?>
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
			$linkClass = ( $m['ajax'] ) ? 'nts-ajax-link' : '';
		?>
		<li><a class="<?php echo $linkClass; ?><?php if ($m['panel'] == substr($NTS_CURRENT_PANEL, 0, strlen($m['panel'])) ){ echo ' current'; } ?>" href="<?php echo $link; ?>"><?php echo $m['title']; ?></a></li>
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
<?php
if( file_exists($NTS_VIEW['displayFile']) )
	require( $NTS_VIEW['displayFile'] );
?>