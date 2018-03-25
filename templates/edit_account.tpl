{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData' style='margin-left: 5ex; margin-right: 5ex;'>

			<h3 style='text-align: center;'>Edit Profile</h3>
			<div class='standardMargins' style='text-align: justify;'>
				Use this form to edit your account profile.  If you don't wish to change your
				password, then simply leave the password boxes empty.
<!--
				Use this form to edit your account profile, or delete the account entirely.
				<strong>NOTE</strong>: if you choose to delete the account, then be aware that
				any and all data (foods, recipes, diaries, etc.) associated with the account will
				also be permanently and irrevocably deleted.  This isn't a problem, but something
				that you should be aware of.  For this reason, you will be prompted twice as to
				whether you really want to delete the account.  If you don't wish to change your
				password, then simply leave the password boxes empty.
-->
			</div>
			<div>
				<form action='{$smarty.server.REQUEST_URI}' method='post' id='formEditUser' onsubmit='validateEditUser("formEditUser"); return false;'>
					<div class='standardMargins'>
						<input type='text' name='username' size='25' value='{$smarty.session.user.username}' /> <strong>Login name</strong>
						(min. 5 chars.)
					</div>
					<div class='standardMargins'>
						<input type='password' name='password' size='25' /> <strong>New password</strong>
						(min. 5 chars.)
					</div>
					<div class='standardMargins'>
						<input type='password' name='password2' size='25' /> <strong>Confirm password</strong>
						(min. 5 chars.)
					</div>
					<div class='standardMargins'>
						<input type='text' name='birthday' id='birthday' value='{$birthday}' readonly='readonly' /> <strong>Birthday</strong>
						<script type="text/javascript">
							Calendar.setup(
								{literal}{{/literal}
									inputField	: "birthday", // ID of the input field
									ifFormat	: "%Y-%m-%d", // the date format
									button		: "birthday", // ID of the button
									weekNumbers	: false,
									showsTime	: true,
									firstDay	: 0
								{literal}}{/literal}
							);
						</script>
					</div>
					<div class='standardMargins'>
						<select name='gender'>
{foreach from=$genders item=gender}
	{if $gender == $smarty.session.user.gender}
							<option value='{$gender}' selected='selected'>{$gender}</option>
	{else}
							<option value='{$gender}'>{$gender}</option>
	{/if}
{/foreach}
						</select> <strong>Gender</strong>
					</div>
					<div class='standardMargins'>
						<input type='hidden' name='action' value='' />
						<input type='submit' name='doEdit' value='Modify' onclick='getElement("formEditUser").action.value = "editUser";' />
<!--
						<input type='submit' name='doDelete' value='Delete' onclick='return verifyDeleteUser();' />
-->
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
