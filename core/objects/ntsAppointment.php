<?php
class ntsAppointment extends ntsObject {
	function ntsAppointment(){
		parent::ntsObject( 'appointment' );
		}

	function unsetGhost(){		
		$this->setProp( 'is_ghost', 0 );
		$now = time();
		$this->setProp( 'created_at', $now );
		}
	}
?>