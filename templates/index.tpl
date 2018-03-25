{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<form action='food_search' method='post' id='searchForm' onsubmit='return validateSearchBox();'>
				<div class='standardMargins' style='text-align: justify;'>
					<span style="font-size: 110%; font-weight: bold;">Welcome to NutriDB.</span>
					NutriDB is a simple tool which allows you to view nutritional information
					for a particular food, or calculate the nutritional profile of a whole recipe.
				</div>
				<div class='standardMargins' style='text-align: justify;'>
					The default <a href='faq#searching'>search options</a> are fine for most
					people, but a few extras are added for anyone needing or wanting a bit of
					extra control.  Just type something in the search box and press the Search
					button to get started.
				</div>
				<div class='standardMargins' style='text-align: center;'>
					<strong>Search for</strong>:
					<input type='text' name='searchString' id='searchString' value='{$currentSearchString}' size='25' />
					<!--
						Apparently IE won't send the submit button's name/value pair when a form
						is submitted via the Enter key ... unless there are at least two text
						input fields.  This is documented all over the web.
					-->
					<input type='text' name='IEHack' value='IEHack: see note above' style='display: none;' />
					<input type='submit' name='doSearch' value='Search' />
				</div>
				<div class='standardMargins'>
					<strong>Category</strong>:<br />
					<select name='foodCat'>
{foreach from=$foodCats item=foodCat}
	{if $foodCat.fdgrp_cd == $currentFoodCat}
						<option value='{$foodCat.fdgrp_cd}' selected='selected'>{$foodCat.fdgrp_desc}</option>
	{else}
						<option value='{$foodCat.fdgrp_cd}'>{$foodCat.fdgrp_desc}</option>
	{/if}
{/foreach}
					</select>
				</div>
				<div class='standardMargins'>
					<strong>Type of search</strong>:<br />
{foreach from=$searchTypes item=searchType}
	{if $searchType == $currentSearchType}
					<input type='radio' name='searchType' value='{$searchType}' checked='checked' />{$searchType}<br />
	{else}
					<input type='radio' name='searchType' value='{$searchType}' />{$searchType}<br />
	{/if}
{/foreach}
				</div>
				<div class='standardMargins'>
					<strong>Type of word search</strong>:<br />
{foreach from=$wordTypes item=wordType}
	{if $wordType == $currentWordType}
					<input type='radio' name='wordType' value='{$wordType}' checked='checked' />{$wordType}<br />
	{else}
					<input type='radio' name='wordType' value='{$wordType}' />{$wordType}<br />
	{/if}
{/foreach}
				</div>
				<div class='standardMargins'>
					<strong>Sort order</strong>:<br />
{foreach from=$sortTypes item=sortType}
	{if $sortType == $currentSortType}
					<input type='radio' name='sortType' value='{$sortType}' checked='checked' />{$sortType}<br />
	{else}
					<input type='radio' name='sortType' value='{$sortType}' />{$sortType}<br />
	{/if}
{/foreach}
				</div>
			</form>

			<hr />

			<div class='standardMargins'>
				Alternatively, you can search for the top 50 foods in the database containing
				the highest concentrations of the specified nutrient.
			</div>

			<form action='nutrient_search' method='post' id='nutrientSearchForm'>
				<div>
					<select name='nutrient'>
{foreach from=$nutrientList item=nutrient}
						<option value='{$nutrient.nutr_no}'>{$nutrient.nutrdesc}</option>
{/foreach}
					</select>
					<input type='submit' name='doFindNutrients' value='Search' />
				</div>
			</form>
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
