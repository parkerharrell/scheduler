<?php
$object = $NTS_VIEW['object'];
$ff =& ntsFormFactory::getInstance();
$confirmForm =& $ff->makeForm( dirname(__FILE__) . '/confirmForm' );
?>
<H2><?php echo M('Are you sure?'); ?></H2>

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