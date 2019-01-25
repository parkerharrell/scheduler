<?php if( $selectStyle == 'dropdown' ): ?>
	<?php
	$ff =& ntsFormFactory::getInstance();
	$form =& $ff->makeForm( dirname(__FILE__) . '/form' );
	$form->display();
	?>
<?php else : ?>
	<ul>
	<?php foreach( $entries as $s ) : ?>
		<?php require( dirname(__FILE__) . '/_index_DisplayService.php' ); ?>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
