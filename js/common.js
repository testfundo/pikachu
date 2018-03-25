/**
 * Copyright (c) 2007 Nathan Kinkade
 * 
 * This code is offered under an MIT (X11) license.  For more information
 * about the terms of this license see the file LICENSE included with this
 * software or visit: http://www.opensource.org/licenses/mit-license.php
 */

function validateNotEmpty(fields) {

	var myFields = new Array();
	myFields = fields.split(",");

	for ( idx = 0; idx < myFields.length; idx++ ) {
		var myField = getElement(myFields[idx]);
		/*
		change the bgcolor back to white just in case the this field
		was already colored and the user has since filled it in.
		*/
		myField.style.backgroundColor = "#ffffff";
		if ( empty(myField.value) ) {
			alert("Please fill in required field - highlighted in red.");
			myField.style.backgroundColor = "#efb5b5";
			myField.focus();
			return false;
		}
	}

}

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
