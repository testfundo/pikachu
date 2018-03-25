{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<div>
				<strong>Nutrient</strong>: {$nutrientName}<br />
			</div>
{if isset($searchResults)}
			<div style='margin-top: 2ex;'>
				The following ({$smarty.get.count}) foods have the highest "{$nutrientName}" content in the database.<br />
				The results are in decending order (highest quantity first).<br />
				In parenthesis is the quantity of "{$nutrientName}" per 100g of the displayed item.<br />
				Please select one.
			</div>
			<div style='margin-top: 2ex;'>
				<ol>
	{foreach from=$searchResults item=searchResult}
					<li><a href='food_quantity?food={$searchResult.food}'>({$searchResult.nutr_val}{$searchResult.units}) {$searchResult.foodDesc}</a><br /></li>
	{/foreach}
				</ol>
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
