<h2><?php echo M('Bookable Resource'); ?>: <?php echo M('Select'); ?></h2>
<ul>
<?php foreach( $NTS_VIEW['RESOURCE_SCHEDULE_EDIT'] as $resId ) : ?>
<?php
$res = ntsObjectFactory::get( 'resource' );
$res->setId( $resId );
?>
<li>
	<a href="<?php echo ntsLink::makeLink( 'admin/schedules/create', '', array('_res_id' => $res->getId(), '_copy_from' => $NTS_VIEW['id']) ); ?>"><?php echo ntsView::objectTitle($res); ?></a>
</li>
<?php endforeach; ?>
</ul>
