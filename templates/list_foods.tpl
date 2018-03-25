{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<h3 style='text-align: center;'>Saved Foods</h3>
{if $userFoods}
	{if $foodCount < 50}
			<div>
		{foreach from=$userFoods item=userFood name=userFoods}
				<div>{$smarty.foreach.userFoods.iteration}) <a href='view_food?food={$userFood.food}&weight={$userFood.weight}&quantity={$userFood.quantity}&description={$userFood.description|escape:'url'}&action=viewFood'>{$userFood.description|escape:'html'}</a></div>
		{/foreach}
			</div>
	{else}
			<div style='float: left; margin-right: 5ex;'>
		{foreach from=$userFoods item=userFood name=userFoods}
			{math equation='ceil(x/2) + 1' x=$foodCount assign=medianFood}
			{if $smarty.foreach.userFoods.iteration == $medianFood}
			</div>
			<div style='float: left; width: 49%;'>
			{/if}
				<div>{$smarty.foreach.userFoods.iteration}) <a href='view_food?food={$userFood.food}&weight={$userFood.weight}&quantity={$userFood.quantity}&description={$userFood.description|escape:'url'}&action=viewFood'>{$userFood.description|escape:'html'}</a></div>
		{/foreach}
			</div>
	{/if}
{else}
			<div>* No saved foods.</div>
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
