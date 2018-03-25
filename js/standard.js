/**
 * Copyright (c) 2007 Nathan Kinkade
 * 
 * This code is offered under an MIT (X11) license.  For more information
 * about the terms of this license see the file LICENSE included with this
 * software or visit: http://www.opensource.org/licenses/mit-license.php
 */

function empty(field) {

	field = trim(field);
	if ( field ) {
		return false;
	} else {
		return true;
	}

}

function trim(string) {
	string = string.replace(/^\s+/, '');
	string = string.replace(/\s+$/, '');
	return string;
}

function getElement(elemid) {

		/* the former for Firefox and crew, the latter for IE */
        return (document.getElementById) ? document.getElementById(elemid) : document.all[elemid];

}

function submitForm(formid) {

	var myForm = getElement(formid);

	myForm.submit();

	return true;

}

function checkAll(fieldname) {

	var myCheckBoxes = document.getElementsByName(fieldname);

	for ( idx = 0; idx < myCheckBoxes.length; idx++ ) {
		if ( myCheckBoxes[idx].checked == false ) {
			myCheckBoxes[idx].checked = true;
		}
	}
}

function uncheckAll(fieldname) {

	var myCheckBoxes = document.getElementsByName(fieldname);

	for ( idx = 0; idx < myCheckBoxes.length; idx++ ) {
		if ( myCheckBoxes[idx].checked == true ) {
			myCheckBoxes[idx].checked = false;
		}
	}
}

function switchToStylesheet(title) {

	var idx, linkElement;

	for ( idx = 0; (linkElement = document.getElementsByTagName("link")[idx]); idx++ ) {
		if ( (linkElement.getAttribute("rel").indexOf("style") != -1) && (linkElement.getAttribute("title")) ) {
			linkElement.disabled = true;
			if ( linkElement.getAttribute("title") == title ) {
				linkElement.disabled = false;
			}
		}
	}

}

// this is only here because XHTML 1.0 strict has deprected the target attribute
function openInNewWindow(url) {
	window.open(url, "_blank", "resizable=yes ,scrollbars=yes,toolbar=no,menubar=yes,width=640,height=480");
}
