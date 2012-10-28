<?php
/* This file is part of CandyDollDB.

CandyDollDB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CandyDollDB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CandyDollDB. If not, see <http://www.gnu.org/licenses/>.
*/

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$UserID = Utils::SafeIntFromQS('user_id');
$DeleteUser = (array_key_exists('cmd', $_GET) && $_GET['cmd'] && ($_GET['cmd'] == COMMAND_DELETE));

$_SESSION['UserSalt'] = NULL;
$PasswordError = FALSE;
$LanguageOptions = NULL;
$DateFormatOptions = NULL;
$RightsCheckboxes = NULL;

$DisableControls =
	$DeleteUser ||
	($UserID == $CurrentUser->getID() && !$CurrentUser->hasPermission(RIGHT_ACCOUNT_EDIT)) ||
	($UserID != $CurrentUser->getID() && !$CurrentUser->hasPermission(RIGHT_USER_EDIT) && !is_null($UserID)) ||
	($UserID != $CurrentUser->getID() && !$CurrentUser->hasPermission(RIGHT_USER_ADD) && is_null($UserID));

$DisableDefaultButton =
	($UserID == $CurrentUser->getID() && !$CurrentUser->hasPermission(RIGHT_ACCOUNT_EDIT)) ||	
	($UserID != $CurrentUser->getID() && !$CurrentUser->hasPermission(RIGHT_USER_DELETE) && !is_null($UserID) && $DeleteUser) ||
	($UserID != $CurrentUser->getID() && !$CurrentUser->hasPermission(RIGHT_USER_EDIT) && !is_null($UserID) && !$DeleteUser) ||
	($UserID != $CurrentUser->getID() && !$CurrentUser->hasPermission(RIGHT_USER_ADD) && is_null($UserID));

$DisableRights = 
	$DeleteUser ||
	(!$CurrentUser->hasPermission(RIGHT_USER_RIGHTS) && !is_null($UserID));

/* @var $User User */
if($UserID)
{
	$Users = User::GetUsers(new UserSearchParameters($UserID));

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
	$User = new User(NULL, $lang->g('LabelNewUser'));
}

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'UserView')
{
	if(array_key_exists('txtUserName', $_POST))
	{
		$User->setUserName(Utils::NullIfEmpty($_POST['txtUserName']));
	}
	
	if(array_key_exists('hidPassword', $_POST))
	{
		$User->setPassword(Utils::NullIfEmpty($_POST['hidPassword']));
		$User->setSalt(Utils::NullIfEmpty($_SESSION['UserSalt']));
	}

	$User->setFirstName(Utils::NullIfEmpty($_POST['txtFirstName']));
	$User->setInsertion(Utils::NullIfEmpty($_POST['txtInsertion']));
	$User->setLastName(Utils::NullIfEmpty($_POST['txtLastName']));
	$User->setEmailAddress(Utils::NullIfEmpty($_POST['txtEmailAddress']));
	$User->setLanguage(Utils::NullIfEmpty($_POST['selectLanguage']));
	$User->setDateDisplayOptions($_POST['selectDateformat']);
	$User->setImageview(Utils::NullIfEmpty($_POST['selectImageview']));

	if($CurrentUser->hasPermission(RIGHT_USER_RIGHTS))
	{
		$getrights = array();
		foreach(Rights::getDefinedRights() as $k => $v)
		{
			if(array_key_exists('chk'.$k, $_POST))
			{ $getrights[] = $v; }
		}
		$User->setRights($getrights);
	}

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

	if(array_key_exists('txtPassword', $_POST) && $_POST['txtPassword'])
	{
		if($_POST['txtRepeatPassword'] && $_POST['txtRepeatPassword'] == $_POST['txtPassword'])
		{
			$NewSalt = Utils::GenerateGarbage(20);
			$_SESSION['UserSalt'] = $NewSalt;
			$User->setSalt($NewSalt);
			$User->setPassword(Utils::HashString($_POST['txtPassword'], $NewSalt));
		}
		else
		{ $PasswordError = TRUE; }
	}

	if($_POST['txtBirthDate'] && $_POST['txtBirthDate'] != 'YYYY-MM-DD' && strtotime($_POST['txtBirthDate']) !== FALSE)
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
				    if(User::Delete($User, $CurrentUser))
				    {
				    	session_regenerate_id(TRUE);
				    	header('location:user.php');
				    	exit;
				    }
				}
				else
				{
				    if(User::Update($User, $CurrentUser))
				    {
				    	if($User->getID() == $CurrentUser->getID())
				    	{ $_SESSION['CurrentUser'] = serialize($User); }
				    	
				    	session_regenerate_id(TRUE);
				    	header('location:user.php');
				    	exit;
				    }
				}
			}
			else
			{
				if(User::Insert($User, $CurrentUser))
				{
					session_regenerate_id(TRUE);
					header('location:user.php');
					exit;
				}
			}
		}
		else
		{
			$e = new SyntaxError(SYNTAX_ERR_EMAILADDRESS);
			Error::AddError($e);
		}
	}
	else
	{
		$e = new LoginError(LOGIN_ERR_PASSWORDSNOTIDENTICAL);
		Error::AddError($e);
	}
}

foreach (i18n::$SupportedLanguages as $l){
	$LanguageOptions .= sprintf("
		<option value=\"%1\$s\"%2\$s>%3\$s%4\$s</option>",
		$l,
		HTMLstuff::SelectedStr($User->getLanguage() == $l),
		$lang->g('LabelLanguage_'.$l),
		$l == 'en' ? $lang->g('LabelSuffixDefault') : NULL
	);
}

foreach($DateStyleArray as $index => $format)
{
	$DateFormatOptions .= sprintf("
		<option value=\"%1\$d\"%2\$s>%3\$s%4\$s</option>",
		$index,
		HTMLstuff::SelectedStr($User->getDateDisplayOptions() == $index),
		date($format),
		$index == 0 ? $lang->g('LabelSuffixDefault') : NULL
	);
}

foreach(Rights::getDefinedRights() as $k => $v)
{
	$RightsCheckboxes .= sprintf("<li>
		<label for=\"chk%1\$s\" class=\"Radio\">
			<input type=\"checkbox\" id=\"chk%1\$s\" name=\"chk%1\$s\"%3\$s%4\$s />
			&nbsp;%2\$s
		</label></li>",
		$k,
		$lang->g('Label'.$k),
		HTMLstuff::CheckedStr($User->hasPermission($v)),
		HTMLstuff::DisabledStr($DisableRights)
	);
}

echo HTMLstuff::HtmlHeader($User->GetFullName(), $CurrentUser);
?>

<script type="text/javascript">
//<![CDATA[

function ToggleBoxes(){
	$('input[id^=chkRIGHT_]').each(function(i, a){
		$(a).attr('checked', !$(a).attr('checked')); 
	});
	return true;
}

//]]>
</script>

<h2><?php echo sprintf('<a href="index.php">%3$s</a> - <a href="user.php">%2$s</a> - %1$s',
	htmlentities($User->getUserName()),
	$lang->g('NavigationUsers'),
	$lang->g('NavigationHome')
)?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="UserView" />
<input type="hidden" id="hidPassword" name="hidPassword" value="<?php echo $User->getPassword()?>" />

<?php if(
	($CurrentUser->hasPermission(RIGHT_ACCOUNT_EDIT) && $User->getID() == $CurrentUser->getID())
	|| $CurrentUser->hasPermission(RIGHT_USER_EDIT)
	|| is_null($User->getID())){ ?>

<div class="FormRow">
<label for="txtUserName"><?php echo $lang->g('LabelUsername')?>: <em>*</em></label>
<input type="text" id="txtUserName" name="txtUserName" maxlength="50" value="<?php echo $User->getUserName()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<?php if(
	($CurrentUser->hasPermission(RIGHT_ACCOUNT_EDIT) && $CurrentUser->hasPermission(RIGHT_ACCOUNT_PASSWORD) && $User->getID() == $CurrentUser->getID())
	|| $CurrentUser->hasPermission(RIGHT_USER_EDIT)
	|| is_null($User->getID())){ ?>

<div class="FormRow">
<label for="txtPassword"><?php echo $lang->g('LabelPassword')?>:<?php echo $UserID ? '' : ' <em>*</em>'?></label>
<input type="password" id="txtPassword" name="txtPassword" maxlength="100" value=""<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
<input type="button" id="btnGenerate" name="btnGenerate" value="<?php echo $lang->g('ButtonGenerate')?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> onclick="$.get('ajax_genpass.php', function(data){$('#txtGenerated, #txtPassword, #txtRepeatPassword').val(data);});" />
<input type="text" id="txtGenerated" name="txtGenerated" class="Small" readonly="readonly" maxlength="10" value=""<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtRepeatPassword"><?php echo $lang->g('LabelRepeatPassword')?>:<?php echo $UserID ? '' : ' <em>*</em>'?></label>
<input type="password" id="txtRepeatPassword" name="txtRepeatPassword" maxlength="100" value=""<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<? } ?>

<? } ?>

<div class="FormRow">
<label for="selectLanguage"><?php echo $lang->g('LabelLanguage')?>:</label>
<select id="selectLanguage" name="selectLanguage"<?php echo HTMLstuff::DisabledStr($DisableControls)?>><?php echo $LanguageOptions ?></select>
</div>

<div class="FormRow">
<label for="selectDateformat"><?php echo $lang->g('LabelSelectDateFormat')?>:</label>
<select id="selectDateformat" name="selectDateformat"<?php echo HTMLstuff::DisabledStr($DisableControls)?>><?php echo $DateFormatOptions ?></select>
</div>

<div class="FormRow">
<label for="selectImageview"><?php echo $lang->g('LabelSelectImageFormat')?>:</label>
<select id="selectImageview" name="selectImageview"<?php echo HTMLstuff::DisabledStr($DisableControls)?>>
<option value="detail" <?php echo $User->getImageview() == 'detail' ? ' selected="selected"' : NULL ?>><?php echo $lang->g('LabelViewModeDetail').$lang->g('LabelSuffixDefault')?></option>
<option value="thumb" <?php echo $User->getImageview() == 'thumb' ? ' selected="selected"' : NULL ?>><?php echo $lang->g('LabelViewModeThumbnail')?></option>

</select>
</div>

<div class="FormRow">
<label><?php echo $lang->g('LabelGender')?>: </label>
<input type="radio" id="radFemale" name="radGender" value="<?php echo GENDER_FEMALE?>"<?php echo $User->getGender() == GENDER_FEMALE ? ' checked="checked"' : NULL?><?php echo HTMLstuff::DisabledStr($DisableControls)?> /> 
<label for="radFemale" class="Radio"><?php echo $lang->g('LabelFemale')?></label>
<input type="radio" id="radMale" name="radGender" value="<?php echo GENDER_MALE?>"<?php echo $User->getGender() == GENDER_MALE ? ' checked="checked"' : NULL?><?php echo HTMLstuff::DisabledStr($DisableControls)?> /> 
<label for="radMale" class="Radio"><?php echo $lang->g('LabelMale')?></label>
</div>

<div class="FormRow">
<label for="txtFirstName"><?php echo $lang->g('LabelFirstname')?>: <em>*</em></label>
<input type="text" id="txtFirstName" name="txtFirstName" maxlength="100" value="<?php echo $User->getFirstName()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtInsertion"><?php echo $lang->g('LabelInsertion')?>:</label>
<input type="text" id="txtInsertion" name="txtInsertion" maxlength="20" value="<?php echo $User->getInsertion()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtLastName"><?php echo $lang->g('LabelLastname')?>: <em>*</em></label>
<input type="text" id="txtLastName" name="txtLastName" maxlength="100" value="<?php echo $User->getLastName()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtEmailAddress"><?php echo $lang->g('LabelEmailAddress')?>: <em>*</em></label>
<input type="text" id="txtEmailAddress" name="txtEmailAddress" maxlength="255" value="<?php echo $User->getEmailAddress()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtBirthDate"><?php echo $lang->g('LabelBirthdate')?>:</label>
<input type="text" id="txtBirthDate" name="txtBirthDate" class="DatePicker"	maxlength="10" value="<?php echo $User->getBirthDate() > 0 ? date('Y-m-d', $User->getBirthDate()) : NULL?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label><?php echo $lang->g('LabelUserRights')?>:</label><br />
<label for="chkToggleRights"><input type="checkbox" id="chkToggleRights" name="chkToggleRights"<?php echo HTMLstuff::DisabledStr($DisableControls)?> onclick="ToggleBoxes();"/>&nbsp;<?php echo $lang->g('ButtonToggle')?></label>
	<div class="CheckBoxMadness">
	<ul><?php echo $RightsCheckboxes?></ul>
	</div>
</div>

<div class="FormRow Clear">
<label>&nbsp;</label>
<input type="submit" id="submitform" class="FormButton" value="<?php echo $DeleteUser ? $lang->g('ButtonDelete') : $lang->g('ButtonSave')?>" <?php echo HTMLstuff::DisabledStr($DisableDefaultButton) ?> />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='user.php';" />
</div>

<div class="Separator"></div>

<?php echo HTMLstuff::Button('index.php')?>
</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>
