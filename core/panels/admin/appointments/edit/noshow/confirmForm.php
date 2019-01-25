<?php
$return = $this->getValue('return');
$params['return'] = $return;

$id = $this->getValue('id');
$params['_id'] = $id;
?>
<p>
<?php echo $this->makePostParams('-current-', 'noshow', $params ); ?>
<input type="submit" VALUE="<?php echo M('Proceed'); ?>">
