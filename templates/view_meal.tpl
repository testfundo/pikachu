{$header}
<div id='columnContainer'>
	<div id='middleColumn'>
		<div id='middleData'>
			<div style='margin-bottom: 1em;'>
				<strong>Recipe</strong>: {$mealDesc}
{if $isLoggedIn && $meal != "0"}
				[<a href='edit_meal?meal={$meal}&action=showMeals'>Edit</a>]
{/if}
			</div>
			<div style='margin-bottom: 1em;'>
{if $showAllNutrients}
				<a href='{$config->_rootUri}/{$config->_thisScript}?meal={$meal}&action={$smarty.get.action}'>
				    Hide unwanted nutrients</a>
{else}
				<a href='{$config->_rootUri}/{$config->_thisScript}?meal={$meal}&action={$smarty.get.action}&showall'>
					Show all nutrients</a>
{/if}
{if $isLoggedIn}
				| <a href='nutrient_chooser'>Manage nutrient list</a>
{/if}
			</div>
			<div style='width: 100%;'>
				<table class='standardTable'>
					<tr class='tableTitleRow'>
{foreach from=$mealData.columnTitles item=columnTitle}
						<th>{$columnTitle}</th>
{/foreach}
					</tr>
{foreach from=$mealData.nutrients item=nutrient}
					<tr class='{cycle values="bgDark,bgLight"}'>
						<td>{$nutrient.nutrientName}</td>
						<td style='text-align: center;'>{$nutrient.total}{$nutrient.units}</td>
	{if $nutrient.percentDri != "--"}
						<td style='text-align: center;'>{$nutrient.percentDri}&#37;</td>
	{else}
						<td style='text-align: center;'>--</td>
	{/if}
	{foreach from=$nutrient.quantities item=quantity}
						<td style='text-align: center;'>{$quantity}</td>
	{/foreach}
					</tr>
{/foreach}
				</table>
			</div>
{if $isLoggedIn} 
			<div style='margin-top: 1em;'>
				<form action='add_meal' method='post' name='formAddMeal' id='formAddMeal' onsubmit='return validateAddMeal("formAddMeal","mealDesc");'>
					<a name='save'></a>
					<div style='margin-bottom: .5em; text-align: justify;'>
						If you would like to save this recipe for later reference, or add it
						to a diary, enter a short descriptive entry in the text box below
						and then select the appropriate button.  If you are unsure how all
						of this works, then take a look at the help on
						<a href='faq#recipes'>creating recipes</a>.
					</div>
					<div style='margin-bottom: .5em;'>
						<input type='text' name='description' id='mealDesc' style='width: 100%' value='{$mealDesc}' />
					</div>
					<div style='margin-bottom: .5em;'>
						<input type='submit' name='saveMeal' value='Save recipe' style='width: 20ex;' onclick='document.formAddMeal.action.value = "saveMeal";'  />
					</div>

	{if $userDiaries}
					<div style='margin-right: 1ex; margin-bottom: .5em; float: left;'>
						<input type='submit' name='addMealToDiary' id='addMealToDiary' value='Add to diary =&gt;' style='width: 20ex;' onclick='document.formAddMeal.action.value = "addMealToDiary";' />
						<select name='diary'>
		{foreach from=$userDiaries item=userDiary}
							<option value='{$userDiary.id}'>{$userDiary.description}</option>
		{/foreach}
						</select>
					</div>
					<div style='margin-bottom: .5em;'>
						with <a href='faq#timestamp' title='What is a diary timestamp?'>timestamp</a>
						<input type='text' name='diaryTimestamp' id='diaryTimestamp' readonly='readonly' value='' />
						<script type="text/javascript">
							Calendar.setup(
								{literal}{{/literal}
									inputField	: "diaryTimestamp", // ID of the input field
									ifFormat	: "%Y-%m-%d %I:%M%p", // the date format
									button		: "diaryTimestamp", // ID of the button
									weekNumbers	: false,
									showsTime	: true,
									firstDay	: 0
								{literal}}{/literal}
							);
						</script>
					</div>
	{/if}
					
					<input type='hidden' name='meal' value='{$meal}' />
					<input type='hidden' name='action' value='' />
				</form>
			</div>
{/if}
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
