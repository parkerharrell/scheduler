<?php
echo "<p>File <b>db.php</b> found, trying to check the database settings ...";

/* check database information */
$wrapper = new ntsMysqlWrapper( NTS_DB_HOST, NTS_DB_USER, NTS_DB_PASS, NTS_DB_NAME, NTS_DB_TABLES_PREFIX );
if( ! $wrapper->checkSettings() ){
	echo '<br>';
	echo $wrapper->getError();
	echo "<p>If it is an error with your MySQL login details, please fix this problem in the <b>db.php</b> file then try again.";
	exit;
	}
echo '<span class="success">seems ok</span>';

/* check if it is already installed */
$installedVersion = '';
$currentTables = $wrapper->getTablesInDatabase();
if( in_array('conf', $currentTables) ){
	echo '<p><span style="color: #ff0000; font-size: 125%;">hitAppoint tables already exist, make sure you are not overwriting an existing database!</span>';
	}
?>
<SCRIPT LANGUAGE="JavaScript">
function checkForm(){
	if( ! document.conf_form.admin_username.value ){
		alert( 'Please enter the administrator username!' );
		return false;
		}
	if( ! document.conf_form.admin_pass.value ){
		alert( 'Please enter the administrator password!' );
		return false;
		}
	if( ! document.conf_form.admin_pass2.value ){
		alert( 'Please confirm the administrator password!' );
		return false;
		}
	if( ! document.conf_form.admin_email.value ){
		alert( 'Please enter the administrator email!' );
		return false;
		}
	if( document.conf_form.admin_pass2.value != document.conf_form.admin_pass.value ){
		alert( 'The entered passwords differ!' );
		return false;
		}
	if( ! document.conf_form.resname_sing.value ){
		alert( 'Please enter the text for Bookable Resource term!' );
		return false;
		}
	if( ! document.conf_form.resname_plu.value ){
		alert( 'Please enter the text for Bookable Resources term!' );
		return false;
		}
	return true;
	}
</SCRIPT>

<FORM METHOD="post" ID="conf_form" NAME="conf_form">
<input type="hidden" name="step" value="create">

<P>
<H3>Terminology</H3>

<p>
How would you name your bookable resources:

<LABEL FOR="resname_sing">
<SPAN>Singular</SPAN>
<input type="text" name="resname_sing" value="Bookable Resource" SIZE="42" TABINDEX="1">
<br>
<SPAN>&nbsp;</SPAN>
<i>Massage Therapist, Tennis Court etc</i>
</LABEL>

<LABEL FOR="resname_plu">
<SPAN>Plural</SPAN>
<input type="text" name="resname_plu" value="Bookable Resources" SIZE="42" TABINDEX="2">
<br>
<SPAN>&nbsp;</SPAN>
<i>Massage Therapists, Tennis Courts etc</i>
</LABEL>

<P>
<H3>Administrator Account</H3>

<LABEL FOR="admin_username">
<SPAN>Username</SPAN>
<INPUT TYPE="text" NAME="admin_username" VALUE="admin" SIZE="24" TABINDEX="3">
</LABEL>

<LABEL FOR="admin_pass">
<SPAN>Password</SPAN>
<INPUT TYPE="password" NAME="admin_pass" VALUE="" TABINDEX="4">
</LABEL>

<LABEL FOR="admin_pass2">
<SPAN>Repeat Password</SPAN>
<INPUT TYPE="password" NAME="admin_pass2" VALUE="" TABINDEX="5">
</LABEL>

<LABEL FOR="admin_email">
<SPAN>Email</SPAN>
<INPUT TYPE="text" NAME="admin_email" VALUE="admin@yoursiteaddress.com" SIZE="36" TABINDEX="6">
</LABEL>

<LABEL FOR="admin_fname">
<SPAN>First Name</SPAN>
<INPUT TYPE="text" NAME="admin_fname" VALUE="James" SIZE="24" TABINDEX="7">
</LABEL>

<LABEL FOR="admin_lname">
<SPAN>Last Name</SPAN>
<INPUT TYPE="text" NAME="admin_lname" VALUE="Brown" SIZE="24" TABINDEX="8">
</LABEL>

<P>
<INPUT TYPE="button" VALUE="Proceed to setup" ONCLICK="return checkForm() && this.form.submit();" TABINDEX="9">
</FORM>
