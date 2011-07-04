<?php
/*	This file is part of CandyDollDB.

    CandyDollDB is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    CandyDollDB is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with CandyDollDB.  If not, see <http://www.gnu.org/licenses/>.
*/

include('cd.php');
$UserName = null;
$Hash = null;
$HashError = false;
$MailSent = null;


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] && $_POST['hidAction'] == 'PasswordPassword')
{
	$UserName = $_POST['txtUserName'];

	$WhereClause = sprintf("user_username = '%1\$s' AND mut_deleted = -1",
		mysql_real_escape_string($UserName)
	);

	$Users = User::GetUsers($WhereClause);
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
			$MailError = new Error();
			$MailError->setErrorMessage($ml->ErrorInfo);
			Error::AddError($MailError);
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
else if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] && $_POST['hidAction'] == 'PasswordReset' && array_key_exists('Hash', $_GET) && preg_match('/^[0-9a-f]{40}$/i', $_GET['Hash']))
{
	$Hash = $_GET['Hash'];

	$WhereClause = sprintf("user_password = '%1\$s' AND mut_deleted = -1",
		mysql_real_escape_string($Hash)
	);

	$Users = User::GetUsers($WhereClause);
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
			User::UpdateUser($User, $User);
				
			$_SESSION['CurrentUser'] = serialize($User);
			header('location:index.php');
		}
		else
		{
			$LoginError = new LoginError();
			$LoginError->setErrorNumber(LOGIN_ERR_PASSWORDSNOTIDENTICAL);
			$LoginError->setErrorMessage(LoginError::TranslateLoginError(LOGIN_ERR_PASSWORDSNOTIDENTICAL));
			Error::AddError($LoginError);
		}
	}
	else
	{
		header('location:login.php');
	}
}
else if (!array_key_exists('hidAction', $_POST) && array_key_exists('Hash', $_GET) && preg_match('/^[0-9a-f]{40}$/i', $_GET['Hash']))
{
	$Hash = $_GET['Hash'];

	$WhereClause = sprintf("user_password = '%1\$s' AND mut_deleted = -1",
		mysql_real_escape_string($Hash)
	);

	$Users = User::GetUsers($WhereClause);
	if($Users)
	{
		/* @var $User User */
		$User = $Users[0];
	}
	else
	{
		$LoginError = new LoginError();
		$LoginError->setErrorNumber(LOGIN_ERR_RESETCODENOTFOUND);
		$LoginError->setErrorMessage(LoginError::TranslateLoginError(LOGIN_ERR_RESETCODENOTFOUND));
		Error::AddError($LoginError);
		$HashError = true;
	}
}

echo HTMLstuff::HtmlHeader('Reset your password'); ?>

<div class="CenterForm">

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<fieldset><legend>Please log in:</legend>

<?php if(!$Hash && is_null($MailSent)) { ?>
<input type="hidden" id="hidAction" name="hidAction" value="PasswordPassword" />

<p>Please provide the username of the account for which you would like to reset the password. A hyperlink will then be sent to the account's email address which will enable you to reset the password.</p>

<div class="FormRow">
<label for="txtUserName">Username: <em>*</em></label>
<input type="text" id="txtUserName" name="txtUserName" maxlength="50" value="<?php echo $UserName; ?>" />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" id="btnSend" name="btnSend" value="Send" />
<input type="button" id="btnCancel" name="btnCancel" value="Cancel" onclick="window.location='login.php';" />
</div>

<?php } ?>

<?php if($Hash && !$HashError){ ?>

<input type="hidden" id="hidAction" name="hidAction" value="PasswordReset" />

<p>Please provide a new password for your account, and repeat it to avoid typing mistakes. Once your password is reset, you will be loggin in automatically.</p>

<div class="FormRow">
<label>Username:</label>
<span><?php echo htmlentities($User->getUserName()); ?></span>
</div>

<div class="FormRow">
<label for="txtNewPassword">New password: <em>*</em></label>
<input type="password" id="txtNewPassword" name="txtNewPassword" maxlength="50" />
</div>

<div class="FormRow">
<label for="txtRepeatPassword">Repeat password: <em>*</em></label>
<input type="password" id="txtRepeatPassword" name="txtRepeatPassword" maxlength="50" />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" id="btnSend" name="btnSend" value="Reset" />
<input type="button" id="btnCancel" name="btnCancel" value="Cancel" onclick="window.location='login.php';" />
</div>

<?php } ?>

<?php if($HashError){ ?>

<p>The hypelink you have used is not or no longer valid.<br />
Please return to the login-page.</p>

<div class="FormRow">
<label>&nbsp;</label>
<input type="button" id="btnCancel" name="btnCancel" value="Return" onclick="window.location='login.php';" />
</div>

<?php } ?>

<?php if(!is_null($MailSent) && $MailSent === true){ ?>

<p>An e-mail containing a hyperlink has been sent to your e-mailaddress. Use it ito reset your account's password.</p>

<div class="FormRow">
<label>&nbsp;</label>
<input type="button" id="btnCancel" name="btnCancel" value="Return" onclick="window.location='login.php';" />
</div>

<?php } ?>

<?php if(!is_null($MailSent) && $MailSent === false){ ?>

<p>An error occurred while sending your e-mail. Please contact the system's administrator.</p>

<div class="FormRow">
<label>&nbsp;</label>
<input type="button" id="btnCancel" name="btnCancel" value="Return" onclick="window.location='login.php';" />
</div>

<?php } ?>

</fieldset>
</form>

</div>

<?php echo HTMLstuff::HtmlFooter(); ?>