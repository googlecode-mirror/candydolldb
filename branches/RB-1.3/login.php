<?php

include('cd.php');
$UserName = null;
$Password = null;


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] && $_POST['hidAction'] == 'LoginLogin')
{
	$UserName = $_POST['txtUserName'];
	$Password = $_POST['txtPassword'];
	
	$WhereClause = sprintf("user_username = '%1\$s' AND mut_deleted = -1",
		mysql_real_escape_string($UserName)
	);
	
	$Users = User::GetUsers($WhereClause);
	if($Users)
	{
		/* @var $User User */
		$User = $Users[0];

		if(Utils::HashString($Password, $User->getSalt()) == $User->getPassword())
		{
			$User->setPreLastLogin($User->getLastLogin());
			$User->setLastLogin(time());
			
			// By resetting the user's Salt and Password-hash upon login,
			// existing reset-URLs and concurrent loginsessions become invalid.
			$User->setSalt(Utils::GenerateGarbage(20));
			$User->setPassword(Utils::HashString($Password, $User->getSalt()));
			
			User::UpdateUser($User, $User);
			
			$_SESSION['CurrentUser'] = serialize($User);
			header('location:index.php');
		}
		else
		{
			$LoginError = new LoginError();
			$LoginError->setErrorNumber(LOGIN_ERR_PASSWORDINCORRECT);
			$LoginError->setErrorMessage(LoginError::TranslateLoginError(LOGIN_ERR_PASSWORDINCORRECT));
			Error::AddError($LoginError);
		}	
	}
	else
	{
		$LoginError = new LoginError();
		$LoginError->setErrorNumber(LOGIN_ERR_USERNAMENOTFOUND);
		$LoginError->setErrorMessage(LoginError::TranslateLoginError(LOGIN_ERR_USERNAMENOTFOUND));
		Error::AddError($LoginError);
	}
}

echo HTMLstuff::HtmlHeader('Login'); ?>

<div class="CenterForm">

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<fieldset>
<legend>Please log in:</legend>

<input type="hidden" id="hidAction" name="hidAction" value="LoginLogin" />

<div class="FormRow">
<label for="txtUserName">Username: <em>*</em></label>
<input type="text" id="txtUserName" name="txtUserName" maxlength="50" value="<?php echo $UserName; ?>" />
</div>

<div class="FormRow">
<label for="txtPassword">Password: <em>*</em></label>
<input type="password" id="txtPassword" name="txtPassword" maxlength="255" />
</div> 

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" id="btnLogin" name="btnLogin" value="Login" />
<a href="password.php">Forgot your password?</a> | <a href="#" onclick="$('#SiteInfo').slideToggle();return false;">Info</a>
</div>

</fieldset>
</form>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	$('#SiteInfo').hide();
	$('input[type^=text]:first').focus();
});
//]]>
</script>

<div id="SiteInfo">

<p>This site is a tribute to the breathtaking beauty of the models shown on <a href="http://www.candydoll.tv/" rel="external">CandyDoll.tv</a> (キャンディドール), and to an awesome league of dedicated collectors throughout the internet.</p>

<p>In their own words:</p>

<cite>
<a href="http://www.candydoll.tv/" rel="external">CandyDoll</a> is the place where you can enjoy the beauty of little girls. We made collection of young and petite girls. You will definitely find these excellent photos worth to be called true masterpieces.
</cite>

<p>Enjoy!</p>

</div>

</div>

<?php echo HTMLstuff::HtmlFooter(); ?>