<?php

include('cd.php');
$UserName = null;
$Password = null;
$ReturnURL = null;

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] && $_POST['hidAction'] == 'LoginLogin')
{
	$UserName = $_POST['txtUserName'];
	$Password = $_POST['txtPassword'];
	$ReturnURL = array_key_exists('url', $_GET) && isset($_GET['url']) ? $_GET['url'] : null;
	
	$Users = User::GetUsers(new UserSearchParameters(null, null, $UserName));
	if($Users)
	{
		/* @var $User User */
		$User = $Users[0];
		if($User->hasPermission(RIGHT_ACCOUNT_LOGIN))
		{
			if(Utils::HashString($Password, $User->getSalt()) == $User->getPassword())
			{
				$User->setPreLastLogin($User->getLastLogin());
				$User->setLastLogin(time());

				// By resetting the user's Salt and Password-hash upon login,
				// existing reset-URLs and concurrent loginsessions become invalid.
				$User->setSalt(Utils::GenerateGarbage(20));
				$User->setPassword(Utils::HashString($Password, $User->getSalt()));

				User::Update($User, $User);

				$_SESSION['CurrentUser'] = serialize($User);
				session_regenerate_id(true);

				if(isset($ReturnURL))
				{ header('location:'.urldecode($ReturnURL)); }
				else
				{ header('location:index.php'); }

				exit;
			}
			else
			{
				$e = new LoginError(LOGIN_ERR_PASSWORDINCORRECT);
				Error::AddError($e);
			}
		}
		else
		{
			$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
			Error::AddError($e);
		}
	}
	else
	{
		$e = new LoginError(LOGIN_ERR_USERNAMENOTFOUND);
		Error::AddError($e);
	}
}

echo HTMLstuff::HtmlHeader($lang->g('NavigationLogIn'))?>

<div class="CenterForm">

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="LoginLogin" />

<div class="FormRow">
<label for="txtUserName"><?php echo $lang->g('LabelUsername')?>: <em>*</em></label>
<input type="text" id="txtUserName" name="txtUserName" maxlength="50" value="<?php echo $UserName?>" />
</div>

<div class="FormRow">
<label for="txtPassword"><?php echo $lang->g('LabelPassword')?>: <em>*</em></label>
<input type="password" id="txtPassword" name="txtPassword" maxlength="255" />
</div> 

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" id="btnLogin" name="btnLogin" value="<?php echo $lang->g('ButtonLogin')?>" />
<a href="password.php"><?php echo $lang->g('MessageForgotYourPassword')?></a> | <a href="#" onclick="$('#SiteInfo').slideToggle();return false;"><?php echo $lang->g('LabelInfo')?></a>
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

<?php echo $lang->g('MessageCDDBInfo')?>

<cite>
CandyDoll is the place where you can enjoy the beauty of little girls. We made collection of young and petite girls. You will definitely find these excellent photos worth to be called true masterpieces.
</cite>

<p><?php echo $lang->g('MessageEnjoy')?></p>

</div>

</div>

<?php
echo HTMLstuff::HtmlFooter();
?>