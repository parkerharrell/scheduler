<html>
<head>
<title>hitAppoint Installation</title>

<STYLE TYPE="text/css">
LABEL {
	DISPLAY: block;
	PADDING: 0.2em 0.2em;
	MARGIN: 0.2em 0.2em 0.5em 0.2em;
	LINE-HEIGHT: 1em;
	overflow: auto;
	}
LABEL SPAN {
	FONT-WEIGHT: bold;
	DISPLAY: block;
	FLOAT: left;
	WIDTH: 12em;
	}
.success {
	FONT-WEIGHT: bold;
	COLOR: #00BB00;
	}
</STYLE>
</head>
<body>
<h1>hitAppoint Installation</h1>
<?php
$step = (isset($_REQUEST['step']) ) ? $_REQUEST['step'] : 'start';
?>
<?php if( $step == 'start' ) : ?>
<?php	require( dirname(__FILE__) . '/form.php' ); ?>
<?php elseif( $step == 'create' ): ?>
<?php	require( NTS_BASE_DIR . '/setup/create-database.php' ); ?>
<?php	require( dirname(__FILE__) . '/populate.php' ); ?>
<?php
	// take notice
		$thisPage = ntsLib::pureUrl( ntsLib::currentPageUrl() );
		$from = ntsLib::webDirName($thisPage);
	// strip started http:// as apache seems to have troubles with it
		$from = preg_replace( '/https?\:\/\//', '', $from );
		$trackUrl = 'http://www.hitappoint.com/licence/?from=' . urlencode($from);
?>
		<span class="success">Database tables created, admin account configured, sample data populated</span>
		<p>
		Your <a href="index.php">hitAppoint</a> is ready.

		<p>
		<a href="index.php"><img border="0" src="<?php echo $trackUrl; ?>"></a>
<?php endif; ?>

</body>
</html>