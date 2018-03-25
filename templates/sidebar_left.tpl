		<div><strong>Current recipe</strong>:</div>
		<div id='divCurrentMeal'>
{if $currentMealItems}
	{foreach from=$currentMealItems key=key item=currentMealItem}
			<div id='currentMealItem-{$key}'>
				<a href='{$smarty.server.REQUEST_URI}' onclick='verifyRemoveCurrentMealItem("{$key}"); return false;' title='Remove: {$currentMealItem.description|escape:"html"}'><img src='{$config->_imgUri}/remove.png' alt='(Del)' /></a>
				<a href='view_food?food={$currentMealItem.food}&amp;weight={$currentMealItem.weight}&amp;quantity={$currentMealItem.quantity}&amp;description={$currentMealItem.description|escape:"url"}&amp;action=viewFood' id='currentMealItemDesc-{$key}' title='View this item'>{$currentMealItem.description|escape:"html"}</a>
			</div>
	{/foreach}
			<div style='margin-top: 1ex;'>
				<a href='view_meal?meal=0&amp;action=getMeal' title='View current recipe'>View recipe</a> |
				<a href='{$smarty.server.REQUEST_URI}' onclick='verifyClearCurrentMeal(); return false;' title='Remove all recipe items'>Clear recipe</a>
			</div>
{else}
			No items in recipe.
{/if}
		</div>
{if $isLoggedIn}
		<div style='margin-top: 2ex;'><strong>Favorites:</strong></div>
		<div style='margin-top: 1ex;'>
	{if $favFoods}
			<form action='view_food' method='post' id='frmFavFoods' style='margin: 0;'>
				<select name='queryString' style='width: 100%;' onchange='return submitForm("frmFavFoods");'>
					<option value=''> -- Foods -- </option>
					<option value='viewAllFoods'>[View All]</option>
		{foreach from=$favFoods item=favFood}
					<option value='food={$favFood.food}&amp;weight={$favFood.weight}&amp;quantity={$favFood.quantity}&amp;description={$favFood.description|escape:'url'}&amp;action=viewFood'>{$favFood.description|escape:'html'}</option>
		{/foreach}
				</select>
				<input type='hidden' name='action' value='viewFood' />
			</form>
	{else}
			* No favorite foods.<br />
	{/if}
		</div>
		<div style='margin-top: 1ex;'>
	{if $favMeals}
			<form action='view_meal' method='post' id='frmFavMeals' style='margin: 0;'>
				<select name='meal' style='width: 100%;' onchange='return submitForm("frmFavMeals");'>
					<option value=''> -- Recipes -- </option>
					<option value='viewAllMeals'>[View All]</option>
		{foreach from=$favMeals item=favMeal}
					<option value='{$favMeal.id}'>{$favMeal.description|escape:'html'}</option>
		{/foreach}
				</select>
				<input type='hidden' name='action' value='viewMeal' />
			</form>
	{else}
			* No favorite recipes.<br />
	{/if}
		</div>
		<div style='margin-top: 1ex;'>
	{if $userDiaries}
			<form action='view_diary' method='post' id='frmFavDiaries' style='margin: 0;'>
				<select name='diary' style='width: 100%;' onchange='return submitForm("frmFavDiaries");'>
					<option value=''> -- Diaries -- </option>
					<option value='viewAllDiaries'>[View All]</option>
		{foreach from=$userDiaries item=userDiary}
					<option value='{$userDiary.id}'>{$userDiary.description|escape:'html'}</option>
		{/foreach}
				</select>
				<input type='hidden' name='action' value='viewDiary' />
			</form>
	{else}
			* No diaries.
	{/if}
		</div>
		<div style='margin-top: 2ex;'>
				<a href='manage'>Manage account</a>.
		</div>
{/if}
