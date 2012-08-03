<?php

include('cd.php');
$UserName = null;
$EmailAddress = null;
$Hash = null;
$HashError = false;
$MailSent = null;


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] && $_POST['hidAction'] == 'PasswordPassword')
{
	$UserName = $_POST['txtUserName'];
	$EmailAddress = $_POST['txtEmailAddress'];

	$Users = User::GetUsers(new UserSearchParameters(
		null,
		null,
		($UserName ? $UserName : Utils::UUID()),
		null,
		($EmailAddress ? $EmailAddress : Utils::UUID())
	));
	
	if($Users)
	{
		/* @var $User User */
		$User = $Users[0];

		$ReturnURL = sprintf("http://%1\$s%2\$s?Hash=%3\$s",
			$_SERVER['HTTP_HOST'],
			$_SERVER['PHP_SELF'],
			$User->getPassword()
		);

		$ml->AddAddress($User->getEmailAddress(), $User->GetFullName());
		$ml->Subject = 'Reset your CandyDoll DB password';
		$ml->Body = sprintf($MailTemplateResetPassword, $User->GetFullName(), $ReturnURL);

		$MailSent = $ml->Send();

		if(!$MailSent)
		{
			$e = new Error(null, $ml->ErrorInfo);
			Error::AddError($e);
		}
	}
	else
	{
		$e = new LoginError(LOGIN_ERR_USERNAMEANDMAILADDRESNOTFOUND);
		Error::AddError($e);
	}
}
else if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] && $_POST['hidAction'] == 'PasswordReset' && array_key_exists('Hash', $_GET) && preg_match('/^[0-9a-f]{128}$/i', $_GET['Hash']))
{
	$Hash = $_GET['Hash'];

	$Users = User::GetUsers(new UserSearchParameters(
		null,
		null,
		null,
		($Hash ? $Hash : Utils::UUID())
	));
	
	if($Users)
	{
		/* @var $User User */
		$User = $Users[0];

		if($_POST['txtNewPassword'] && $_POST['txtRepeatPassword'] && $_POST['txtNewPassword'] == $_POST['txtRepeatPassword'])
		{
			$User->setSalt(Utils::GenerateGarbage(20));
			$User->setPassword(Utils::HashString($_POST['txtNewPassword'], $User->getSalt()));

			$User->setPreLastLogin($User->getLastLogin());
			$User->setLastLogin(time());
			User::Update($User, $User);
				
			$_SESSION['CurrentUser'] = serialize($User);
			header('location:index.php');
			exit;
		}
		else
		{
			if(!$_POST['txtNewPassword'] && !$_POST['txtRepeatPassword'])
			{
				$e = new Error(REQUIRED_FIELD_MISSING);
				Error::AddError($e);
			}
			else
			{
				$e = new LoginError(LOGIN_ERR_PASSWORDSNOTIDENTICAL);
				Error::AddError($e);
			}
		}
	}
	else
	{
		header('location:login.php');
		exit;
	}
}
else if (!array_key_exists('hidAction', $_POST) && array_key_exists('Hash', $_GET) && preg_match('/^[0-9a-f]{128}$/i', $_GET['Hash']))
{
	$Hash = $_GET['Hash'];

	$Users = User::GetUsers(new UserSearchParameters(
		null,
		null,
		null,
		$Hash ? $Hash : Utils::UUID()
	));
	
	if($Users)
	{
		/* @var $User User */
		$User = $Users[0];
	}
	else
	{
		$e = new LoginError(LOGIN_ERR_RESETCODENOTFOUND);
		Error::AddError($e);
		$HashError = true;
	}
}

echo HTMLstuff::HtmlHeader($lang->g('NavigationResetYourPassword'))?>

<div class="CenterForm">

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
<fieldset>

<?php if(!$Hash && is_null($MailSent)) { ?>
<input type="hidden" id="hidAction" name="hidAction" value="PasswordPassword" />

<?php echo $lang->g('MessagePasswordReset')?>

<div class="FormRow">
<label for="txtUserName"><?php echo $lang->g('LabelUsername')?>: <em>*</em></label>
<input type="text" id="txtUserName" name="txtUserName" maxlength="50" value="<?php echo $UserName?>" />
</div>

<div class="FormRow">
<label for="txtEmailAddress"><?php echo $lang->g('LabelEmailAddress')?>: <em>*</em></label>
<input type="text" id="txtEmailAddress" name="txtEmailAddress" maxlength="254" value="<?php echo $EmailAddress?>" />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" id="btnSend" name="btnSend" value="<?php echo $lang->g('ButtonSend')?>" />
<input type="button" id="btnCancel" name="btnCancel" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='login.php';" />
</div>

<?php } ?>

<?php if($Hash && !$HashError){ ?>

<input type="hidden" id="hidAction" name="hidAction" value="PasswordReset" />

<?php echo $lang->g('MessagePasswordEnterRepeat')?>

<div class="FormRow">
<label><?php echo $lang->g('LabelUsername')?>:</label>
<span><?php echo htmlentities($User->getUserName())?></span>
</div>

<div class="FormRow">
<label for="txtNewPassword"><?php echo $lang->g('LabelNewPassword')?>: <em>*</em></label>
<input type="password" id="txtNewPassword" name="txtNewPassword" maxlength="50" />
</div>

<div class="FormRow">
<label for="txtRepeatPassword"><?php echo $lang->g('LabelRepeatPassword')?>: <em>*</em></label>
<input type="password" id="txtRepeatPassword" name="txtRepeatPassword" maxlength="50" />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" id="btnSend" name="btnSend" value="<?php echo $lang->g('ButtonReset')?>" />
<input type="button" id="btnCancel" name="btnCancel" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='login.php';" />
</div>

<?php } ?>

<?php if($HashError){ ?>

<?php echo $lang->g('MessagePasswordResetError')?>

<div class="FormRow">
<label>&nbsp;</label>
<input type="button" id="btnCancel" name="btnCancel" value="<?php echo $lang->g('ButtonReturn')?>" onclick="window.location='login.php';" />
</div>

<?php } ?>

<?php if(!is_null($MailSent) && $MailSent === true){ ?>

<?php echo $lang->g('MessagePasswordResetSuccess')?>

<div class="FormRow">
<label>&nbsp;</label>
<input type="button" id="btnCancel" name="btnCancel" value="<?php echo $lang->g('ButtonReturn')?>" onclick="window.location='login.php';" />
</div>

<?php } ?>

<?php if(!is_null($MailSent) && $MailSent === false){ ?>

<?php echo $lang->g('MessagePasswordResetSendError')?>

<div class="FormRow">
<label>&nbsp;</label>
<input type="button" id="btnCancel" name="btnCancel" value="<?php echo $lang->g('ButtonReturn')?>" onclick="window.location='login.php';" />
</div>

<?php } ?>

</fieldset>
</form>

</div>

<?php
echo HTMLstuff::HtmlFooter();
?>