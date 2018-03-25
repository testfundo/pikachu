<!DOCTYPE html>
	
<html lang='en'>

<head>{$myHeaders}</head>

{if $config->_thisScript == "/"}
<body onload='getElement("searchForm").searchString.focus();'>
{else}
<body>
{/if}

<div id='header'>
	<div id='headerLeft'>
		<a href='{$config->_rootUri}/' style='color: #ffffff;'>Nutri<span style="color: #b5e7bd;">DB</span></a>
		<span style="font-size: 75%;">... an online food and recipe nutrition calculator</span>
		
	</div>

{if isset($isLoggedIn)}
	<div id='headerRight'>
		Hi {$smarty.session.user.username|escape:"html"}.<br />
		[<a href='logout'>Logout</a>]

	</div>
{else}
	<div id='headerRight'>
		<form action='login' method='post' id='loginForm' onsubmit='return validateLoginFields();'>
			<div id='loginLeft'>
				Login <input type='text' name='username' id='username' size='15' maxlength='25' /><br />
				Password <input type='password' name='password' id='password' size='15' maxlength='25' />
			</div>
			<div id='loginRight'>
				<input type='submit' name='doLogin' value='Login' style='margin-bottom: 1ex;' /><br />
				<a href='register'>Register</a>.
			</div>
		</form>
	</div>
{/if}

	<div id='headerLinkBar'>
		<div id='headerLinks'>
			<a href='{$config->_rootUri}' title='Home/Search Page'>Search</a> &nbsp; | &nbsp;
			<a href='about' title='About nutridb.org'>About</a> &nbsp; | &nbsp;
			<a href='guide' title='A brief guide'>Guide</a> &nbsp; | &nbsp;
			<a href='faq' title='FAQ'>FAQ</a> &nbsp; | &nbsp;
			<a href='download' title='Download'>Download</a> &nbsp; | &nbsp;
			<a href='contact' title='Contact'>Contact</a>
		</div>
		<div id='systemMsgs'>{$systemMsg}</div>
	</div>
</div>


