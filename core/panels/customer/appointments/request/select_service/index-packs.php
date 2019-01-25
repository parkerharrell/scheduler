<?php
$packs = $NTS_VIEW['packs'];
reset( $packs );
?>
<?php if( $selectStyle == 'dropdown' ): ?>
	<?php
	$ff =& ntsFormFactory::getInstance();
	$form =& $ff->makeForm( dirname(__FILE__) . '/form-packs' );
	$form->display();
	?>
<?php else : ?>
	<ul>
	<?php foreach( $packs as $p ) : ?>
		<li>
		<h3><?php echo $p->getProp('title'); ?></h3>
		<?php if( $p->getProp('description') ) : ?> 
			<?php echo $p->getProp('description'); ?>
		<?php endif; ?>

		<ul>
			<li>
			<a href="<?php echo ntsLink::makeLink('-current-', 'select_pack', array('id' => $p->getId()) ); ?>"><?php echo M('Select'); ?> <b><?php echo $p->getProp('title'); ?></b></a>
			</li>
		</ul>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>