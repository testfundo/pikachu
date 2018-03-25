<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>{$myHeaders}</head>

<body>

<div id='header'>
	<div id='headerLeft'>
		Nutrition Information Database
	</div>

{if isset($isLoggedIn)}
	<div id='headerMiddle'></div>
	<div id='headerRight'>
		Hi {$displayUserName|capitalize}.<br />
		[<a href='?logout'>Logout</a>]

	</div>
{else}
	<form action='login.php' method='post' name='loginForm' onsubmit='return validateNotEmpty("username,password");'>
		<div id='headerMiddle'>
			Login <input type='text' name='username' id='username' size='15' maxlength='25' /><br />
			Password <input type='password' name='password' id='password' size='15' maxlength='25' />
		</div>
		<div id='headerRight'>
			<input type='submit' name='doLogin' value='Login' style='margin-bottom: 1ex;' /><br />
			<a href='register.php'>Register</a>.
		</div>
	</form>
{/if}

	<div id='headerLinkBar'>
		<div id='headerLinks'>
			<a href='{$config->_rootUrl}/' title='Home Page'>Home</a> &nbsp; | &nbsp;
			<a href='faq.php'>Help</a>
		</div>
		<div id='systemMsgs'>{$systemMsg}</div>
	</div>
</div>


