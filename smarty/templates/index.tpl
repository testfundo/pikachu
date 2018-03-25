{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<form action='food_match.php' method='post' name='foodmatch'>
				Select the type of search:<br />
				<input checked='checked' type='radio' name='stype' value='and' /> All Words<br />
				<input type='radio' name='stype' value='or' /> Any Words<br />
				<input type='radio' name='stype' value='=' /> Exact Phrase<br /><br />
				Select the type of word search:<br />
				<input checked='checked' type='radio' name='wtype' value='part' /> Partial Word<br />
				<input type='radio' name='wtype' value='full' /> Full Word<br /><br />
				Select a category:<br />
				<select name='fdgp_cd'>
					<option selected='selected' value='all'>All categories</option>
{foreach from=$fdGroups item=fdGroup} 
					<option value='{$fdGroup.fdgp_cd}'>{$fdGroup.fdgp_desc}</option>
{/foreach}
				</select><br /><br />
				Search for:
				<input type='text' name='whatFood' />
				<input type='submit' value='Search!' />
			</form>
			<a href='faq.php#search'>What do all these search options mean?</a>
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
