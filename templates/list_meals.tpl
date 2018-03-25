{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<h3 style='text-align: center;'>Saved Meals</h3>
{if $userMeals}
	{if $mealCount < 50}
			<div>
		{foreach from=$userMeals item=userMeal name=userMeals}
				<div>{$smarty.foreach.userMeals.iteration}) <a href='view_meal?meal={$userMeal.id}&action=viewMeal'>{$userMeal.description|escape:'html'}</a></div>
		{/foreach}
			</div>
	{else}
			<div style='float: left; margin-right: 5ex;'>
		{foreach from=$userMeals item=userMeal name=userMeals}
			{math equation='ceil(x/2) + 1' x=$mealCount assign=medianMeal}
			{if $smarty.foreach.userMeals.iteration == $medianMeal}
			</div>
			<div style='float: left; width: 49%;'>
			{/if}
				<div>{$smarty.foreach.userMeals.iteration}) <a href='view_meal?meal={$userMeal.id}&action=viewMeal'>{$userMeal.description|escape:'html'}</a></div>
		{/foreach}
			</div>
	{/if}
{else}
			<div>* No saved recipes.</div>
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
