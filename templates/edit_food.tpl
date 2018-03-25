{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<h3 style='text-align: center;'>Edit Foods</h3>
			<div style='float: left; padding-right: 2ex; width: 20%;'>
				<div><span style='text-decoration: underline;'><strong>Saved foods</strong></span></div>
{if $savedFoods}
	{foreach from=$savedFoods item=savedFood}
				<div name='savedFoods' id='savedFood-{$savedFood.id}'>
					<a href='{$smarty.server.REQUEST_URI}' title='{$savedFood.description|escape:"html"}' onclick='loadFoodToEdit("{$savedFood.id}"); return false;'>{$savedFood.description|escape:"html"|truncate:25:" ..."}</a>
				</div>
	{/foreach}
{else}
				No saved foods.
{/if}
			</div>
			<div id='editFood' style='float: left; padding-left: 2ex; border-left: 1px solid black; width: 75%;'>
{if $editFood}
				<script type='text/javascript'>xajax_loadFoodToEdit("{$editFood}");</script>
{else}
				&lt;= Select a food to edit.
{/if}
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
