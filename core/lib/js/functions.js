var elementsCache = new Object();

function ntsUpdateCurrentLocation( newParamsArray ){
// get current
	var paramList = new Array();
	var url = window.location.toString();
	var queryString = url.split("?");

	if( queryString.length > 1 ){
		var params = queryString[1].split("&");
		for( var i = 0; i < params.length; i++ ){
			var paramItem = params[i].split("=");
			paramList[ paramItem[0] ] = paramItem[1];
			}
		}

// update
	for( var i in newParamsArray ){
		paramList[ i ] = escape( newParamsArray[ i ] );
		}

// build url
	var paramStrings = new Array();
	var k = 0;
	for( var i in paramList ){
		paramStrings[k] = i + "=" + paramList[i];
		k++;
		}
	var finalParamString = paramStrings.join( "&" );
	
	window.location.search = finalParamString;
	}

function ntsMarkAllRows( container_id, clear ) {
	var rows = document.getElementById(container_id).getElementsByTagName('tr');
	var unique_id;
	var checkbox;

	for ( var i = 0; i < rows.length; i++ ) {
		checkbox = rows[i].getElementsByTagName( 'input' )[0];

		if ( checkbox && checkbox.type == 'checkbox' ) {
			unique_id = checkbox.name + checkbox.value;
			if ( checkbox.disabled == false ) {
				if( clear )
					checkbox.checked = true;
				else
					checkbox.checked = false;
				}
			}
		}
	return true;
	}

function ntsElementHide( htmlElementId ){
	var htmlDiv = document.getElementById( htmlElementId );
	if( ! htmlDiv ){
		return false;
		}
	htmlDiv.style.display = "none";
	return true;
	}

function ntsElementShow( htmlElementId, viewStyle ){
	viewStyle = typeof(viewStyle) != 'undefined' ? viewStyle : 'block';
	var htmlDiv = document.getElementById( htmlElementId );
	if( ! htmlDiv )
		return false;
	htmlDiv.style.display = viewStyle;
	return true;
	}

function ntsElementToggle( htmlElementId, show, viewStyle ){
	viewStyle = typeof(viewStyle) != 'undefined' ? viewStyle : 'block';
	var htmlDiv = document.getElementById( htmlElementId );
	if( ! htmlDiv )
		return false;
	if( htmlDiv.style.display == "none" )
		return ntsElementShow( htmlElementId, viewStyle );
	else
		return ntsElementHide( htmlElementId );
	}

function ntsElementDelete( htmlElementId ){
	var htmlDiv = document.getElementById( htmlElementId );
	if( htmlDiv && (htmlDiv.style.display != "none" ) ){
		elementsCache[htmlElementId] = htmlDiv.innerHTML;
		ntsElementHide( htmlElementId );
		htmlDiv.innerHTML = '';
		}
	return true;
	}

function ntsElementRestore( htmlElementId ){
	var htmlDiv = document.getElementById( htmlElementId );
	if( htmlDiv && (htmlDiv.style.display == "none") ){
		ntsElementShow( htmlElementId );
		if( elementsCache[htmlElementId] ){
			htmlDiv.innerHTML = elementsCache[htmlElementId];
			}
		}
	return true;
	}

function ntsElementSetContent( htmlElementId, content ){
	var htmlDiv = document.getElementById( htmlElementId );
	if( ! htmlDiv )
		return false;
	htmlDiv.innerHTML = content;
	return true;
	}
