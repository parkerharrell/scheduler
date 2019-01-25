<?php
$object = $NTS_VIEW['object'];
$ff =& ntsFormFactory::getInstance();
$confirmForm =& $ff->makeForm( dirname(__FILE__) . '/confirmForm' );
?>
<H2><?php echo M('Are you sure?'); ?></H2>

<?php if( $NTS_VIEW['appsCount'] ) : ?>
	<p>
	<b><?php echo ntsView::objectTitle($object); ?></b>: <a href="<?php echo ntsLink::makeLink('admin/appointments/browse', '', array('location' => $NTS_VIEW['object']->getId())); ?>"><?php echo M('There are [b]{APPS_COUNT}[/b] appointment(s)', array('APPS_COUNT' => $NTS_VIEW['appsCount']) ); ?></a>
	<p>
	<?php echo M('If you proceed, these appointments will be cancelled' ); ?>.
<?php endif; ?>

<p>
<table>
<tr>
	<td>
	<?php
	$confirmForm->display();
	?>
	</td>
	<td>
	<A HREF="javascript:history.go(-1);"><?php echo M('Cancel'); ?></A>
	</td>
</tr>
</table>