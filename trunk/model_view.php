<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$ModelID = Utils::SafeIntFromQS('model_id');
$DeleteModel = $CurrentUser->hasPermission(DELETE) ? (array_key_exists('cmd', $_GET) && $_GET['cmd'] && ($_GET['cmd'] == COMMAND_DELETE)) : null;
$EditModel = $CurrentUser->hasPermission(EDIT);

$TagsThisModel = Tag2All::GetTag2Alls(sprintf('model_id = %1$d AND set_id is null AND image_id is null AND video_id is null', $ModelID));
$TagsInDB = Tag::GetTags();

if($ModelID)
{
	$WhereClause = sprintf('model_id = %1$d AND mut_deleted = -1', $ModelID);
	$Models = Model::GetModels($WhereClause);

	if($Models)
	{ $Model = $Models[0]; }
	else
	{ HTMLstuff::RefererRedirect(); }
}
else
{
	$Model = new Model(null, $lang->g('NavigationNewModel'));
}

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'ModelView')
{
	$Model->setFirstName($_POST['txtFirstName']);
	$Model->setLastName($_POST['txtLastName']);

	if($_POST['txtBirthDate'] && $_POST['txtBirthDate'] != 'YYYY-MM-DD' && strtotime($_POST['txtBirthDate']) !== false)
	{ $Model->setBirthDate(strtotime($_POST['txtBirthDate'])); }
	else
	{ $Model->setBirthDate(-1); }

	$tags = Tag::GetTagArray($_POST['txtTags']);

	$Model->setRemarks($_POST['txtRemarks']);
	
	if($Model->getID())
	{
		if($DeleteModel)
		{
		    if(Model::DeleteModel($Model, $CurrentUser))
		    {
		    	header('location:index.php');
		    	exit;
		    }
		}
		else
		{
		    if(Model::UpdateModel($Model, $CurrentUser))
		    {
		    	Tag2All::HandleTags($tags, $TagsThisModel, $TagsInDB, $CurrentUser, $Model->getID(), null, null, null);
		    	header('location:index.php');
		    	exit;
		    }
		}
	}
	else
	{
		if(Model::InsertModel($Model, $CurrentUser))
		{
			$modelid = $db->GetLatestID();
			if($modelid) {
				$Model->setID($modelid);
			}
			
			Tag2All::HandleTags($tags, $TagsThisModel, $TagsInDB, $CurrentUser, $Model->getID(), null, null, null);
			header('location:index.php');
		    exit;
		}
	}
	
	
}

echo HTMLstuff::HtmlHeader($Model->GetFullName(), $CurrentUser);

if($ModelID)
{
	echo HTMLstuff::ImageLoading(
		sprintf('download_image.php?model_id=%1$d&width=400&height=600&portrait_only=true', $ModelID),
		400,
		600,
		htmlentities($Model->GetFullName()),
		htmlentities($Model->GetFullName())
	);
	
	echo '<div class="PhotoContainer Loading"></div>';
}

?>

<h2><?php echo sprintf('<a href="index.php">%2$s</a> - %1$s',
	htmlentities($Model->GetFullName()),
	$lang->g('NavigationHome')
)?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="ModelView" />

<div class="FormRow">
<label for="txtFirstName"><?php echo $lang->g('LabelFirstname')?>: <em>*</em></label>
<input type="text" id="txtFirstName" name="txtFirstName" maxlength="100" value="<?php echo $Model->getFirstName()?>"<?php echo HTMLstuff::DisabledStr($DeleteModel || !$EditModel)?> />
</div>

<div class="FormRow">
<label for="txtLastName"><?php echo $lang->g('LabelLastname')?>:</label>
<input type="text" id="txtLastName" name="txtLastName" maxlength="100" value="<?php echo $Model->getLastName()?>"<?php echo HTMLstuff::DisabledStr($DeleteModel || !$EditModel)?> />
</div>

<div class="FormRow">
<label for="txtBirthDate"><?php echo $lang->g('LabelBirthdate')?>:</label>
<input type="text" id="txtBirthDate" name="txtBirthDate" class="DatePicker"	maxlength="10" value="<?php echo $Model->getBirthDate() > 0 ? date('Y-m-d', $Model->getBirthDate()) : null?>"<?php echo HTMLstuff::DisabledStr($DeleteModel || !$EditModel)?> />
</div>

<div class="FormRow">
<label for="txtTags"><?php echo $lang->g('LabelTags')?> (CSV):</label>
<input type="text" id="txtTags" name="txtTags" maxlength="400" class="TagsBox" value="<?php echo Tag2All::Tags2AllCSV($TagsThisModel)?>"<?php echo HTMLstuff::DisabledStr($DeleteModel || !$EditModel)?> />
</div>

<div class="FormRow">
<label for="txtRemarks"><?php echo $lang->g('LabelRemarks')?>:</label>
<textarea id="txtRemarks" name="txtRemarks" cols="42" rows="16" <?php echo HTMLstuff::DisabledStr($DeleteModel || !$EditModel)?>><?php echo $Model->getRemarks()?></textarea>
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit"<?php echo HTMLstuff::DisabledStr(!$EditModel)?> class="FormButton" value="<?php echo $DeleteModel ? $lang->g('ButtonDelete') : $lang->g('ButtonSave')?>" />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='index.php';" />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonClearCacheImage')?>" onclick="window.location='cacheimage_delete.php?model_id=<?php echo $ModelID ?>';"<?php echo HTMLstuff::DisabledStr($DeleteModel)?> />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonClearIndexCacheImage')?>" onclick="window.location='cacheimage_delete.php?index_id=<?php echo $ModelID ?>';"<?php echo HTMLstuff::DisabledStr($DeleteModel)?> />
</div>

<div class="Separator"></div>

<?php echo $ModelID ? HTMLstuff::Button(sprintf('set.php?model_id=%1$d', $ModelID), $lang->g('NavigationSets')) : ''?>

<?php echo $ModelID ? HTMLstuff::Button(sprintf('download_image.php?index_id=%1$d&amp;width=500&amp;height=750', $ModelID), $lang->g('ButtonIndex'), ' rel="lightbox"') : ''?>

<?php echo HTMLstuff::Button('index.php')?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>