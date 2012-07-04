<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$UserID = Utils::SafeIntFromQS('user_id');
$DeleteUser = (array_key_exists('cmd', $_GET) && $_GET['cmd'] && ($_GET['cmd'] == COMMAND_DELETE));

$_SESSION['UserSalt'] = null;
$PasswordError = false;
$LanguageOptions = null;
$DateFormatOptions = null;
 

/* @var $User User */
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
	$User = new User(null, $lang->g('LabelNewUser'));
}

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'UserView')
{
	if((array_key_exists('txtUserName', $_POST)) && (array_key_exists('hidPassword', $_POST)))
	{
		$User->setUserName($_POST['txtUserName']);
		$User->setPassword($_POST['hidPassword']);
		$User->setSalt($_SESSION['UserSalt']);
	}

	$User->setFirstName($_POST['txtFirstName']);
	$User->setInsertion($_POST['txtInsertion']);
	$User->setLastName($_POST['txtLastName']);
	$User->setEmailAddress($_POST['txtEmailAddress']);
	$User->setLanguage($_POST['selectLanguage']);
	$User->setDateDisplayOptions($_POST['selectDateformat']);
	$User->setImageview($_POST['selectImageview']);

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

foreach (i18n::$SupportedLanguages as $l){
	$LanguageOptions .= sprintf("
		<option value=\"%1\$s\"%2\$s>%3\$s%4\$s</option>",
		$l,
		$User->getLanguage() == $l ? ' selected="selected"' : null,
		$lang->g('LabelLanguage_'.$l),
		$l == 'en' ? ' [Default]' : null
	);
}

foreach($DateStyleArray as $index => $format)
{
	$DateFormatOptions .= sprintf("
		<option value=\"%1\$d\"%2\$s>%3\$s%4\$s</option>",
		$index,
		$User->getDateDisplayOptions() == $index ? ' selected="selected"' : null,
		date($format),
		$index == 0 ? ' [Default]' : null
	);
}

echo HTMLstuff::HtmlHeader($User->GetFullName(), $CurrentUser);

?>

<script type="text/javascript">
//<![CDATA[

setInterval(function () {
  if($("#txtRepeatPassword").val().length > 0) {
    $("#submitform").removeAttr("disabled");
  } else {
    $("#submitform").attr("disabled", "disabled");
  }
}, 500);

//]]>
</script>

<h2><?php echo sprintf('<a href="index.php">%3$s</a> - <a href="user.php">%2$s</a> - %1$s',
	htmlentities($User->getUserName()),
	$lang->g('NavigationUsers'),
	$lang->g('NavigationHome')
); ?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>" method="post">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="UserView" />
<input type="hidden" id="hidPassword" name="hidPassword" value="<?php echo $User->getPassword(); ?>" />

<?php if($User->getID() == $CurrentUser->getID() || $User->getUserName() == $lang->g('LabelNewUser')){ ?>
<div class="FormRow">
<label for="txtUserName"><?php echo $lang->g('LabelUsername');?>: <em>*</em></label>
<input type="text" id="txtUserName" name="txtUserName" maxlength="50" value="<?php echo $User->getUserName();?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtPassword"><?php echo $lang->g('LabelPassword');?>:<?php echo $UserID ? '' : ' <em>*</em>'; ?></label>
<input type="password" id="txtPassword" name="txtPassword" maxlength="100" value=""<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
<input type="button" id="btnGenerate" name="btnGenerate" value="<?php echo $lang->g('ButtonGenerate');?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> onclick="$.get('ajax_genpass.php', function(data){$('#txtGenerated, #txtPassword, #txtRepeatPassword').val(data);});" />
<input type="text" id="txtGenerated" name="txtGenerated" class="Small" readonly="readonly" maxlength="10" value=""<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtRepeatPassword"><?php echo $lang->g('LabelRepeatPassword');?>:<?php echo $UserID ? '' : ' <em>*</em>'; ?></label>
<input type="password" id="txtRepeatPassword" name="txtRepeatPassword" maxlength="100" value=""<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>
<? } ?>

<div class="FormRow">
<label for="selectLanguage"><?php echo $lang->g('LabelLanguage');?>:</label>
<select id="selectLanguage" name="selectLanguage"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?>><?php echo $LanguageOptions ?></select>
</div>

<div class="FormRow">
<label for="selectDateformat"><?php echo $lang->g('LabelSelectDateFormat');?>:</label>
<select id="selectDateformat" name="selectDateformat"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?>><?php echo $DateFormatOptions ?></select>
</div>

<div class="FormRow">
<label for="selectImageview"><?php echo $lang->g('LabelSelectImageFormat');?>:</label>
<select id="selectImageview" name="selectImageview"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?>>
<option value="detail" <?php echo $User->getImageview() == 'detail' ? ' selected="selected"' : null ?>>Detail View [Default]</option>
<option value="thumb" <?php echo $User->getImageview() == 'thumb' ? ' selected="selected"' : null ?>>Thumbnail View</option>

</select>
</div>

<div class="FormRow">
<label><?php echo $lang->g('LabelGender');?>: </label>
<input type="radio" id="radFemale" name="radGender" value="<?php echo GENDER_FEMALE; ?>"<?php echo $User->getGender() == GENDER_FEMALE ? ' checked="checked"' : null; ?><?php echo HTMLstuff::DisabledStr($DeleteUser); ?> /> 
<label for="radFemale" class="Radio"><?php echo $lang->g('LabelFemale');?></label>
<input type="radio" id="radMale" name="radGender" value="<?php echo GENDER_MALE; ?>"<?php echo $User->getGender() == GENDER_MALE ? ' checked="checked"' : null; ?><?php echo HTMLstuff::DisabledStr($DeleteUser); ?> /> 
<label for="radMale" class="Radio"><?php echo $lang->g('LabelMale');?></label>
</div>

<div class="FormRow">
<label for="txtFirstName"><?php echo $lang->g('LabelFirstname');?>: <em>*</em></label>
<input type="text" id="txtFirstName" name="txtFirstName" maxlength="100" value="<?php echo $User->getFirstName();?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtInsertion"><?php echo $lang->g('LabelInsertion');?>:</label>
<input type="text" id="txtInsertion" name="txtInsertion" maxlength="20" value="<?php echo $User->getInsertion();?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtLastName"><?php echo $lang->g('LabelLastname');?>: <em>*</em></label>
<input type="text" id="txtLastName" name="txtLastName" maxlength="100" value="<?php echo $User->getLastName();?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtEmailAddress"><?php echo $lang->g('LabelEmailAddress');?>: <em>*</em></label>
<input type="text" id="txtEmailAddress" name="txtEmailAddress" maxlength="255" value="<?php echo $User->getEmailAddress();?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label for="txtBirthDate"><?php echo $lang->g('LabelBirthdate');?>:</label>
<input type="text" id="txtBirthDate" name="txtBirthDate" class="DatePicker"	maxlength="10" value="<?php echo $User->getBirthDate() > 0 ? date('Y-m-d', $User->getBirthDate()) : null; ?>"<?php echo HTMLstuff::DisabledStr($DeleteUser); ?> />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" id="submitform" class="FormButton" value="<?php echo $DeleteUser ? $lang->g('ButtonDelete') : $lang->g('ButtonSave'); ?>" <?php echo ($User->getID() == $CurrentUser->getID() || $User->getUserName() == $lang->g('LabelNewUser')) ?  'disabled="disabled"' : null ?> />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonCancel');?>" onclick="window.location='user.php';" />
</div>

<div class="Separator"></div>

<?php echo HTMLstuff::Button('index.php'); ?>
</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>