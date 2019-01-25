<?php

//include '../botkiller.php';    # at the beginning of the file to get rid of the bots as early as possible - MAC
ini_set('memory_limit','300M');

require( dirname(__FILE__) . '/core/controller.php' );
require( dirname(__FILE__) . '/core/view.php' );

if ( @$_REQUEST['referrer']  ) $_SESSION['httpreferrer'] = $_REQUEST['referrer'];  # don't remove this, it keeps the Cookie Check working...  :^?

?>
<!--
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="../jquery.jcookie.min.js"></script>

<div id="container" style="display:none;background:#FFF;top:0;left:0;height:2000px;width: 100%;text-align:center;z-index:9999998;position:absolute">
    <p style="margin-top: 100px">
	 Cookie Check
	<p>
	In order to use this scheduler, cookies must be enabled in your browser.</p>
	<p>
	If you would like to schedule an appointment online, <br />
	<b>please enable cookies in your browser's security settings.</b></p>
	<p>
	After enabling cookies, use your browser's <i>refresh</i> button or <br />
	press the [F5] key on your keyboard to display the online scheduler. </p>
	<p>If you prefer, you can also call our office during regular business hours <br />
	and we will	be happy to assist you with scheduling your appointment.</p>
	</p>
</div>

<div id="jswarning" style="background:#FFF;top:0;left:0;height:2000px;width: 100%;text-align:center;z-index:9999999;position:absolute">
	<p style="margin-top: 100px">
	 Javascript Check
	<p>
	In order to use this scheduler, javascript must be enabled in your browser.</p>
	<p>
	If you would like to schedule an appointment online, <br />
	<b>please enable javascript in your browser's security settings.</b></p>
	<p>
	After enabling javascript, use your browser's <i>refresh</i> button or <br />
	press the [F5] key on your keyboard to display the online scheduler. </p>
	<p>If you prefer, you can also call our office during regular business hours <br />
	and we will	be happy to assist you with scheduling your appointment.</p>
	</p>
</div>



<script type="text/javascript">

    jQuery('#jswarning').hide();

    jQuery.cookie("testcookie", "testvalue")

    //Check if cookie exists`
    cookiesEnabled=( jQuery.cookie("testcookie") ) ? true : false;
    if (cookiesEnabled == false) {
        jQuery('#container').show();
    }

</script>
-->

