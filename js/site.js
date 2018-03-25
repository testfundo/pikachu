/**
 * Copyright (c) 2007 Nathan Kinkade
 * 
 * This code is offered under an MIT (X11) license.  For more information
 * about the terms of this license see the file LICENSE included with this
 * software or visit: http://www.opensource.org/licenses/mit-license.php
 */

function validateSearchBox() {

	var mySearchString = getElement("searchString");

	// reset the bgcolor to white just in case this
	// is the second time they have got here without
	// having refreshed the page
	mySearchString.style.backgroundColor = "#ffffff";

	if ( empty(mySearchString.value) ) {
		mySearchString.style.backgroundColor = "#efb5b5";
		mySearchString.focus();
		alert("Please enter at least one search word.");
		return false;
	} else {
		return true;
	}

}

	//------------------------------------------------------------------//

function validateLoginFields() {

	var status = true;
	var myUsername = getElement("username");
	var myPassword = getElement("password");

	// reset the bgcolor to white just in case this
	// is the second time they have got here without
	// having refreshed the page
	myUsername.style.backgroundColor = "#ffffff";
	myPassword.style.backgroundColor = "#ffffff";

	if ( empty(myPassword.value) ) {
		myPassword.style.backgroundColor = "#efb5b5";
		myPassword.focus();
		status = false;
	}

	if ( empty(myUsername.value) ) {
		myUsername.style.backgroundColor = "#efb5b5";
		myUsername.focus();
		status = false;
	}

	if ( status == false ) {
		alert("You must enter both a username and password.");
	}

	return status;

}

	//------------------------------------------------------------------//

function validateEditUser(formId) {

	var myForm = getElement(formId);

	if ( empty(myForm.username.value) ) {
		alert("You must specify a login name.");
		myForm.username.focus();
		return false;
	} else {
		if ( myForm.username.value.length < 5 ) {
			alert("Your login name must be at least 5 characters long.");
			myForm.username.focus();
			return false;
		}
	}

	if ( ! empty(myForm.password.value) ) {
		if ( myForm.password.value.length < 5 ) {
			alert("Your password must be at least 5 characters long.");
			myForm.password.focus();
			return false;
		}
	}

	if ( myForm.password.value != myForm.password2.value ) {
		alert("Your passwords do not match.");
		myForm.password2.focus();
		return false;
	}

	if ( empty(myForm.age.value) ) {
		alert("You must specify an age (even if it's not real).");
		myForm.age.focus();
		return false;
	} else {
		if ( isNaN(myForm.age.value) ) {
			alert("Your age must be a number.");
			myForm.age.focus();
			return false;
		}
	}

}

	//------------------------------------------------------------------//

function validateRegisterUser(formId) {

	var myForm = getElement(formId);

	if ( empty(myForm.username.value) ) {
		alert("You must specify a login name.");
		myForm.username.focus();
		return false;
	} else {
		if ( myForm.username.value.length < 5 ) {
			alert("Your login name must be at least 5 characters long.");
			myForm.username.focus();
			return false;
		}
	}

	if ( empty(myForm.password.value) ) {
		alert("You must specify a password.");
		myForm.password.focus();
		return false;
	} else {
		if ( myForm.password.value.length < 5 ) {
			alert("Your password must be at least 5 characters long.");
			myForm.password.focus();
			return false;
		}
	}

	if ( myForm.password.value != myForm.password2.value ) {
		alert("Your passwords do not match.");
		myForm.password2.focus();
		return false;
	}

	if ( empty(myForm.birthday.value) ) {
		alert("You must specify a birthday (even if it's not real).");
		return false;
	}

	if ( myForm.terms.checked != true ) {
		alert("You must accept the Terms & Conditions of this site in order to register.");
		return false;
	}

	// make sure that the specified username doesn't already exist
	xajax_usernameExists(myForm.username.value); 

}

	//------------------------------------------------------------------//

function changeQuantitySource(formid, newSource) {

	// quantitySource will be either 0 or 1.  0 should be
	// a predefined quantity and 1 should be a userdefined
	// quantity
	getElement(formid).quantitySource[newSource].checked = true;

	return true;

}

	//------------------------------------------------------------------//

function toggleShowRenameField(selectid,divid) {

	var myDiv = getElement(divid);
	var mySelectBox = getElement(selectid);

	if ( mySelectBox.value == "Rename" ) {
		myDiv.style.display = "";
	} else {
		myDiv.style.display = "none";
	}

	return true;

}

	//------------------------------------------------------------------//

function validateCreateDiary(fieldId) {

	if ( empty(getElement("fieldId").value) ) {
		alert("You must specify a name for the diary.");
		getElement("fieldId").focus();
		return false;
	} else {
		return true;
	}

}

	//------------------------------------------------------------------//

function validateEditFood(formId) {

	var myForm = getElement(formId);

	if ( formId == "formEditFood" ) {
		var	myOldName = myForm.foodDesc.value
	} else if ( formId == "formQuickEditFood" ) {
		var myOldName = myForm.food.options[myForm.food.selectedIndex].text;
	}

	if ( myForm.action.value == "Delete" ) {
		var msg = "Are you sure you want to permanently delete this saved food?\n\n" + myOldName;
		if ( window.confirm(msg) ) {
			return true;
		} else {
			return false;
		}
	} else if ( myForm.action.value == "Rename") {
		if ( empty(myForm.newFoodName.value) ) {
			var msg = "You must specify a new name when renaming a saved food.";
			alert(msg);
			return false;
		}
	} else if ( myForm.action.value == "Modify") {
		// make sure that the description isn't empty
		if ( empty(myForm.foodDesc.value) ) {
			var msg = "You must specify a description.";
			alert(msg);
			myForm.foodDesc.focus();
			return false;
		}

		// if they are editing a food, then they have the ability to
		// modify the quantity, so make sure that the quantity exists
		// and that it's a number
		if ( formId == "formEditFood" ) {
			if ( ! empty(myForm.quantity.value) ) {
				if ( isNaN(myForm.quantity.value) ) {
					var msg = "The amount must be a number.";
					alert(msg);
					myForm.quantity.focus();
					return false;
				}
			} else {
				var msg = "You must specify an amount.";
				alert(msg);
				myForm.quantity.focus();
				return false;
			}
		}
		return true;
	} else if ( myForm.action.value == "Edit" ) {
		return true;
	} else {
		// there was no recognized action so don't submit the form
		var msg = "The action you specified wasn't recognized.";
		alert(msg);
		return false;
	}

}

	//------------------------------------------------------------------//

function validateEditMeal(formId) {

	var myForm = getElement(formId);

	if ( formId == "formEditMeal" ) {
		var	myOldName = myForm.mealDesc.value
	} else if ( formId == "formQuickEditMeal" ) {
		var myOldName = myForm.meal.options[myForm.meal.selectedIndex].text;
	}

	if ( myForm.action.value == "Delete" ) {
		var msg = "WARNING: If you choose to remove this recipe it will also be " +
			"removed from any diary to which you may have added it.  Are you sure you want to " +
			"permanently delete this saved recipe?\n\n" + myOldName;
		if ( window.confirm(msg) ) {
			return true;
		} else {
			return false;
		}
	} else if ( myForm.action.value == "Rename") {
		if ( empty(myForm.newMealName.value) ) {
			var msg = "You must specify a new name when renaming a saved recipe.";
			alert(msg);
			return false;
		}
	} else if ( myForm.action.value == "Modify") {
		// make sure that the description isn't empty
		if ( empty(myForm.mealDesc.value) ) {
			var msg = "You must specify a description for the recipe.";
			alert(msg);
			myForm.mealDesc.focus();
			return false;
		}

		// if they are editing a meal, then they have the ability to
		// modify the quantities, so make sure that the quantities exist
		// and that they are number
		if ( formId == "formEditMeal" ) {
			var itemIds = myForm.mealItemIds.value.split(",");
			for ( idx = 0; idx < itemIds.length; idx++ ) {
				var itemDesc = getElement("mealItemDesc-" + itemIds[idx]).value;
				if ( empty(itemDesc) ) {
					var msg = "You must specify a description for each recipe item.";
					alert(msg);
					getElement("mealItemDesc-" + itemIds[idx]).focus();
					return false;
				}
				if ( empty(getElement("mealItemQuantity-" + itemIds[idx]).value) ) {
					var msg = "You must specify an amount for recipe item '" + itemDesc + "'.";
					alert(msg);
					getElement("mealItemQuantity-" + itemIds[idx]).focus();
					return false;
				} else {
					if ( isNaN(getElement("mealItemQuantity-" + itemIds[idx]).value) ) {
						var msg = "The amount for recipe item '" + itemDesc + "' must be a number.";
						alert(msg);
						getElement("mealItemQuantity-" + itemIds[idx]).focus();
						return false;
					}
				}
			}
		}
		return true;
	} else if ( myForm.action.value == "Edit" ) {
		return true;
	} else {
		// there was no recognized action so don't submit the form
		var msg = "The action you specified wasn't recognized.";
		alert(msg);
		return false;
	}

}

	//------------------------------------------------------------------//

function validateEditDiary(formId) {

	var myForm = getElement(formId);
	var myOldName = myForm.diary.options[myForm.diary.selectedIndex].text;

	if ( myForm.action.value == "Delete" ) {
		var msg = "Are you sure you want to permanently delete this diary and all of it's content?\nThere is no way to recover the data once it is deleted.\n\n" + myOldName;
		if ( window.confirm(msg) ) {
			return true;
		} else {
			return false;
		}
	} else if ( myForm.action.value == "Rename") {
		if ( empty(myForm.newDiaryName.value) ) {
			var msg = "You must specify a new name when renaming a diary.";
			alert(msg);
			myForm.newDiaryName.focus();
			return false;
		}
	} else {
		// there was no recognized action so don't submit the form
		var msg = "The action you specified wasn't recognized.";
		alert(msg);
		return false;
	}

	return true;

}

	//------------------------------------------------------------------//

function verifyRemoveCurrentMealItem(mealItem) {

	var itemDesc = getElement("currentMealItemDesc-" + mealItem).text;
	var msg = "Are you sure you want to remove this item from the current recipe?\n\n" + itemDesc;

	if ( window.confirm(msg) ) {
		xajax_removeCurrentMealItem(mealItem);
		return true;
	} else {
		return false;
	}

}


	//------------------------------------------------------------------//

function verifyClearCurrentMeal() {

	var msg = "Are you sure you want to clear/reset the entire current recipe?\n";

	if ( window.confirm(msg) ) {
		xajax_clearCurrentMeal();
		return true;
	} else {
		return false;
	}

}

	//------------------------------------------------------------------//

function validateAddFood(formId,foodDesc) {

	var myForm = getElement(formId);
	var myFoodDesc = getElement(foodDesc);

	if ( empty(myFoodDesc.value) ) {
		var msg = "You must give the food a description.";
		myForm.description.focus();
		alert(msg);
		return false;
	}

	if ( myForm.action.value == "addFoodToDiary" ) {
		if ( empty(myForm.diaryTimestamp.value) ) {
			var msg = "You must specify a timestamp.";
			alert(msg);
			return false;
		}
	}

	return true;

}

	//------------------------------------------------------------------//

function validateAddMeal(formId,mealDesc) {

	var myForm = getElement(formId);
	var myMealDesc = getElement(mealDesc);

	if ( empty(myMealDesc.value) ) {
		var msg = "You must give the recipe a description.";
		myForm.description.focus();
		alert(msg);
		return false;
	}

	if ( myForm.action.value == "addMealToDiary" ) {
		if ( empty(myForm.diaryTimestamp.value) ) {
			var msg = "You must specify a timestamp.";
			alert(msg);
			return false;
		}
	}

	return true;

}

	//------------------------------------------------------------------//

function verifyRemoveMealItem(mealItem) {

	var itemDesc = getElement("mealItemDesc-" + mealItem).value;
	var msg = "Are you sure you want to permanently delete this item from the recipe?\n\n" + itemDesc;

	if ( window.confirm(msg) ) {
		xajax_removeMealItem(mealItem);
	} else {
		return false;
	}

}
	//------------------------------------------------------------------//

function verifyRemoveDiaryItem(diaryItem) {

	var itemDesc = getElement("itemDesc-" + diaryItem).innerHTML;
	var msg = "Are you sure that you want to permanently delete this diary item?\n\n" + itemDesc;

	if ( window.confirm(msg) ) {
		xajax_removeDiaryItem(diaryItem);
	} else {
		return false;
	}

}

	//------------------------------------------------------------------//

function loadFoodToEdit(food) {

	xajax_loadFoodToEdit(food);

	return true;

}

	//------------------------------------------------------------------//

function loadMealToEdit(meal) {

	xajax_loadMealToEdit(meal);

	return true;

}

	//------------------------------------------------------------------//

function validateAddDiaryNote(formId) {

	var myForm = getElement(formId);
	
	if ( empty(myForm.note.value) ) {
		var msg = "You cannot add an empty note.";
		myForm.note.focus();
		alert(msg);
		return false;
	}

	return true;

}

	//------------------------------------------------------------------//

function highlightSysMsgBox() {

	getElement("systemMsgs").style.color = "#ffffff";
	getElement("systemMsgs").innerHTML = "System messages will appear here.";

}

	//------------------------------------------------------------------//

function unhighlightSysMsgBox() {

	getElement("systemMsgs").style.color = "#000000";
	getElement("systemMsgs").innerHTML = "";

}
