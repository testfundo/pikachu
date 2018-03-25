		Current meal items:<br />
{if isset($currentMealItems)}
			<form action='view_meal.php' method='post'>
	{foreach from=$currentMealItems key=key item=currentMealItem}
				<span class='indent'> =&gt; {$currentMealItem} (<a href='edit_new_meal.php?rm_item={$key}' title='Remove meal item'>Del</a>)</span>
				<br />
				<input type='hidden' name='source' value='new' />
				<input type='submit' name='view_new_meal' value='View Meal' />
				<a href='edit_new_meal.php?clear_meal=yes' title='Remove all meal items'>Clear Meal</a>
			</form>
	{/foreach}
{else}
			(No items in current meal.)
{/if}

		<br /><br />
{if isset($isLoggedIn)}
		Saved items:<br />
	{if isset($myFoods)}
		<form action='food_data.php' method='post' id='frmMyFoods'>
			<select name='my_food' style='width: 70%;' onchange='return submitForm("frmMyFoods");'>
				<option value=''> -- Select -- </option>
		{foreach from=$myFoods item=myFood}
				<option value='{$myFood.id_my_foods}'>{$myFood.my_desc}</option>
		{/foreach}
	    	</select>
			Foods
			<input type='hidden' name='source' value='food' />
		</form>
	{else}
		No saved foods.
	{/if}

	{if isset($myMeals)}
		<form action='view_meal.php' method='post' id='frmMyMeals'>
			<select name='meal_id' style='width: 70%;' onchange='return submitForm("frmMyMeals");'>
				<option value=''> -- Select -- </option>
		{foreach from=$myMeals item=myMeal}
				<option value='{$myMeal.id_my_meals}'>{$myMeal.meal_desc}</option>
		{/foreach}
	    	</select>
			Meals
			<input type='hidden' name='source' value='saved' />
		</form>
	{else}
		No saved meals.
	{/if}
  
	{if isset($myDiaries)}
		<form action='view_diary.php' method='post' id='frmMyDiaries'>
			<select name='id_my_diaries' style='width: 70%;' onchange='return submitForm("frmMyDiaries");'>
				<option value=''> -- Select -- </option>
		{foreach from=$myDiaries item=myDiary}
				<option value='{$myDiary.id_my_diaries}'>{$myDiary.diary_desc}</option>
		{/foreach}
	    	</select>
			Meals
		</form>
		Diaries
	{else}
		No saved diaries.
	{/if}
		<p><a href='my_stuff.php'>Manage my account</a>.</p>
{/if}
  
