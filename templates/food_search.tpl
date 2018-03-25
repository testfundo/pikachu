{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<div>
				<strong>Search text</strong>: '{$searchString|escape:"html"}'<br />
				<strong>Search type</strong>: {$searchType|escape:"html"}/{$wordType|escape:"html"}<br />
				<strong>Category</strong>: {$foodCatName|escape:"html"}<br />
				<strong>Sort by</strong>: {$sortType|escape:"html"}
			</div>
{if isset($searchResults)}
			<div style='margin-top: 2ex;'>
				The following items matched your search.
				Select one, or <a href='/?{$smarty.server.QUERY_STRING|escape:"url"}'>refine your search</a>.
			</div>
			<div style='margin-top: 2ex;'>
	{if $sortType == "Category"}
		{foreach from=$searchResults key=category item=foodCat}
			<div style='text-align: center; background-color: #e0e0e0;'>{$foodCat.foodCatName}</div>
			{foreach from=$foodCat.searchResults item=searchResult}
				<div>
				{if $category == "userFood"}
					<a href='view_food?{$searchResult.food}&amp;description={$searchResult.foodDesc|escape:"url"}'>{$searchResult.foodDesc|escape:"html"}</a>
				{elseif $category == "userMeal"}
					<a href='view_meal?meal={$searchResult.food}&amp;description={$searchResult.foodDesc|escape:"url"}'>{$searchResult.foodDesc|escape:"html"}</a>
				{else}
					<a href='food_quantity?food={$searchResult.food}'>{$searchResult.foodDesc|escape:"html"}</a>
				{/if}
				</div>
			{/foreach}
		{/foreach}
	{else}
		{foreach from=$searchResults item=searchResult}
				<div>	
			{if $searchResult.category == "userFood"}
					<a href='view_food?{$searchResult.food}&amp;description={$searchResult.foodDesc|escape:"url"}'>{$searchResult.foodDesc|escape:"html"}</a>
			{elseif $searchResult.category == "userMeal"}
					<a href='view_meal?meal={$searchResult.food}&amp;description={$searchResult.foodDesc|escape:"url"}'>{$searchResult.foodDesc|escape:"html"}</a>
			{else}
					<a href='food_quantity?food={$searchResult.food}'>{$searchResult.foodDesc|escape:"html"}</a>
			{/if}
				</div>
		{/foreach}
	{/if}
			</div>
			<div class='pageNav'>	
				{$pageNav}
			</div>
{else}
			<div style='margin-top: 2ex;'>
				<span class='msgError'>No items matched your search.</span><br />
			</div>
			<div>
				Would you like to <a href='/?{$smarty.server.QUERY_STRING}'>refine your search</a>?
			</div>
			<div>
				Don't understand the search options?  See the <a href='faq#searching'>help</a> on searching.
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
