{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<h3 style='text-align: center;'>Diaries</h3>
{if $userDiaries}
	{if $diaryCount < 50}
			<div>
		{foreach from=$userDiaries item=userDiary name=userDiaries}
				<div>{$smarty.foreach.userDiaries.iteration}) <a href='view_diary?diary={$userDiary.id}&action=viewDiary'>{$userDiary.description|escape:'html'}</a></div>
		{/foreach}
			</div>
	{else}
			<div style='float: left; margin-right: 5ex;'>
		{foreach from=$userDiaries item=userDiary name=userDiaries}
			{math equation='ceil(x/2) + 1' x=$diaryCount assign=medianDiary}
			{if $smarty.foreach.userDiaries.iteration == $medianDiary}
			</div>
			<div style='float: left; width: 49%;'>
			{/if}
				<div>{$smarty.foreach.userDiaries.iteration}) <a href='view_diary?diary={$userDiary.id}&action=viewDiary'>{$userDiary.description|escape:'html'}</a></div>
		{/foreach}
			</div>
	{/if}
{else}
			<div>* No saved diaries.</div>
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
