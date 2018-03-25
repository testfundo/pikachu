{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<div>
				<strong>You selected</strong>: {$foodQuantities[0].foodDesc|escape}<br />
{if ! empty($foodQuantities[0].sciname)}
				<strong>Scientific name</strong>: <span style='text-decoration: italic;'>{$foodQuantities[0].sciname}</span>
{/if}
			</div>

			<div style='margin-top: 2ex;'>
				You must now choose a quantity for the selected food.  You may choose between
				various predefined quantities, or you may enter your own quantity.  If you
				enter your own quantity, any decimal number is allowable, including fractionals. 
			</div>

			<form action='view_food' method='post' id='formFoodQuantity' style='margin-top: 2ex;'>
				<div>
					<input type='radio' name='quantitySource' value='predefined' checked='checked' />
					Select a predefined quantity/weight:
				</div>
				<div style='margin-top: 2ex; margin-left: 5em;'>
{foreach from=$foodQuantities item=foodQuantity name=foodQuantity}
	{if $smarty.foreach.foodQuantity.index == 0}
					<input type='radio' name='predefinedWeight' value='{$foodQuantity.seq}' checked='checked' onfocus='return changeQuantitySource("formFoodQuantity", "0");' />
	{else}
					<input type='radio' name='predefinedWeight' value='{$foodQuantity.seq}' onfocus='return changeQuantitySource("formFoodQuantity", "0");' />
	{/if}
					{$foodQuantity.amount} {$foodQuantity.msre_desc} ({$foodQuantity.gm_wgt} grams)<br />
{/foreach}
				</div>
				<div style='margin-top: 2ex;'>
					<input type='radio' name='quantitySource' value='userdefined' />
					Enter your own quantity/weight:
				</div>
				<div style='margin-top: 2ex; margin-left: 5em;'>
					<input type='text' name='quantity' size='5' onfocus='return changeQuantitySource("formFoodQuantity", "1");'/>
					<select name='userdefinedWeight'>
{foreach from=$foodQuantities item=foodQuantity}
						<option value='{$foodQuantity.seq}'>{$foodQuantity.msre_desc}</option>
{/foreach}
					</select>
				</div>
				<div style='margin-top: 2ex;'>
					<input type='hidden' name='food' value='{$food}' />
					<input type='hidden' name='action' value='getFood' />
					<input type='submit' name='doGetFood' value='Proceed' />
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
