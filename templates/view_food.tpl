{$header}
<div id='columnContainer'>
	<div id='middleColumn'>
		<div id='middleData'>
			<div style='margin-bottom: 1em;'>
{if $foodDesc}
				<strong>Food</strong>: {$foodDesc} ({$foodData[0].foodDesc})<br />
{else}
				<strong>Food</strong>: {$foodData[0].foodDesc}<br />
{/if}
{if ! empty($foodData[0].sciname)}
				<strong>Scientific name</strong>: {$foodData[0].sciname}<br />
{/if}
				<strong>Quantity</strong>: {$quantity} {$foodData[0].msre_desc} (about {$gramWeight} grams)
			</div>
			<div style='margin-bottom: 1em;'>
{if $showAllNutrients}
				<a href='{$config->_rootUri}/{$config->_thisScript}?food={$food}&amp;weight={$weight}&amp;quantity={$quantity}'>
				    Hide unwanted nutrients</a>
{else}
				<a href='{$config->_rootUri}/{$config->_thisScript}?food={$food}&amp;weight={$weight}&amp;quantity={$quantity}&amp;showall'>
					Show all nutrients</a>
{/if}
{if $isLoggedIn}
				| <a href='nutrient_chooser'>Manage nutrient list</a>
{/if}
			</div>
			<div style='width: 100%;'>
				<table class='standardTable'>
					<tr class='tableTitleRow'>
						<th>Nutrient</th>
						<th>Quantity</th>
						<th>&#37;DRI</th>
					</tr>

{foreach from=$foodData item=nutrient}
					<tr class='{cycle values="bgDark,bgLight"}'>
						<td>{$nutrient.nutrdesc}</td>
						<td style='text-align: center;'>{$nutrient.nutrientQuantity}{$nutrient.units}</td>
	{if $nutrient.percentDri != "--"}
						<td style='text-align: center;'>{$nutrient.percentDri}&#37;</td>
	{else}
						<td style='text-align: center;'>--</td>
	{/if}
					</tr>
{/foreach}
				</table>
			</div>
			<div style='margin-top: 1em;'>
				<form action='add_food' method='post' id="formAddFood" onsubmit='return validateAddFood("formAddFood","foodDesc");'>
{if $isLoggedIn} 
					<div style='margin-bottom: .5em; text-align: justify;'>
						If you would like to save this item for later reference, or add it
						to a recipe or diary, enter a short descriptive entry in the
						text box below and then select the appropriate button.  A default
						description may have been added for you.  However, this description may
						not be very helpful and could possibly be quite long.  You are
						encouraged to change this to something more meaningful, and possibly
						shorter.  Are you still <a href='faq#saving'>confused</a>?
					</div>
					<div style='margin-bottom: .5em;'>
	{if $foodDesc}
						<input type='text' name='description' id='foodDesc' size='25' value='{$foodDesc|escape:'html'}' />
	{else}
						<input type='text' name='description' id='foodDesc' size='25' value='{$quantity} {$foodData[0].msre_desc|escape:'html'} {$foodData[0].foodDesc|escape:'html'}' />
	{/if}
					</div>
					<div style='margin-bottom: .5em;'>
						<input type='submit' name='saveFood' id='saveFood' value='Save food' style='width: 20ex;' onclick='getElement("formAddFood").action.value = "saveFood";' />
					</div>
					<div style='margin-bottom: .5em;'>
						<input type='submit' name='addFoodToMeal' id='addFoodToMeal' value='Add to recipe =&gt;' style='width: 20ex;' onclick='getElement("formAddFood").action.value = "addFoodToMeal";' />
						<select name='meal'>
							<option selected='selected' value='0'>New recipe</option>
	{foreach from=$myMeals item=myMeal}
							<option value='{$myMeal.id}'>{$myMeal.description}</option>
	{/foreach}
						</select>
					</div>
	{if $userDiaries}
					<div style='margin-right: 1ex; margin-bottom: .5em; float: left;'>
						<input type='submit' name='addFoodToDiary' id='addFoodToDiary' value='Add to diary =&gt;' style='width: 20ex;' onclick='getElement("formAddFood").action.value = "addFoodToDiary";' />
						<select name='diary'>
		{foreach from=$userDiaries item=userDiary}
							<option value='{$userDiary.id}'>{$userDiary.description}</option>
		{/foreach}
						</select>
					</div>
					<div style='margin-bottom: .5em;'>
						with
						<a href='faq#timestamp' title='What is a diary timestamp?'>timestamp</a>
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
{else}
					<div style='margin-bottom: .5em; text-align: justify;'>
						Would you like to add this food to a recipe?  Enter a short
						descriptive entry in the text box below and then click the
						"Add to recipe" button.  A default description may have been
						added for you.  However, this description may not be very
						helpful and could possibly be quite long.  You are encouraged
						to change this to something more meaningful, and possibly
						shorter.  Are you still <a href='faq#saving'>confused</a>?
					</div>
					<div style='margin-bottom: .5em;'>
	{if $foodDesc}
						<input type='text' name='description' size='25' value='{$foodDesc|escape:'html'}' />
	{else}
						<input type='text' name='description' size='25' value='{$quantity} {$foodData[0].msre_desc|escape:'html'} {$foodData[0].foodDesc|escape:'html'}' />
	{/if}
					</div>
					<div style='margin-bottom: .5em;'>
						<input type='submit' name='addFoodToMeal' value='Add to recipe' style='width: 20ex;' />
						<input type='hidden' name='meal' value='0' />
					</div>
{/if}
					<div>
						<input type='hidden' name='food' value='{$food}' />
						<input type='hidden' name='weight' value='{$weight}' />
						<input type='hidden' name='quantity' value='{$quantity}' />
						<input type='hidden' name='action' value='' />
					</div>
				</form>
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
