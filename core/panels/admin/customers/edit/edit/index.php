<?php
$ntsdb =& dbWrapper::getInstance();
$id = $NTS_VIEW['id'];
$ff =& ntsFormFactory::getInstance();
?>
<?php
$NTS_VIEW['form']->display();
?>