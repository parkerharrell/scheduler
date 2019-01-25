<?php if( isset($NTS_VIEW['display']) ) : ?>
	<?php require($NTS_VIEW['display']); ?>
<?php else : ?>
<?php
$conf =& ntsConf::getInstance();

$currentVersion = $conf->get('currentVersion');
if( ! $currentVersion )
	$currentVersion = NTS_APP_VERSION;

list( $v1, $v2, $v3 ) = explode( '.', $currentVersion );
$dgtCurrentVersion = $v1 . $v2 . sprintf('%02d', $v3 );

$fileVersion = NTS_APP_VERSION;
list( $v1, $v2, $v3 ) = explode( '.', $fileVersion );
$dgtFileVersion = $v1 . $v2 . sprintf('%02d', $v3 );
?>
<table>
<tr>
<?php ###Customized by RAH - 5/27/11 - Change hitAppointment to Appointment Scheduler
?>
<!-- <td><?php //echo M('hitAppoint Path'); ?></td> -->
<td><?php echo M('Appointment Scheduler Path'); ?></td>
<th><?php echo realpath(NTS_APP_DIR . '/../'); ?></th>
<td></td>
</tr>

<tr>
<td><?php echo M('Installed Version'); ?></td>
<th><?php echo $currentVersion; ?></th>
<td>
   <?php /*###Customized by RAH - 5/27/11 - Remove Uninstall? link
<a class="alert" href="<?php echo ntsLink::makeLink('-current-', 'uninstall' ); ?>" onClick="return confirm('<?php echo M('Are you sure?'); ?>');"><?php echo M('Uninstall'); ?>?</a>*/?>
<a class="alert" href="<?php //echo ntsLink::makeLink('-current-', 'uninstall' ); ?>" onClick="return confirm('<?php echo M('Are you sure?'); ?>');"><?php //echo M('Uninstall'); ?></a>
</td>
</tr>
<tr>
<td><?php echo M('Uploaded Version'); ?></td>
<th><?php echo $fileVersion; ?></th>
<td></td>
</tr>

<tr>
<td colspan="3">
<?php if( $dgtFileVersion > $dgtCurrentVersion ) : ?>
	<p>
	<a href="<?php echo ntsLink::makeLink('-current-/../backup', 'make' ); ?>"><?php echo M('Download Backup'); ?></a> - highly recommended!
	<p>
	<a href="<?php echo ntsLink::makeLink('-current-', 'upgrade' ); ?>"><?php echo M('Run Upgrade Procedure'); ?></a>
<?php else: ?>
	<?php echo M('No Upgrade Procedure To Run'); ?>
<?php endif; ?>
</td>
</tr>
</table>
<?php endif; ?>
