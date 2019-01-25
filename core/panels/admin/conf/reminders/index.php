<?php
$reminderUrl = ntsLink::makeLinkFull( NTS_FRONTEND_WEBPAGE, '', '', array('nts-reminder' => 1) );
?>
<p>
Please note, that for the reminders feature to work, you need to set up a <b>cron job</b>. Cron job is a process that runs periodically at your web server.
<p>
<ul>
	<li>Log in to your web hosting control panel and go to Cron Jobs</li>
	<li>Add a cron job that will be pulling the following command every hour:
	<p>
    <b>wget -O /dev/null '<?php echo $reminderUrl; ?>'</b>
	</li>
</ul>

<p>
Once you get the cron job configured at your website, please edit the following setting:
<p>
<?php
$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form' );
$form->display();
?>