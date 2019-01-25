<?php
if( isset($_SESSION['reload_parent']) )
	unset( $_SESSION['reload_parent'] );
if( ! defined('NTS_HEADER_SENT') ){
	if( isset($NTS_VIEW['headFile']) && $NTS_VIEW['headFile'] && file_exists($NTS_VIEW['headFile']) )
		require( $NTS_VIEW['headFile'] );
	else
		require( dirname(__FILE__) . '/head.php' );
	define( 'NTS_NEED_FOOTER', 1 );
	}
?>

<?php global $NTS_VIEW, $NTS_CURRENT_PANEL, $NTS_REQUESTED_PANEL, $NTS_CURRENT_USER; ?>
<!-- HEADER -->
<?php if( isset($NTS_VIEW['headerFile']) && file_exists($NTS_VIEW['headerFile']) ) : ?>
	<?php require( $NTS_VIEW['headerFile'] ); ?>
<?php endif; ?>

<div id="nts">

<?php if( ntsView::isAdminAnnounce() ) : ?>
	<ul id="nts-admin-announce">
	<?php $t = ntsView::getAdminAnnounceText();	?>
		<li><?php echo $t[0]; ?></li>
	</ul>
	<?php ntsView::clearAdminAnnounce(); ?>
<?php endif; ?>

<?php if( NTS_APP_LITE && ( preg_match('/^admin/', $NTS_CURRENT_PANEL) ) ) : ?>
<p>
Order <a href="<?php echo NTS_APP_URL; ?>">full version</a> to get loads of additional features!
<?php endif; ?>

<!-- USER ACCOUNT INFO  -->
<?php require( dirname(__FILE__) . '/user-info.php' ); ?>

<!-- MENU  -->
<?php if( $NTS_VIEW['menu1'] ) : ?>
	<ul id="nts-menu1">
	<?php foreach( $NTS_VIEW['menu1'] as $m ) : ?>
		<?php
		$currentOne = false;
		if( 
			( ($m['panel'] == substr($NTS_REQUESTED_PANEL, 0, strlen($m['panel']))) && ( (strlen($NTS_REQUESTED_PANEL) == $m['panel']) || (substr($NTS_REQUESTED_PANEL, strlen($m['panel']), 1) == '/') ) )
			||
			( ($m['panel'] == substr($NTS_CURRENT_PANEL, 0, strlen($m['panel']))) && ( (strlen($NTS_CURRENT_PANEL) == $m['panel']) || (substr($NTS_CURRENT_PANEL, strlen($m['panel']), 1) == '/') ) )
			){
			$currentOne = true;
			}
		$link = ntsLink::makeLink($m['panel'], '', $m['params'], false, true);
		?>
		<li><a<?php if ( $currentOne ){ echo ' class="current"'; } ?> href="<?php echo $link; ?>"><?php echo $m['title']; ?></a></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if( $NTS_VIEW['subtitle'] ) : ?>
	<h2><?php echo $NTS_VIEW['subtitle']; ?></h2>
<?php endif; ?>

<?php if( $NTS_VIEW['menu2'] ) : ?>
	<ul id="nts-menu2">
	<?php foreach( $NTS_VIEW['menu2'] as $m ) : ?>
		<?php 
			if( isset($m['directLink']) )
				$link = $m['directLink'];
			else {
				if( $NTS_VIEW['subHeaderFile'] && (! isset($m['params']['-skipSaveOn-'])) )
					$link = ntsLink::makeLink( $m['panel'], '', $m['params'], false );
				else {
					unset( $m['params']['-skipSaveOn-'] );
					$link = ntsLink::makeLink( $m['panel'], '', $m['params'], false, true );
					}
				}
//		echo "<br>$NTS_REQUESTED_PANEL<br>" . '"' . $m['panel'] . '"' . '<br>';
		$currentOne = false;
		if( 
			($m['panel'] == substr($NTS_REQUESTED_PANEL, 0, strlen($m['panel'])) ) ||
			($m['panel'] == substr($NTS_CURRENT_PANEL, 0, strlen($m['panel'])) )
			){
			$currentOne = true;
			}
		?>
		<li><a<?php if ( $currentOne ){ echo ' class="current"'; } ?> href="<?php echo $link; ?>"><?php echo $m['title']; ?></a></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

<!-- ANNOUNCE IF ANY -->
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

<!-- FOOTER IF ANY -->
<?php if( isset($NTS_VIEW['footerFile']) && file_exists($NTS_VIEW['footerFile']) ) : ?>
	<?php require( $NTS_VIEW['footerFile'] ); ?>
<?php endif; ?>
</div>

<?php 
if( ( ! ( preg_match('/^admin/', $NTS_CURRENT_PANEL) || preg_match('/^superadmin/', $NTS_CURRENT_PANEL) ) ) ){
	echo '<!-- for stats -->' . "\n" . '<div id="ntsCredit"><a href="' . NTS_APP_URL . '">' . 'Powered by ' . NTS_APP_TITLE . '</a></div>';
	}
?>
<?php
if( defined('NTS_NEED_FOOTER') )
	require( dirname(__FILE__) . '/footer.php' );
?>