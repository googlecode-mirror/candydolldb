<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

if(array_key_exists('user_id', $_GET) && $_GET['user_id'] && is_numeric($_GET['user_id'])){
	$UserID = (int)$_GET['user_id'];
}else{
	$UserID = null;
}

/* @var $User User */
$DeleteUser = (array_key_exists('cmd', $_GET) && $_GET['cmd'] && ($_GET['cmd'] == COMMAND_DELETE));
$_SESSION['UserSalt'] = null;
$PasswordError = false; 


if($UserID)
{
	$WhereClause = sprintf('user_id = %1$d AND mut_deleted = -1', $UserID);
	$Users = User::GetUsers($WhereClause);

	if($Users)
	{ $User = $Users[0]; }
	else
	{
		header('location:index.php');
		exit;
	}
	
	$_SESSION['UserSalt'] = $User->getSalt();
}
else
{
	$User = new User(null, 'New user');
}

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'UserView')
{
	$User->setUserName($_POST['txtUserName']);
	$User->setPassword($_POST['hidPassword']);
	$User->setSalt($_SESSION['UserSalt']);
	$User->setFirstName($_POST['txtFirstName']);
	$User->setInsertion($_POST['txtInsertion']);
	$User->setLastName($_POST['txtLastName']);
	$User->setEmailAddress($_POST['txtEmailAddress']);
	
	if(array_key_exists('radGender', $_POST))
	{
		switch (intval($_POST['radGender']))
		{
			case GENDER_FEMALE:
				$User->setGender(GENDER_FEMALE);
				break;
			case GENDER_MALE:
				$User->setGender(GENDER_MALE);
				break;
			default:
			case GENDER_UNKNOWN:
				$User->setGender(GENDER_UNKNOWN);
				break;
		}
	}
	else
	{ $User->setGender(GENDER_UNKNOWN); }
	
	if($_POST['txtPassword'])
	{
		if($_POST['txtRepeatPassword'] && $_POST['txtRepeatPassword'] == $_POST['txtPassword'])
		{
			$NewSalt = Utils::GenerateGarbage(20);
			$_SESSION['UserSalt'] = $NewSalt;
			$User->setSalt($NewSalt);
			$User->setPassword(Utils::HashString($_POST['txtPassword'], $NewSalt));
		}
		else
		{ $PasswordError = true; }
	}

	if($_POST['txtBirthDate'] && $_POST['txtBirthDate'] != 'YYYY-MM-DD' && strtotime($_POST['txtBirthDate']) !== false)
	{ $User->setBirthDate(strtotime($_POST['txtBirthDate'])); }
	else
	{ $User->setBirthDate(-1); }
	
	if(!$PasswordError || $DeleteUser)
	{
		if(Utils::ValidateEmail($User->getEmailAddress()) || $DeleteUser)
		{
			if($User->getID())
			{
				if($DeleteUser)
				{
				    if(User::DeleteUser($User, $CurrentUser))
				    {
				    	header('location:user.php');
				    	exit;
				    }
				}
				else
				{
				    if(User::UpdateUser($User, $CurrentUser))
				    {
				    	if($User->getID() == $CurrentUser->getID())
				    	{ $_SESSION['CurrentUser'] = serialize($User); }
				    	header('location:user.php');
				    	exit;
				    }
				}
			}
			else
			{
				if(User::InsertUser($User, $CurrentUser))
				{
					header('location:user.php');
					exit;
				}
			}
		}
		else
		{
			$EmailError = new SyntaxError();
			$EmailError->setErrorNumber(SYNTAX_ERR_EMAILADDRESS);
			$EmailError->setErrorMessage(SyntaxError::TranslateSyntaxError(SYNTAX_ERR_EMAILADDRESS));
			Error::AddError($EmailError);
		}
	}
	else
	{
		$LoginError = new LoginError();
		$LoginError->setErrorNumber(LOGIN_ERR_PASSWORDSNOTIDENTICAL);
		$LoginError->setErrorMessage(LoginError::TranslateLoginError(LOGIN_ERR_PASSWORDSNOTIDENTICAL));
		Error::AddError($LoginError);
	}
}

echo HTMLstuff::HtmlHeader($User->GetFullName(), $CurrentUser);

?>

<h2><?php echo sprintf('<a href="index.php">Home</a> - <a href="user.php">Users</a> - %1$s',
	htmlentities($User->getUserName())
); ?></h2>

<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
<fieldset>
<legend>Please fill in these fields:</legend>

<input type="hidden" id="hidAction" name="hidAction" value="UserView" />
<input type="hidden" id="hidPassword" name="hidPassword" value="<?php echo $User->getPassword(); ?>" />

<div class="FormRow">
<label for="txtUserName">Username: <em>*</em></label>
<input type="text" id="txtUserName" name="txtUserName" maxlength="50" value="<?php echo $User->getUserName();?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtPassword">Password:<?php echo $UserID ? '' : ' <em>*</em>'; ?></label>
<input type="password" id="txtPassword" name="txtPassword" maxlength="100" value=""<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
<input type="button" id="btnGenerate" name="btnGenerate" value="Generate"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> onclick="$.get('ajax_genpass.php', function(data){$('#txtGenerated, #txtPassword, #txtRepeatPassword').val(data);});" />
<input type="text" id="txtGenerated" name="txtGenerated" class="Small" readonly="readonly" maxlength="10" value=""<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtRepeatPassword">Repeat password:<?php echo $UserID ? '' : ' <em>*</em>'; ?></label>
<input type="password" id="txtRepeatPassword" name="txtRepeatPassword" maxlength="100" value=""<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label>Gender: </label>
<input type="radio" id="radFemale" name="radGender" value="<?php echo GENDER_FEMALE; ?>"<?php echo $User->getGender() == GENDER_FEMALE ? ' checked="checked"' : null; ?><?php echo HTMLstuff::DisabledStr($DeleteUser); ?> /> 
<label for="radFemale" class="Radio">Female</label>
<input type="radio" id="radMale" name="radGender" value="<?php echo GENDER_MALE; ?>"<?php echo $User->getGender() == GENDER_MALE ? ' checked="checked"' : null; ?><?php echo HTMLstuff::DisabledStr($DeleteUser); ?> /> 
<label for="radMale" class="Radio">Male</label>
</div>

<div class="FormRow">
<label for="txtFirstName">Firstname: <em>*</em></label>
<input type="text" id="txtFirstName" name="txtFirstName" maxlength="100" value="<?php echo $User->getFirstName();?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtInsertion">Insertion:</label>
<input type="text" id="txtInsertion" name="txtInsertion" maxlength="20" value="<?php echo $User->getInsertion();?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtLastName">Lastname: <em>*</em></label>
<input type="text" id="txtLastName" name="txtLastName" maxlength="100" value="<?php echo $User->getLastName();?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtEmailAddress">Email address: <em>*</em></label>
<input type="text" id="txtEmailAddress" name="txtEmailAddress" maxlength="255" value="<?php echo $User->getEmailAddress();?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtBirthDate">Birthdate:</label>
<input type="text" id="txtBirthDate" name="txtBirthDate" class="DatePicker"	maxlength="10" value="<?php echo $User->getBirthDate() > 0 ? date('Y-m-d', $User->getBirthDate()) : null; ?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteUser ? 'Delete' : 'Save'; ?>" />
<input type="button" class="FormButton" value="Cancel" onclick="window.location='user.php';" />
</div>

<div class="Separator"></div>

<?php echo HTMLstuff::Button('index.php'); ?>
</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>