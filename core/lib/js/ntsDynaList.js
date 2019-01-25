function ntsDynaList(){
	this.formId = 'formId';
	this.controlId = 'thisId';
	this.newItemsUrl = 'newItemsUrl';
	this.allItemsCount = 100;

	this.containerId = 'ntsDynaList_Container';
	this.addContainerId = 'ntsDynaList_AddContainer';
	this.newContainerId = 'ntsDynaList_NewContainer';

	this.headerAddon = '';
	this.rowAddon = '';

	this.allowEmpty = false;
	this.sortOptions = false;
	this.readonly = false;
	this.minValue = 0;
	this.maxValue = 0;
	
	this.inputAddons = new Array(); 

	this.currentOptions = new Array();
	this.currentOptionsDisplay = new Array();

	this.makeListContent = function(){
		var finalHtml = "";
		finalHtml += '<table class="nts-listing" id="' + this.containerId + '" border="0">';

		if( this.headerAddon && (this.currentOptions.length > 0) ){
			finalHtml += this.headerAddon;
			}

		for ( var i = 0; i < this.currentOptions.length; i++) {
			var trClass = (i % 2) ? 'even' : 'odd';
			var thisCatValue = '';
			var tableRowId = this.containerId + '_' + this.currentOptions[i].id;

			thisCatValue += 
				'<tr class="' + trClass + '" id="' + tableRowId + '">' +
				'<td>' + this.currentOptions[i].title + '</td>'
				;

			if( this.rowAddon ){
				var addonCode = this.rowAddon;
				addonCode = addonCode.replace( /{OPTION_ID}/g, this.currentOptions[i].id );
				var tCode = 'thisCatValue += ' + addonCode + ';';
				eval( tCode );
				}

			thisCatValue += '<td>';

			if( ( ! this.readonly ) && ( this.allowEmpty || (this.currentOptions.length > 1) ) ){
				var deleteCtlId = this.controlId + '_Delete_' + this.currentOptions[i].id;
				thisCatValue += 
					'<a class="ntsDeleteControl" href="#' + deleteCtlId + '" id="' + deleteCtlId + '" name="' + deleteCtlId + '" onClick="return false;">' + '[X]' + '</a>';
					;
				}
			else {
				thisCatValue += '&nbsp;';
				}
			thisCatValue += '</td></tr>';

			finalHtml += thisCatValue;
//		alert( thisCatValue );
			}
		finalHtml += '</table>';
		return finalHtml;
		}

	this.makeValue = function( catsArray ){
		var finalCatValue = "";
		var catLines = new Array();

		for ( var i = 0; i < catsArray.length; i++) {
			var catLine = catsArray[ i ].id;
			catLines.push( catLine );
			}

		finalCatValue = catLines.join( '||' );
//		alert( finalCatValue );
		return finalCatValue;
		}

	this.setCurrentValue = function(){
		var currentValue = this.makeValue( this.currentOptions );
		document.forms[ this.formId ][ this.controlId ].value = currentValue;
		}

	this.getCurrentValue = function(){
		return document.forms[ this.formId ][ this.controlId ].value;
		}

	this.buildDisplay = function(){
		var saveAddonValues = new Array();
		if( this.inputAddons.length ){
			for( i = 0; i < this.currentOptions.length; i++ ){
				for( j = 0; j < this.inputAddons.length; j++ ){
					var addonHandle = this.controlId + '_' + this.inputAddons[j] + '_' + this.currentOptions[i].id;
					if( document.forms[ this.formId ][ addonHandle ] ){
						var addonValue = new Object();
						addonValue.id = addonHandle;
						addonValue.value = document.forms[ this.formId ][ addonHandle ].value;
						saveAddonValues.push( addonValue );
						}
					}
				}
			}
	
		jQuery('#' + this.containerId).html( this.makeListContent() );
		this.setCurrentValue();

		if( this.currentOptions.length < this.allItemsCount ){
			jQuery('#' + this.addContainerId).show();
			}
		else {
			jQuery('#' + this.addContainerId).hide();
			}
		jQuery('#' + this.newContainerId).hide();
		jQuery('#' + this.newContainerId).html('');
		
		if( saveAddonValues.length > 0 ){
			for( i = 0; i < saveAddonValues.length; i++ ){
				document.forms[ this.formId ][ saveAddonValues[i].id ].value = saveAddonValues[i].value;
				}
			}
		}

	this.addOption = function( valueString ){
		var newValue = valueString.split( '::' );

		var cat = new Object();
		cat.id = newValue[0];
		cat.title = newValue[1];
		if( newValue[2] ){
			var addValue = newValue[2].split( '||' );
			cat[ addValue[0] ] = addValue[1]; 
			}

		this.currentOptions.push( cat );
		if( this.sortOptions ){
			this.currentOptions.sort( function(a,b){ return (a.id - b.id); } );
			}
		this.buildDisplay();
		return true;
		}

	this.deleteOption = function( id ){
		for ( var i = 0; i < this.currentOptions.length; i++) {
			if( this.currentOptions[ i ].id == id ){
				this.currentOptions.splice( i, 1 );
				break;
				}
			}
		if( this.sortOptions ){
			this.currentOptions.sort( function(a,b){ return (a.id - b.id); } );
			}

		this.buildDisplay();
//		this.deleteDisplayRow( id );
		return true;
		}
		
	this.deleteDisplayRow = function( id ){
		var tableRowId = this.containerId + '_' + id;
		jQuery('#' + tableRowId).hide();
		}

	this.setMin = function( minValue ){
		this.minValue = minValue;
//		alert( "set min " + this.minValue );
		return true;
		}

	this.setMax = function( maxValue ){
		this.maxValue = maxValue;
//		alert( "set max " + this.maxValue );
		return true;
		}

	this.walkOptions = function(){
		var myLength = this.currentOptions.length;
		for ( var i = (myLength - 1); i >= 0; i--) {
			if(
				( this.currentOptions[ i ].id < this.minValue )
				||
				( this.currentOptions[ i ].id >= this.maxValue )
				)
				{
//				alert( this.currentOptions[ i ].id + ' vs ' + this.minValue );
//				alert( this.currentOptions[ i ].id + ' vs ' + this.maxValue );
				
				this.deleteOption( this.currentOptions[ i ].id );
				}
			}
		}

	this.toggleNewForm = function( showInput ){
		if( showInput ){
			jQuery('#' + this.addContainerId).hide();
			jQuery('#' + this.newContainerId).show();
			jQuery('#' + this.newContainerId).html( 'loading ...' );

			var newContainerId = this.newContainerId;
			jQuery('#' + newContainerId).load( this.newItemsUrl, {current: this.getCurrentValue(), min: this.minValue, max: this.maxValue}, function(){
				jQuery('#' + newContainerId).html( '<select id="' + newContainerId +'_select">' + jQuery('#' + newContainerId).html() + '</select>' );
				});
			}
		else {
			jQuery('#' + this.addContainerId).show();
			jQuery('#' + this.newContainerId).html( '' );
			jQuery('#' + this.newContainerId).hide();
			}
		return true;
		}
	}
