// JavaScript Document

$(document).ready(function() {
						   
	$(".main-nav > ul > li a").click(function(){
		$(".main-nav > ul > li a").not(this).parent().removeClass("open");
		if($(this).parent().hasClass("open")){
			$(this).parent().removeClass("open");
		}else{
			$(this).parent().addClass("open");	
		}
		
				   
	});					   
						   
	$(".sub-title").click(function(){
		var container = $(this).parent(".sub-footer");		
		if(container.hasClass("open")){
			container.removeClass("open").addClass("closed");
		}else{
		    container.removeClass("closed").addClass("open");
		}
	});		
	
	$(".cat_box.expandable h2.title").click(function(){
		var container = $(this).parent();
		var control = $("span", this);
		if(container.hasClass("closed")){
			container.removeClass("closed");
			control.text("+view less");
		}else{
			container.addClass("closed");
			control.text("+view more");
		}
	});
	
	$(".articles .title span").click(function(){
		var linkURL = $(this).parent().find("a").attr("href");
		window.location = linkURL;
	});
	
	
	$(".hp-banners li img").click(function(){
		var linkURL = $(this).attr("data-href");
		window.location = linkURL;
	});
	
});


////AUTO TAB

$(document).ready(function() {
$('#Home1').autotab({ target: 'Home2', format: 'numeric' });
$('#Home2').autotab({ target: 'Home3', format: 'numeric', previous: 'area_code' });
$('#Home3').autotab({ target: 'Work1', previous: 'Home2', format: 'numeric' });


$('#Work1').autotab({ target: 'Work2', format: 'numeric' });
$('#Work2').autotab({ target: 'Work3', format: 'numeric', previous: 'area_code' });
$('#Work3').autotab({ target: 'Cell1', previous: 'Work2', format: 'numeric' });

$('#Cell1').autotab({ target: 'Cell2', format: 'numeric' });
$('#Cell2').autotab({ target: 'Cell3', format: 'numeric', previous: 'area_code' });
$('#Cell3').autotab({ previous: 'Cell2', format: 'numeric' });

$('#Home1-1').autotab({ target: 'Home2-1', format: 'numeric' });
$('#Home2-1').autotab({ target: 'Home3-1', format: 'numeric', previous: 'area_code' });
$('#Home3-1').autotab({ target: 'Cell1-1', previous: 'Home2-1', format: 'numeric' });

$('#Work1-1').autotab({ target: 'Work2-1', format: 'numeric' });
$('#Work2-1').autotab({ target: 'Work3-1', format: 'numeric', previous: 'area_code' });
$('#Work3-1').autotab({ previous: 'Work2-1', format: 'numeric' });

$('#Cell1-1').autotab({ target: 'Cell2-1', format: 'numeric' });
$('#Cell2-1').autotab({ target: 'Cell3-1', format: 'numeric', previous: 'area_code' });
$('#Cell3-1').autotab({ target: 'Work1-1', previous: 'Cell2-1', format: 'numeric' });
});

////////////MENU FUNCTIONS
var timeout         = 0;
var closetimer		= 0;
var ddmenuitem      = 0;
function menu_open(){
menu_canceltimer();
menu_close();
ddmenuitem = $(this).find('ul').eq(0).css('visibility', 'visible');}
function menu_close(){
if(ddmenuitem) ddmenuitem.css('visibility', 'hidden');}
function menu_timer(){
closetimer = window.setTimeout(menu_close, timeout);}
function menu_canceltimer(){
if(closetimer){
window.clearTimeout(closetimer);
closetimer = null;}}
$(document).ready(function(){
$('#menu > li').bind('mouseover', menu_open)
$('#menu > li').bind('mouseout',  menu_timer)});

 $(function() {

    //Preserves the mouse-over on top-level menu elements when hovering over children
    $("#menu ul").each(function(i){
      $(this).hover(function(){
        $(this).parent().find("a").slice(0,1).addClass("active");
      },function(){
        $(this).parent().find("a").slice(0,1).removeClass("active");
      });
    });

   
  });

document.onclick = menu_close;
////////////END MENU FUNCTIONS






function fnSubmit()
{
	if (checkEmpty() && CheckPhones() && checkAreaCode(document.form1.Home1.value) && checkAreaCode(document.form1.Work1.value) && checkAreaCode(document.form1.Cell1.value)) {
	
	document.form1.submit();
	}
}

function fnSubmit2()
{
	if (checkEmpty2() && CheckPhones() && checkAreaCode(document.form1.Home1.value) && checkAreaCode(document.form1.Work1.value) && checkAreaCode(document.form1.Cell1.value)) {
	
	document.form1.submit();
	}
}

function checkEmpty()
{
	if(document.form1.FullName.value == '' || document.form1.Email.value == ''){
		alert("Please fill out the required information");
		return false;
		
	}else{
		
		return true;	
	}
} 

function checkEmpty2()
{
	if(document.form1.FirstName.value == '' || document.form1.LastName.value == '' || document.form1.Email.value == ''){
		alert("Please fill out the required information");
		return false;
		
	}else{
		return true;	
	}
} 

function CheckPhones()
{
	FillOnePhone = false;
	AllPhonesOk = true;
	Phone1RegExp = /^(\d{10})$/;
	var FeildsNames = new Array("Home", "Cell", "Work");
	for(i=0;i<FeildsNames.length;i++)
	{
		PhoneVal = eval("document.form1."+ FeildsNames[i]+ "1.value + document.form1." + FeildsNames[i]+ "2.value + document.form1." + FeildsNames[i]+ "3.value");
		if (PhoneVal == '') continue
		else
		{
		  FillOnePhone = true;
			if (!Phone1RegExp.test(PhoneVal))
			{
				alert("Insert a valid number, please!");
				eval("document.form1." + FeildsNames[i] + "1.focus()");
				AllPhonesOk = false;
				break;
			}
		}
	}
	if (!FillOnePhone)
	{
  	alert('Please fill out at least one phone number.');
		return false;
	}
	else
	  if (AllPhonesOk) return true
		else return false;
} 

function downloadAlert(){
	alert("A download for this article is not yet available. Check back soon.");	
	
}

function checkAreaCode(val){
	
	var codes = new Array(201, 202, 203, 205, 206, 207, 208, 209, 210, 212, 213, 214, 215, 216, 217, 218, 219, 224, 225, 228, 229, 231, 234, 236, 239, 240, 248, 251, 253, 254, 256, 260, 262, 267, 269, 270, 276, 278, 281, 283, 301, 302, 303, 304, 305, 307, 308, 309, 310, 312, 313, 314, 315, 316, 317, 318, 319, 320, 321, 323, 325, 330, 331, 334, 336, 337, 339, 341, 347, 351, 352, 360, 361, 369, 380, 385, 386, 401, 402, 404, 405, 406, 407, 408, 409, 410, 412, 413, 414, 415, 417, 419, 423, 424, 425, 430, 432, 434, 435, 440, 442, 443, 464, 469, 470, 475, 478, 479, 480, 484, 501, 502, 503, 504, 505, 507, 508, 509, 510, 512, 513, 515, 516, 517, 518, 520, 530, 540, 541, 551, 557, 559, 561, 562, 563, 564, 567, 570, 571, 573, 574, 575, 580, 585, 586, 601, 602, 603, 605, 606, 607, 608, 609, 610, 612, 614, 615, 616, 617, 618, 619, 620, 623, 626, 627, 628, 630, 631, 636, 641, 646, 650, 651, 660, 661, 662, 669, 678, 679, 682, 689, 701, 702, 703, 704, 706, 707, 708, 712, 713, 714, 715, 716, 717, 718, 719, 720, 724, 727, 731, 732, 734, 737, 740, 747, 754, 757, 760, 762, 736, 764, 765, 769, 770, 772, 773, 774, 775, 779, 781, 785, 786, 787, 801, 802, 803, 804, 805, 806, 808, 810, 812, 813, 841, 815, 816, 817, 818, 828, 830, 831, 832, 835, 845, 847, 848, 850, 856, 857, 858, 859, 860, 862, 863, 864, 865, 870, 872, 878, 901, 903, 904, 906, 907, 908, 909, 910, 912, 913, 914, 915, 916, 917, 918, 919, 920, 925, 927, 928, 931, 935, 936, 937, 940, 941, 947, 949, 951, 954, 956, 957, 959, 970, 971, 972, 973, 975, 978, 979, 980, 984, 985, 989);
	
	
	 var Found = false;
	if(val == ""){var Found = true; return true;}
  for (var i = 0; i < codes.length; i++){
    if (codes[i] == val){
      
	  var Found = true;
	  return true;	
      break;
	  
    }
    else if ((i == (codes.length - 1)) && (!Found)){
      if (codes[i] != val){
        alert('Please enter a valid US area code!');
		return false;
      }
    }
  }

}

/*
 * Autotab - jQuery plugin 1.0
 * http://dev.lousyllama.com/auto-tab
 * 
 * Copyright (c) 2008 Matthew Miller
 * 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 * 
 * Revised: 2008/05/22 01:23:25
 */

(function($) {

$.fn.autotab = function(options) {
	var defaults = {
		format: 'all',			// text, numeric, alphanumeric, all
		maxlength: 2147483647,	// Defaults to maxlength value
		uppercase: false,		// Converts a string to UPPERCASE
		lowercase: false,		// Converts a string to lowecase
		nospace: false,		// Remove spaces in the user input
		target: null,			// Where to auto tab to
		previous: null			// Backwards auto tab when all data is backspaced
	};

	$.extend(defaults, options);
	
	
	$.browser={};(function(){$.browser.msie=false;
	$.browser.version=0;if(navigator.userAgent.match(/MSIE ([0-9]+)\./)){
	$.browser.msie=true;$.browser.version=RegExp.$1;}})();

	var check_element = function(name) {
		var val = null;
		var check_id = $('#' + name)[0];
		var check_name = $('input[name=' + name + ']')[0];

		if(check_id != undefined)
			val = $(check_id);
		else if(check_name != undefined)
			val = $(check_name);

		return val;
	};

	var key = function(e) {
		if(!e)
			e = window.event;

		return e.keyCode;
	};

	// Sets targets to element based on the name or ID passed
	if(typeof defaults.target == 'string')
		defaults.target = check_element(defaults.target);

	if(typeof defaults.previous == 'string')
		defaults.previous = check_element(defaults.previous);

	var maxlength = $(this).attr('maxlength');

	// Each text field has a maximum character limit of 2147483647

	// defaults.maxlength has not changed and maxlength was specified
	if(defaults.maxlength == 2147483647 && maxlength != 2147483647)
		defaults.maxlength = maxlength;
	// defaults.maxlength overrides maxlength
	else if(defaults.maxlength > 0)
		$(this).attr('maxlength', defaults.maxlength)
	// defaults.maxlength and maxlength have not been specified
	// A target cannot be used since there is no defined maxlength
	else
		defaults.target = null;

	// IE does not recognize the backspace key
	// with keypress in a blank input box
	

	return this.keypress(function(e) {
		if(key(e) == 8)
		{
			var val = this.value;

			if(val.length == 0 && defaults.previous)
				defaults.previous.focus();
		}
	}).keyup(function(e) {
		var val = this.value;

		switch(defaults.format)
		{
			case 'text':
				var pattern = new RegExp('[0-9]+', 'g');
				var val = val.replace(pattern, '');
				break;

			case 'alpha':
				var pattern = new RegExp('[^a-zA-Z]+', 'g');
				var val = val.replace(pattern, '');
				break;

			case 'number':
			case 'numeric':
				var pattern = new RegExp('[^0-9]+', 'g');
				var val = val.replace(pattern, '');
				break;

			case 'alphanumeric':
				var pattern = new RegExp('[^0-9a-zA-Z]+', 'g');
				var val = val.replace(pattern, '');
				break;

			case 'all':
			default:
				break;
		}

		if(defaults.nospace)
		{
			pattern = new RegExp('[ ]+', 'g');
			val = val.replace(pattern, '');
		}

		if(defaults.uppercase)
			val = val.toUpperCase();

		if(defaults.lowercase)
			val = val.toLowerCase();

		this.value = val;

		/**
		 * Do not auto tab when the following keys are pressed
		 * 8:	Backspace
		 * 9:	Tab
		 * 16:	Shift
		 * 17:	Ctrl
		 * 18:	Alt
		 * 19:	Pause Break
		 * 20:	Caps Lock
		 * 27:	Esc
		 * 33:	Page Up
		 * 34:	Page Down
		 * 35:	End
		 * 36:	Home
		 * 37:	Left Arrow
		 * 38:	Up Arrow
		 * 39:	Right Arrow
		 * 40:	Down Arroww
		 * 45:	Insert
		 * 46:	Delete
		 * 144:	Num Lock
		 * 145:	Scroll Lock
		 */
		var keys = [8, 9, 16, 17, 18, 19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46, 144, 145];
		var string = keys.toString();

		if(string.indexOf(key(e)) == -1 && val.length == defaults.maxlength && defaults.target)
			defaults.target.focus();
	});
};

})(jQuery);


