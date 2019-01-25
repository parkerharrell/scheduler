<?php
$ntsConf =& ntsConf::getInstance();
$settingFormat = $ntsConf->get('dateFormat');
$myTimeFormat = $settingFormat ? $settingFormat : 'd/m/Y';
$weekStartsOn = $ntsConf->get('weekStartsOn');
$t = new ntsTime;

switch( $inputAction ){
	case 'display':
$input .=<<<EOT
<script language="javascript">
// Simulates PHP's date function
Date.prototype.format = function(format) {
	var returnStr = '';
	var replace = Date.replaceChars;
	for (var i = 0; i < format.length; i++) {
		var curChar = format.charAt(i);
		if (replace[curChar]) {
			returnStr += replace[curChar].call(this);
		} else {
			returnStr += curChar;
		}
	}
	return returnStr;
};
Date.replaceChars = {
	shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	longMonths: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	shortDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
	longDays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],

	// Day
	d: function() { return (this.getDate() < 10 ? '0' : '') + this.getDate(); },
	D: function() { return Date.replaceChars.shortDays[this.getDay()]; },
	j: function() { return this.getDate(); },
	l: function() { return Date.replaceChars.longDays[this.getDay()]; },
	N: function() { return this.getDay() + 1; },
	S: function() { return (this.getDate() % 10 == 1 && this.getDate() != 11 ? 'st' : (this.getDate() % 10 == 2 && this.getDate() != 12 ? 'nd' : (this.getDate() % 10 == 3 && this.getDate() != 13 ? 'rd' : 'th'))); },
	w: function() { return this.getDay(); },
	z: function() { return "Not Yet Supported"; },
	// Week
	W: function() { return "Not Yet Supported"; },
	// Month
	F: function() { return Date.replaceChars.longMonths[this.getMonth()]; },
	m: function() { return (this.getMonth() < 9 ? '0' : '') + (this.getMonth() + 1); },
	M: function() { return Date.replaceChars.shortMonths[this.getMonth()]; },
	n: function() { return this.getMonth() + 1; },
	t: function() { return "Not Yet Supported"; },
	// Year
	L: function() { return (((this.getFullYear()%4==0)&&(this.getFullYear()%100 != 0)) || (this.getFullYear()%400==0)) ? '1' : '0'; },
	o: function() { return "Not Supported"; },
	Y: function() { return this.getFullYear(); },
	y: function() { return ('' + this.getFullYear()).substr(2); },
	// Time
	a: function() { return this.getHours() < 12 ? 'am' : 'pm'; },
	A: function() { return this.getHours() < 12 ? 'AM' : 'PM'; },
	B: function() { return "Not Yet Supported"; },
	g: function() { return this.getHours() % 12 || 12; },
	G: function() { return this.getHours(); },
	h: function() { return ((this.getHours() % 12 || 12) < 10 ? '0' : '') + (this.getHours() % 12 || 12); },
	H: function() { return (this.getHours() < 10 ? '0' : '') + this.getHours(); },
	i: function() { return (this.getMinutes() < 10 ? '0' : '') + this.getMinutes(); },
	s: function() { return (this.getSeconds() < 10 ? '0' : '') + this.getSeconds(); },
	// Timezone
	e: function() { return "Not Yet Supported"; },
	I: function() { return "Not Supported"; },
	O: function() { return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + '00'; },
	P: function() { return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + ':' + (Math.abs(this.getTimezoneOffset() % 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() % 60)); },
	T: function() { var m = this.getMonth(); this.setMonth(0); var result = this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/, '$1'); this.setMonth(m); return result;},
	Z: function() { return -this.getTimezoneOffset() * 60; },
	// Full Date/Time
	c: function() { return this.format("Y-m-d") + "T" + this.format("H:i:sP"); },
	r: function() { return this.toString(); },
	U: function() { return this.getTime() / 1000; }
};

function ntsPositionInfo(object) {

  var p_elm = object;

  this.getElementLeft = getElementLeft;
  function getElementLeft() {
    var x = 0;
    var elm;
    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    while (elm != null) {
      x+= elm.offsetLeft;
      elm = elm.offsetParent;
    }
    return parseInt(x);
  }

  this.getElementWidth = getElementWidth;
  function getElementWidth(){
    var elm;
    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    return parseInt(elm.offsetWidth);
  }

  this.getElementRight = getElementRight;
  function getElementRight(){
    return getElementLeft(p_elm) + getElementWidth(p_elm);
  }

  this.getElementTop = getElementTop;
  function getElementTop() {
    var y = 0;
    var elm;
    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    while (elm != null) {
      y+= elm.offsetTop;
      elm = elm.offsetParent;
    }
    return parseInt(y);
  }

  this.getElementHeight = getElementHeight;
  function getElementHeight(){
    var elm;
    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    return parseInt(elm.offsetHeight);
  }

  this.getElementBottom = getElementBottom;
  function getElementBottom(){
    return getElementTop(p_elm) + getElementHeight(p_elm);
  }
}

function ntsCalendarControl() {

  var calendarId = 'ntsCalendarControl';
  var currentYear = 0;
  var currentMonth = 0;
  var currentDay = 0;

  var selectedYear = 0;
  var selectedMonth = 0;
  var selectedDay = 0;

  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var VALUE_FIELD = null;
	var DISPLAY_FIELD = null;

  function getProperty(p_property){
    var p_elm = calendarId;
    var elm = null;

    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    if (elm != null){
      if(elm.style){
        elm = elm.style;
        if(elm[p_property]){
          return elm[p_property];
        } else {
          return null;
        }
      } else {
        return null;
      }
    }
  }

  function setElementProperty(p_property, p_value, p_elmId){
    var p_elm = p_elmId;
    var elm = null;

    if(typeof(p_elm) == "object"){
      elm = p_elm;
    } else {
      elm = document.getElementById(p_elm);
    }
    if((elm != null) && (elm.style != null)){
      elm = elm.style;
      elm[ p_property ] = p_value;
    }
  }

  function setProperty(p_property, p_value) {
    setElementProperty(p_property, p_value, calendarId);
  }

  function getDaysInMonth(year, month) {
    return [31,((!(year % 4 ) && ( (year % 100 ) || !( year % 400 ) ))?29:28),31,30,31,30,31,31,30,31,30,31][month-1];
  }

	function getDayOfWeek(year, month, day) {
		var date = new Date(year,month-1,day)
		return date.getDay();
		}

  this.clearDate = clearDate;
	function clearDate() {
		VALUE_FIELD.value = '';
		var displayField = document.getElementById( DISPLAY_FIELD );
		displayField.innerHTML = '';
		hide();
		}

	this.setDate = setDate;
	function setDate( year, month, day ){	
		if (VALUE_FIELD) {
			if (month < 10) {month = "0" + month;}
			if (day < 10) {day = "0" + day;}

			var myDate = new Date( year, month - 1, day );
			var dateString = myDate.format('$myTimeFormat');
			var valueString = year + "" + month + "" + day;;

			VALUE_FIELD.value = valueString;
			var displayField = document.getElementById( DISPLAY_FIELD );
			displayField.innerHTML = dateString;

			hide();
			}
		return;
		}

  this.changeMonth = changeMonth;
  function changeMonth(change) {
    currentMonth += change;
    currentDay = 0;
    if(currentMonth > 12) {
      currentMonth = 1;
      currentYear++;
    } else if(currentMonth < 1) {
      currentMonth = 12;
      currentYear--;
    }

    calendar = document.getElementById(calendarId);
    calendar.innerHTML = calendarDrawTable();
  }

  this.changeYear = changeYear;
  function changeYear(change) {
    currentYear += change;
    currentDay = 0;
    calendar = document.getElementById(calendarId);
    calendar.innerHTML = calendarDrawTable();
  }

  function getCurrentYear() {
    var year = new Date().getYear();
    if(year < 1900) year += 1900;
    return year;
  }

  function getCurrentMonth() {
    return new Date().getMonth() + 1;
  } 

  function getCurrentDay() {
    return new Date().getDate();
  }
EOT;

$text_Close = M('Close');
$text_ShortDays = "'" . join( "','", array( M('Sun'), M('Mon'), M('Tue'), M('Wed'), M('Thu'), M('Fri'), M('Sat') ) ) . "'";

$input .=<<<EOT

function calendarDrawTable() {
	var shortDays = [ $text_ShortDays ];
	var dayOfMonth = 1;
	var validDay = 0;
	var startDayOfWeek = getDayOfWeek(currentYear, currentMonth, dayOfMonth);
	var daysInMonth = getDaysInMonth(currentYear, currentMonth);
	var css_class = null; //CSS class for each day

	var table = "<table cellspacing='0' cellpadding='0'>";

	table = table + "<tr class='header'>";
	table = table + "  <th colspan='1' class='previous'><a href='javascript:ntsCalendarControl.changeMonth(-1);'>&lt;</a></th>";
	table = table + "  <th colspan='5' class='title' style='text-align: center;'>" + months[currentMonth-1] + " " + currentYear + "</th>";
	table = table + "  <th colspan='1' class='next'><a href='javascript:ntsCalendarControl.changeMonth(1);'>&gt;</a></th>";
	table = table + "</tr>";
	table = table + "</table>";

	table = table + "<table cellspacing='0' cellpadding='0' border='0'>";

	table = table + "<tr>";
	var weekStartsOn = $weekStartsOn;
	for( var i = 0; i <= 6; i++ ){
		var realWeekDay = weekStartsOn + i;
		if( realWeekDay > 6 )
			realWeekDay = realWeekDay - 7;
		table = table + "<td>" + shortDays[realWeekDay] + "</td>";
		}
	table = table + "</tr>";
	for(var week=0; week < 6; week++) {
		if( dayOfMonth > daysInMonth )
			continue;
		table = table + "<tr>";
		for( var i = 0; i <= 6; i++ ){
			var dayOfWeek = weekStartsOn + i;
			if( dayOfWeek > 6 )
				dayOfWeek = dayOfWeek - 7;

			if (week == 0 && startDayOfWeek == dayOfWeek){
				validDay = 1;
				}
			else if ( validDay == 1 && dayOfMonth > daysInMonth ){
				validDay = 0;
				}

			if(validDay) {
				if (dayOfMonth == selectedDay && currentYear == selectedYear && currentMonth == selectedMonth){
					css_class = 'current';
					}
				else if (dayOfWeek == 0 || dayOfWeek == 6){
					css_class = 'weekend';
					}
				else {
					css_class = 'weekday';
					}
				table = table + "<td class='" + css_class + "'><a href=\"javascript:ntsCalendarControl.setDate("+currentYear+","+currentMonth+","+dayOfMonth+")\">" + dayOfMonth + "</a></td>";
				dayOfMonth++;
				}
			else {
				table = table + "<td class='empty'>&nbsp;</td>";
				}
			}
		table = table + "</tr>";
		}

    table = table + "<tr class='footer'><th colspan='7'><a href='javascript:ntsCalendarControl.hide();'>$text_Close</a></td></tr>";
    table = table + "</table>";

    return table;
  }

	this.show = show;
	function show( valueField, displayFieldId ){
		can_hide = 0;
	    // If the calendar is visible and associated with this field do not do anything.
		if (VALUE_FIELD == valueField){
			return;
			}
		else {
			VALUE_FIELD = valueField;
			DISPLAY_FIELD = displayFieldId;
			}
    if(VALUE_FIELD) {
      try {
        var dateString = new String( VALUE_FIELD.value );
        selectedYear = parseInt( dateString.substr(0,4),10 );
        selectedMonth = parseInt( dateString.substr(4,2),10 );
        selectedDay = parseInt( dateString.substr(6,2),10 );
      } catch(e) {}
    }

    if (!(selectedYear && selectedMonth && selectedDay)) {
      selectedMonth = getCurrentMonth();
      selectedDay = getCurrentDay();
      selectedYear = getCurrentYear();
    }

    currentMonth = selectedMonth;
    currentDay = selectedDay;
    currentYear = selectedYear;

    if(document.getElementById){
		calendar = document.getElementById(calendarId);
		calendar.innerHTML = calendarDrawTable(currentYear, currentMonth);

      setProperty('display', 'block');

		var fieldPos = new ntsPositionInfo( DISPLAY_FIELD );
		var calendarPos = new ntsPositionInfo( calendarId );

      var x = fieldPos.getElementLeft();
      var y = fieldPos.getElementBottom();

      setProperty('left', x + "px");
      setProperty('top', y + "px");
 
      if (document.all) {
        setElementProperty('display', 'block', 'ntsCalendarControlIFrame');
        setElementProperty('left', x + "px", 'ntsCalendarControlIFrame');
        setElementProperty('top', y + "px", 'ntsCalendarControlIFrame');
        setElementProperty('width', calendarPos.getElementWidth() + "px", 'ntsCalendarControlIFrame');
        setElementProperty('height', calendarPos.getElementHeight() + "px", 'ntsCalendarControlIFrame');
      }
    }
  }

	this.hide = hide;
	function hide(){
		if( VALUE_FIELD ){
			setProperty('display', 'none');
			setElementProperty('display', 'none', 'ntsCalendarControlIFrame');
			VALUE_FIELD = null;
			}
		}

	this.visible = visible;
	function visible(){
		return VALUE_FIELD
		}
	this.can_hide = can_hide;
	var can_hide = 0;
	}

var ntsCalendarControl = new ntsCalendarControl();

//document.write("<iframe id='ntsCalendarControlIFrame' src='javascript:false;' frameBorder='0' scrolling='no'></iframe>");
//document.write("<div id='ntsCalendarControl'></div>");
</script>

<style>
#ntsCalendarControlIFrame {
	display: none;
	left: 0px;
	position: absolute;
	top: 0px;
	height: 25em;
	width: 18em;
	z-index: 99;
	}

#ntsCalendarControl {
	position:absolute;
	background-color: inherit;
	margin:0;
	display:none;
	z-index: 100;
	}

#ntsCalendarControl table {
	margin: 0.5em 1em 0 1em;
	border-collapse: collapse;
	width: 17.5em;
	}

#ntsCalendarControl table td {
	text-align: center;
	margin: 0;
	padding: 0;
	border: #999999 1px solid;
	line-height: 2em;
	font-size: 0.75em;
	width: 2.5em;
	}
#ntsCalendarControl th.previous, #ntsCalendarControl th.next {
	width: 3em;
	}
#ntsCalendarControl th.previous {
	text-align: left;
	}
#ntsCalendarControl th.next {
	text-align: right;
	}

#ntsCalendarControl tr.header {
	line-height: 2em;
	}
#ntsCalendarControl tr.header a {
	text-decoration: none;
	}
#ntsCalendarControl tr.footer {
	line-height: 2em;
	}

/* these are later can be redefined in the app css file */
#ntsCalendarControl {
	background-color: #FFF;
	border: 1px solid #336;
	}
#ntsCalendarControl td.empty {
	background-color: #ffffff;
	}
#ntsCalendarControl td.weekday {
	background-color: #eeeeee;
	}
#ntsCalendarControl td.weekend {
	background-color: #ffffcc;
	}
#ntsCalendarControl td.current {
	background-color: #333366;
	color: #ffffff;
	}
#ntsCalendarControl td.current a {
	color: #ffffff;
	}
#ntsCalendarControl tr.header a, #ntsCalendarControl tr.footer a {
	color: #ffffff;
	background-color: #999999;
	padding: 0.25em 1em;
	}

</style>
EOT;
		if( ! $conf['value'] )
			$conf['value'] = date( 'Ymd' );

		$input .= $this->makeInput(
			'hidden',
			array(
				'id'		=> $conf['id'] . '2',
				'value'		=> $conf['value'],
				)
			);

		list( $year, $month, $day ) = ntsTime::splitDate( $conf['value'] );
		$t->setDateTime( $year, $month, $day, 0, 0, 0 );
		$defaultFormattedValue = $t->formatDate();

		$hiddenInputId = "document.forms['" . $this->getName() . "']." . $conf['id'] . '2';
		$displayItemId = $conf['id'] . '-display';
		$input .=<<<EOT

<iframe id='ntsCalendarControlIFrame' src='javascript:false;' frameBorder='0' scrolling='no'></iframe>
<div id='ntsCalendarControl'></div>

		<a id="$displayItemId" name="$displayItemId" href="#" onClick="ntsCalendarControl.show( $hiddenInputId, this.id ); return false;">$defaultFormattedValue</a>
EOT;

		break;

	case 'submit':
		$input = $req->getParam( $handle . '2' );
		break;

	case 'check_submit':
		$input = isset( $_POST[$handle . '2'] ) ? true : false;
		break;
	}
?>