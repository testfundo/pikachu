{$header}
<div id='columnContainer'>
	<div id='middleColumn'>
		<div id='middleData'>
			<div class='standardMargins'>
				<strong>Diary</strong>: {$diaryDesc}
			</div>

			<div id='diaryCalendar' style='width: 15em; margin-bottom: 2ex; margin-right: 2ex; float: left;'></div>
			<script type='text/javascript'>
{literal}
				function dateChanged(calendar) {
					if (calendar.dateClicked) {
						var y = calendar.date.getFullYear();
						var m = calendar.date.getMonth();
						var d = calendar.date.getDate();
{/literal}
						window.location = "view_diary?diary={$smarty.get.diary}&action=viewDiary&date=" + y + "-" + (m + 1) + "-" + d;
{literal}
					}
				}
				Calendar.setup(
					{
						flat         : "diaryCalendar", // ID of the parent element
						flatCallback : dateChanged,           // our callback function
{/literal}
						range : [{$firstYear},{$lastYear}],
						date : "{$calendarStartDate}"
{literal}
					}
				);
{/literal}
			</script>

			<div style='float: left;'>
				<div style='margin-bottom: 2ex;'>
					Navigate this diary with the calendar.
				</div>
				<form action='edit_diary' method='post' id='formEditDiary' onsubmit='return validateAddDiaryNote("formEditDiary");'>
					<div><strong>Add a note to this diary:</strong></div>
					<textarea name='note' rows='3' style='width: 97%;'></textarea>
					<div style='margin-bottom: .5em;'>
						<input type='hidden' name='action' value='addNote' />
						<input type='hidden' name='diary' value='{$smarty.get.diary}' />
						<input type='submit' name='doAddNote' value='Add note' />
						with
						<a href='faq#timestamp' title='What is a diary timestamp?'>timestamp</a>
						<input type='text' name='diaryTimestamp' id='diaryTimestamp' readonly='readonly' value='{$smarty.get.date|date_format:"%Y-%m-%d"}' size='20' />
						<script type="text/javascript">
							Calendar.setup(
								{literal}{{/literal}
									inputField	: "diaryTimestamp",
									ifFormat	: "%Y-%m-%d %I:%M%p",
									button		: "diaryTimestamp",
									weekNumbers	: false,
									showsTime	: true,
									firstDay	: 0
								{literal}}{/literal}
							);
						</script>
					</div>
					<div>
						<small>TIP: leave the timestamp blank to use the present date/time.</small>
					</div>
				</form>
			</div>
			<div style='clear: both;'></div>
			
{if $diaryItems}
			<div class='standardMargins'>
				<strong>Diary entries for {$smarty.get.date|date_format:"%A, %B %e, %Y"}.</strong><br />
			</div>

			<div class='standardMargins' style='width: 100%;'>
				<table class='standardTable'>
					<tr class='tableTitleRow'>
						<th>Item</th>
						<th>Date</th>
						<th>Type</th>
						<th>X</th>
					</tr>
	{foreach from=$diaryItems item=diaryItem}
					<tr class='{cycle values="bgDark,bgLight"}' id='itemRow-{$diaryItem.id}'>
		{if $diaryItem.type == "Food"}
						<td><a href='view_food?{$diaryItem.uri}' id='itemDesc-{$diaryItem.id}'>{$diaryItem.description}</a></td>
		{elseif $diaryItem.type == "Meal"}
						<td><a href='view_meal?{$diaryItem.uri}' id='itemDesc-{$diaryItem.id}'>{$diaryItem.description}</a></td>
		{elseif $diaryItem.type == "Note"}
						<td><span id='itemDesc-{$diaryItem.id}'>{$diaryItem.data}</span></td>
		{/if}
						<td style='text-align: center;'>{$diaryItem.date}</td>
						<td style='text-align: center;'>{$diaryItem.type}</td>
						<td style='text-align: center;'>
							<a href='{$smarty.server.REQUEST_URI}' onclick='verifyRemoveDiaryItem("{$diaryItem.id}"); return false;'>
								<img src='{$config->_imgUri}/remove.png' alt='Del' title='Remove: {$diaryItem.description}' />
							</a>
						</td>
					</tr>
	{/foreach}
				</table>
			</div>

	{if $summaryData}
			<div class='standardMargins' style='width: 100%'>
				<strong>Nutrient summary.</strong><br />
		{if $showAllNutrients}
				<a href='{$config->_rootUri}/{$config->_thisScript}?diary={$smarty.get.diary}&action={$smarty.get.action}&date={$smarty.get.date}'>
				    Hide unwanted nutrients</a> |
		{else}
				<a href='{$config->_rootUri}/{$config->_thisScript}?diary={$smarty.get.diary}&action={$smarty.get.action}&date={$smarty.get.date}&showall'>
					Show all nutrients</a> |
		{/if}
				<a href='nutrient_chooser'>Manage nutrient list</a>
			</div>

			<div class='standardMargins' style='width: 100%;'>
				<table class='standardTable'>
					<tr class='tableTitleRow'>
						<th>Nutrient</th>
						<th>Total</th>
						<th>%DRI</th>
					</tr>
		{foreach from=$summaryData item=nutrient}
					<tr class='{cycle values="bgDark,bgLight"}'>
						<td>{$nutrient.nutrientName}</td>
						<td style='text-align: center;'>{$nutrient.total}{$nutrient.units}</td>
			{if $nutrient.percentDri != "--"}
						<td style='text-align: center;'>{$nutrient.percentDri}&#37;</td>
			{else}
						<td style='text-align: center;'>--</td>
			{/if}
			{foreach from=$nutrient.quantities item=quantity}
						<td style='text-align: center;'>{$quantity}</td>
			{/foreach}
					</tr>
		{/foreach}
				</table>
			</div>
	{/if}
{else}
			<div class='standardMargins' style='text-align: justify;'>
				This day has no diary entries.  If you would like to add a food or a recipe, search for or view an item as
				you normally would and then use the form at the bottom of the nutrition summary page to add the item
				to a diary.  Using the "timestamp" field you can add a food or recipe to this day.  Here's a tip:  create 
				a collection of saved foods that you eat frequently and use them as the building blocks for diaries and recipes.
				If you want to add a note to this diary, you may do so with the form below.
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
