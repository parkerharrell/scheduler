<?php if( $selectStyle == 'dropdown' ): ?>
	<?php
	$ff =& ntsFormFactory::getInstance();
	$form =& $ff->makeForm( dirname(__FILE__) . '/form-categories' );
	$form->display();
	?>
<?php else : ?>
<SCRIPT LANGUAGE="Javascript">
function ntsElementHide( htmlElementId ){
	var htmlDiv = document.getElementById( htmlElementId );
	if( ! htmlDiv )
		return false;
	htmlDiv.style.display = "none";
	return true;
	}

function ntsElementShow( htmlElementId ){
	var htmlDiv = document.getElementById( htmlElementId );
	if( ! htmlDiv )
		return false;
	htmlDiv.style.display = "block";
	return true;
	}

function ntsElementToggle( htmlElementId, show ){
	var htmlDiv = document.getElementById( htmlElementId );
	if( ! htmlDiv )
		return false;
	if( htmlDiv.style.display == "none" )
		return ntsElementShow( htmlElementId );
	else
		return ntsElementHide( htmlElementId );
	}

function ntsExpandCategory( catId ){
	myId = 'category-' + catId;
// hide all
	mainList = document.getElementById( 'ntsSelectService' );
	if( mainList ){
		subLists = mainList.getElementsByTagName('UL');
		if( subLists ){
			for( k = 0; k < subLists.length; k++ ){
				if( (subLists[ k ].id != myId) && (subLists[ k ].className != "pleaseShow") && (subLists[ k ].className == "ntsCategory") )
					subLists[ k ].style.display = 'none';
				}
			}
		}
// show this one
	if( myId ){
		ntsElementToggle( myId );
		}
	}
</SCRIPT>
<STYLE>
#ha UL#ntsSelectService {
	list-style-type: none;
	margin: 0.5em 0;
	padding: 0;
	}
#ha UL#ntsSelectService LI UL {
	list-style-type: none;
	margin: 0.5em 0;
	padding: 0;
	}
</STYLE>

<ul id="ntsSelectService">
<?php foreach( $showCats as $cat ) : ?>
	<li>
	<h2><a id="cat-<?php echo $cat[0]; ?>" name="cat-<?php echo $cat[0]; ?>" href="#cat-<?php echo $cat[0]; ?>" onclick="ntsExpandCategory( <?php echo $cat[0]; ?> ); return false;"><?php echo $cat[1]; ?></a></h2>
	<?php $myEntries = $cat2service[$cat[0]]; ?>
	<ul id="category-<?php echo $cat[0]; ?>" class="ntsCategory" style="display: none;">
	<?php if( $cat[2] ) : ?><?php echo $cat[2]; ?><?php endif; ?>
	<?php foreach( $myEntries as $s ) : ?>
		<?php require( dirname(__FILE__) . '/_index_DisplayService.php' ); ?>
	<?php endforeach; ?>
	</ul>

	</li>
<?php endforeach; ?>
</ul>

<SCRIPT LANGUAGE="Javascript">
ntsExpandCategory( 0 );
</SCRIPT>
<?php endif; ?>