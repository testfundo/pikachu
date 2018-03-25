{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<h3 style='text-align: center;'>Nutrient Chooser</h3>
			<p style='text-align: justify;'>
				There is fairly bewildering array of nutrient data available for most of the foods
				in this database.  In most cases, you may only be concerned with a select few of
				these nutrients.  This form allows you to select which nutrients will be displayed
				in your food and recipe summaries.  Don't be afraid to uncheck too may options below.
				With every summary you will have the option to view information for all nutrients,
				including the ones you have selected to hide here.  Checked nutrients will be displayed
				in summaries.  Unchecked nutrients will be hidden.
			</p>
			<a href='javascript: checkAll("nutrients[]");'>Check all</a> |
			<a href='javascript: uncheckAll("nutrients[]");'>Uncheck all</a>
			<div style='width: 100%;'>
				<form action='{$smarty.server.REQUEST_URI}' method='post' id='nutrientChooser'>
					<table class='standardTable'>
						<tr class='tableTitleRow'>
							<th>Show/Hide</th>
							<th>Nutrient Description</th>
						</tr>
{foreach from=$nutrients item=nutrient}
						<tr class='{cycle values="bgDark,bgLight"}'>
	{if ! empty($nutrient.myNutrient)}
							<td style='text-align: center;'><input type='checkbox' name='nutrients[]' value='{$nutrient.nutr_no}' checked='checked' /></td>
	{else}
							<td style='text-align: center;'><input type='checkbox' name='nutrients[]' value='{$nutrient.nutr_no}' /></td>
	{/if}
							<td>{$nutrient.nutrdesc}</td>
						</tr>
{/foreach}
					</table>
					<br />
					<input type='submit' name='setNutrients' value='Save Changes'>
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
