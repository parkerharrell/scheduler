<?php
$o = $NTS_VIEW['o'];
?>
<H2><?php echo M('Are you sure?'); ?></H2>
<p>
<b><?php echo $o['title']; ?></b><br>

<p>
<A HREF="javascript:history.go(-1);">Cancel</A>
<A class="alert" HREF="<?php echo ntsLink::makeLink('-current-', 'delete', array('id' => $o['id']) ); ?>"><?php echo M('Delete'); ?></A>