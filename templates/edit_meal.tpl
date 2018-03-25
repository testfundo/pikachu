{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<h3 style='text-align: center;'>Edit Meals</h3>
			<div style='float: left; padding-right: 2ex; width: 25%;'>
				<div>
					<span style='text-decoration: underline;'><strong>Saved recipes</strong></span>
				</div>
{if $savedMeals}
	{foreach from=$savedMeals item=savedMeal}
				<div name='savedMeals' id='savedMeal-{$savedMeal.id}'>
					<a href='{$smarty.server.REQUEST_URI}' title='{$savedMeal.description}' onclick='loadMealToEdit({$savedMeal.id}); return false;'>{$savedMeal.description|escape:"html"|truncate:25:" ..."}</a>
				</div>
	{/foreach}
{else}
				No saved recipes.
{/if}
			</div>
			<div style='float: left; overflow: auto; margin-bottom: 1em;'>
				<form action='edit_meal' method='post' name='formEditMeal' id='formEditMeal' style='onsubmit='return validateEditMeal("formEditMeal");'>
					<div id='editMeal' style='float: left; padding-left: 2ex; border-left: 1px solid black;'>
{if $editMeal}
						<script type='text/javascript'>xajax_loadMealToEdit("{$editMeal}");</script>
{else}
						&lt;= Select a recipe to edit.
{/if}
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
