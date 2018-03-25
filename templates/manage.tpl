{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<h3 style='text-align: center;'>Manage Account</h3>
			<div><strong>Quick edit favorites</strong>:</div>
			<div style='border: 1px solid black;'>
{if $favFoods}
			<div style='padding: 1ex;'>
				<form action='edit_food' method='post' name='formQuickEditFood' id='formQuickEditFood' onsubmit='return validateEditFood("formQuickEditFood");'>
					<div style='float: left; margin-right: .5ex; overflow: auto;'>
						<strong>Foods:</strong><br />
						<select name='food' id='foodId'>
	{foreach from=$favFoods item=favFood}
							<option value='{$favFood.id}'>{$favFood.description|escape:'html'|truncate:50:" ..."}</option>
	{/foreach}
						</select>
					</div>
					<div style='float: left; margin-right: .5ex;'>
						<strong>Action:</strong><br />
						<select name='action' id='foodAction' onchange='return toggleShowRenameField("foodAction","renameFood");'>
							<option value='Edit'>Edit</option>
							<option value='Delete'>Delete</option>
							<option value='Rename'>Rename</option>
						</select>
					</div>
					<div id='renameFood' style='display: none; float: left; margin-right: .5ex;'>
						<strong>New name:</strong><br />
						<input type='text' name='newFoodName' id='newFoodName' size='20' />
					</div>
					<div style='float: left;'>
						&nbsp;<br />
						<input type='submit' name='doModifyFood' value='Go' />
					</div>
				</form>
			</div>
			<div style='clear: both;'></div>
{else}
			<div style='margin: 1ex;'>* No saved foods to manage.</div>
{/if}
{if $favMeals}
			<div style='padding: 1ex;'>
				<form action='edit_meal' method='post' name='formQuickEditMeal' id='formQuickEditMeal' onsubmit='return validateEditMeal("formQuickEditMeal");'>
					<div style='float: left; margin-right: .5ex; overflow: auto;'>
						<strong>Meals:</strong><br />
						<select name='meal' id='meal'>
	{foreach from=$favMeals item=favMeal}
							<option value='{$favMeal.id}'>{$favMeal.description|escape:"html"|truncate:50:" ..."}</option>
	{/foreach}
						</select>
					</div>
					<div style='float: left; margin-right: .5ex;'>
						<strong>Action:</strong><br />
						<select name='action' id='mealAction' onchange='return toggleShowRenameField("mealAction","renameMeal");'>
							<option value='Edit'>Edit</option>
							<option value='Rename'>Rename</option>
							<option value='Delete'>Delete</option>
						</select>
					</div>
					<div id='renameMeal' style='display: none; float: left; margin-right: .5ex;'>
						<strong>New name:</strong><br />
						<input type='text' name='newMealName' size='20' />
					</div>
					<div style='float: left;'>
						&nbsp;<br />
						<input type='submit' name='doModifyMeal' value='Go' />
					</div>
				</form>
			</div>
			<div style='clear: both;'></div>
{else}
			<div style='margin: 1ex;'>* No saved recipes to manage.</div>
{/if}
{if $userDiaries}
			<div style='margin: 1ex;'>
				<form action='edit_diary' method='post' name='formQuickEditDiary' id='formQuickEditDiary' onsubmit='return validateEditDiary("formQuickEditDiary");'>
					<div style='float: left; margin-right: .5ex; overflow: auto;'>
						<strong>Diaries:</strong><br />
						<select name='diary' id='diaryId'>
	{foreach from=$userDiaries item=userDiary}
							<option value='{$userDiary.id}'>{$userDiary.description|escape:"html"|truncate:50:" ..."}</option>
	{/foreach}
						</select>
					</div>
					<div style='float: left; margin-right: .5ex;'>
						<strong>Action:</strong><br />
						<select name='action' id='diaryAction' onchange='return toggleShowRenameField("diaryAction","renameDiary");'>
							<option value='Delete'>Delete</option>
							<option value='Rename'>Rename</option>
						</select>
					</div>
					<div id='renameDiary' style='display: none; float: left; margin-right: .5ex;'>
						<strong>New name:</strong><br />
						<input type='text' name='newDiaryName' id='newDiaryName' size='20' />
					</div>
					<div style='float: left;'>
						&nbsp;<br />
						<input type='submit' name='doModifyDiary' value='Go' />
					</div>
				</form>
			</div>
			<div style='clear: both;'>&nbsp;</div>
{else}
			<div style='margin: 1ex;'>* No saved diaries to manage.</div>
{/if}
			</div>

			<div style='margin-top: 2ex; margin-bottom: 2ex;'>
				<a href='edit_food?action=showFoods'>Edit foods</a> - use this section to edit any/all foods,
				not just favorites.
			</div>

			<div style='margin-top: 2ex; margin-bottom: 2ex;'>
				<a href='edit_meal?action=showMeals'>Edit recipes</a> - use this section to edit any/all recipes,
				not just favorites.
			</div>

			<div style='margin-top: 2ex; margin-bottom: 2ex;'>
				<form action='add_diary' method='post' onsubmit='return validateCreateDiary("newDiaryName");'>
					<div>
						<strong>Create</strong> a new diary named
						<input type='text' name='newDiaryName' id='newDiaryName' />
						<input type='submit' name='doCreateDiary' value='Go' />
					</div>
				</form>
			</div>
			<div style='margin-top: 2ex; margin-bottom: 2ex; text-align: justify;'>
				<a href='nutrient_chooser'>Edit your list of standard nutrients.</a>  There is
				a large number of nutrients available for most foods.  Usually you will not be
				concerned with the majority of them, but rather only a small percentage of the
				available nutrients.  Use this section to filter out the nutrients you do not want
				to see, though you will always have the option to view all nutrient data while
				viewing any food or recipe.
			</div>

			<div style='margin-top: 2ex; margin-bottom: 2ex; text-align: justify;'>
				<a href='edit_account'>Edit your profile.</a>  Here you can change
				things like your password, username, birthday or gender.
<!--
				<a href='edit_account'>Edit your profile.</a>  Here you can change
				things like your password, username, birthday, gender, or even delete your
				whole account and all the data associated with it.
-->
			</div>
		</div>
	</div>

	<div id='leftColumn'>
		<div id='leftData'>
			{$sidebar_left}
		</div>
	</div>

	<div id='rightColumn'>
		<div id='rightData'>
			{$sidebar_right}
		</div>
	</div>

</div>
{$footer}
