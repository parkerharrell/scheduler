<?php
global $NTS_VIEW, $NTS_CURRENT_USER;

$class = 'appointment';
$om =& objectMapper::getInstance();
$req = new ntsRequest();

// if we have several apps, check services and custom forms
$allServices = $this->getValue( 'services' );
$allForms = array();
$form2service = array();
reset( $allServices );
foreach( $allServices as $serv ){
	$formId = $om->isFormForService( $serv->getId() );
	if( ! in_array($formId, $allForms) ){
		$allForms[] = $formId;
		if( ! isset($form2service[$formId]) )
			$form2service[ $formId ] = array();
		$form2service[ $formId ][] = $serv->getId();
		}
	}
?>

<table>

<?php
	if( 
		($NTS_CURRENT_USER->getId() < 1) && 
		( 
		isset($_SESSION['temp_customer_id'])) || 
		( $req->getParam('email') && $req->getParam('first_name') && $req->getParam('last_name') )
		)
	:
?>
	<?php 
	$tempCustomer = new ntsUser();
	if( isset($_SESSION['temp_customer_id']) ){
		$tempCustomer->setId( $_SESSION['temp_customer_id'] );
		}
	elseif( $req->getParam('email') && $req->getParam('first_name') && $req->getParam('last_name') ) {
		$noCookieFields = array( 'email', 'first_name', 'last_name' );
		foreach( $noCookieFields as $ncf ){
			$tempCustomer->setProp( $ncf, $req->getParam($ncf) );
			echo $this->makeInput (
				'hidden',
				array(
					'id'	=> $ncf,
					'value'	=> $req->getParam($ncf)
					)
				);
			}
		}
	?>
	<tr>
		<th><?php echo M('Your Name'); ?></th>
		<td>
			<b><?php echo $tempCustomer->getProp('first_name'); ?> <?php echo $tempCustomer->getProp('last_name'); ?></b>
			<a href="<?php echo ntsLink::makeLink('-current-', 'reset_customer'); ?>"><?php echo M('Not you?'); ?></a>
		</td>
	</tr>
<?php endif; ?>

<?php if( $allForms ) : ?>
<?php foreach( $allForms as $formId ) : ?>
<?php
		if( ! $formId )
			continue;
		$otherDetails = array(
			'service_id'	=> $form2service[ $formId ][0],
			);
		$fields = $om->getFields( $class, 'external', true, $otherDetails );
		reset( $fields );
?>
<?php if( count($allForms) > 1 ) : ?>
<?php
$serviceTitles = array();
reset( $form2service[ $formId ] );
foreach( $form2service[ $formId ] as $si ){
	$thisService = ntsObjectFactory::get( 'service' );
	$thisService->setId( $si );
	$serviceTitles[] = ntsView::objectTitle( $thisService );
	}
?>
	<tr>
		<th colspan="2"><h3><?php echo join( ', ', $serviceTitles); ?></h3></th>
	</tr>
<?php endif; ?>

	<?php foreach( $fields as $f ) : ?>
	<?php $c = $om->getControl( $class, $f[0], false ); ?>
	<?php
	if( isset($f[4]) ){
		if( $f[4] == 'read' ){
			continue;
			}
		}
	?>
	<tr>
		<th><?php echo $c[0]; ?></th>
		<td>
		<?php
		echo $this->makeInput (
			$c[1],
			$c[2],
			$c[3]
			);
		?>
<?php	if( $c[2]['description'] ) : ?>
&nbsp;<i><?php echo $c[2]['description']; ?></i></td>
<?php	endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>

<tr>
<th>&nbsp;</th>
<td>
	<?php if( $NTS_VIEW['RESCHEDULE'] ) : ?>
		<?php echo $this->makePostParams('-current-', 'reschedule' ); ?>
		<INPUT TYPE="submit" VALUE="<?php echo M('Confirm Reschedule'); ?>">
	<?php else: ?>
		<?php echo $this->makePostParams('-current-', 'submit' ); ?>
		<INPUT TYPE="submit" VALUE="<?php echo M('Confirm Appointment'); ?>">
	<?php endif; ?>
</td>
</tr>
</table>