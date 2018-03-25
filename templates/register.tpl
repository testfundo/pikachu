{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>

			<h3 style='text-align: center;'>Register</h3>
			<div style='text-align: center;'> <!-- this outer div here is to make IE6 center align a box correctly -->
				<div class='registerDiv'>
					<div class='standardMargins' style='text-align: justify;'>
						Please complete the following form to register with nutridb.org.  It is fairly
						important that your birthday and gender be correct because the DRIs (Dietary Reference Intakes -
						a newer version of the RDAs (Recommended Dietary Allowances)) are age/gender specific, so
						if you give incorrect values here, then the returned DRIs may be incorrect for you in
						particular.  <strong>NOTE</strong>: nutridb.org does not request your real name, an email
						address, or any other personal/private information, nor does nutridb.org want this
						information.  Below you simply need to select a unique login name, a password and basic
						age/gender information.  For more information see the help regarding
						<a href='faq#whyregister'>why you might want to register</a>.
					</div>
				</div>
				<div class='registerDiv'>
					<form action='register' method='post' id='formRegisterUser' onsubmit='validateRegisterUser("formRegisterUser"); return false;'>
						<div class='standardMargins'>
							<input type='text' name='username' size='25' /> <strong>Login name</strong>
							(min. 5 chars.)
						</div>
						<div class='standardMargins'>
							<input type='password' name='password' size='25' /> <strong>Password</strong>
							(min. 5 chars.)
						</div>
						<div class='standardMargins'>
							<input type='password' name='password2' size='25' /> <strong>Confirm password</strong>
							(min. 5 chars.)
						</div>
						<div class='standardMargins'>
							<input type='text' name='birthday' id='birthday' readonly='readonly' value='' /> <strong>Birthday</strong>
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
								<option value='Female' selected='selected'>Female</option>
								<option value='Male'>Male</option>
							</select> <strong>Gender</strong>
						</div>
						<div class='standardMargins'>
							<input type='checkbox' name='terms' value='accepted' />
							I have read, fully understand, and accept the <a href='{$smarty.server.REQUEST_URI}' onclick='openInNewWindow("terms.html"); return false;'>
                  Terms &amp; Conditions</a> of use.
						</div>
						<div class='standardMargins'>
							<input type='hidden' name='action' value='' />
							<input type='submit' name='doRegister' value='Register' onclick='getElement("formRegisterUser").action.value = "registerUser";' />
						</div>
					</form>
				</div>
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
