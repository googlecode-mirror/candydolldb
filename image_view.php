<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$ModelID = Utils::SafeIntFromQS('model_id');
$SetID = Utils::SafeIntFromQS('set_id');
$ImageID = Utils::SafeIntFromQS('image_id');

$TagsInDB = Tag::GetTags();
$TagsThisImage = Tag2All::GetTag2Alls(new Tag2AllSearchParameters(
	FALSE, FALSE, FALSE,
	is_null($ModelID) ? FALSE : $ModelID, FALSE,
	is_null($SetID) ? FALSE : $SetID, FALSE,
	is_null($ImageID) ? FALSE : $ImageID, FALSE,
	FALSE, FALSE,
	FALSE, FALSE, FALSE, TRUE));

if(!isset($ModelID))
{
	header('location:index.php');
	exit;
}

if(!isset($SetID))
{
	header('location:set.php?model_id='.$ModelID);
	exit;
}

$DeleteImage = (array_key_exists('cmd', $_GET) && $_GET['cmd'] && ($_GET['cmd'] == COMMAND_DELETE));
$ReturnURL = sprintf('image.php?model_id=%1$d&set_id=%2$d', $ModelID, $SetID);

$DisableControls =
	$DeleteImage ||
	(!$CurrentUser->hasPermission(RIGHT_IMAGE_EDIT) && !is_null($ImageID)) ||
	(!$CurrentUser->hasPermission(RIGHT_IMAGE_ADD) && is_null($ImageID));

$DisableDefaultButton =
	(!$CurrentUser->hasPermission(RIGHT_IMAGE_DELETE) && !is_null($ImageID) && $DeleteImage) ||
	(!$CurrentUser->hasPermission(RIGHT_IMAGE_EDIT) && !is_null($ImageID) && !$DeleteImage) ||
	(!$CurrentUser->hasPermission(RIGHT_IMAGE_ADD) && is_null($ImageID));

$DisableCacheDeleteButton =
	$DeleteImage ||
	is_null($ImageID) ||
	!$CurrentUser->hasPermission(RIGHT_CACHE_DELETE);

/* @var $Image Image */
/* @var $Set Set */
/* @var $Model Model */
if($ImageID != NULL)
{
	$Images = Image::GetImages(new ImageSearchParameters(
		$ImageID,
		FALSE,
		is_null($SetID) ? FALSE : $SetID,
		FALSE,
		is_null($ModelID) ? FALSE : $ModelID));

	if($Images)
	{ $Image = $Images[0]; }
	else
	{
		header('location:set.php?model_id='.$ModelID);
		exit;
	}

	$Set = $Image->getSet();
	$Model = $Set->getModel();
}
else
{
	$Image = new Image(NULL, $lang->g('LabelNew'));
	$Set = Set::GetSets(new SetSearchParameters($SetID));

	if($Set)
	{
		$Set = $Set[0];
		$Model = $Set->getModel();
		$Image->setSet($Set);
	}
	else
	{
		header('location:index.php');
		exit;
	}
}

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'ImageView')
{
	$Image->setFileName(Utils::NullIfEmpty($_POST['txtFilename']));
	$Image->setFileExtension(Utils::NullIfEmpty($_POST['txtFileExtension']));
	$Image->setFileSize(intval($_POST['txtFilesize']));
	$Image->setFileCheckSum(Utils::NullIfEmpty($_POST['txtFileChecksum']));
	$Image->setFileCRC32(Utils::NullIfEmpty($_POST['txtFileCRC32']));
	$Image->setImageWidth(abs(intval($_POST['txtImageWidth'])));
	$Image->setImageHeight(abs(intval($_POST['txtImageHeight'])));
	
	$tags = Tag::GetTagArray($_POST['txtTags']);
	
	if($Image->getImageWidth() == 0) { $Image->setImageHeight(0); }
	if($Image->getImageHeight() == 0) { $Image->setImageWidth(0); }
	
	if($Image->getID())
	{
		if($DeleteImage)
		{
			if(Image::Delete($Image, $CurrentUser))
			{
				header('location:'.$ReturnURL);
				exit;
			}
		}
		else
		{
			if(Image::Update($Image, $CurrentUser))
			{
				Tag2All::HandleTags($tags, $TagsThisImage, $TagsInDB, $CurrentUser, $ModelID, $SetID, $Image->getID(), NULL, TRUE);
				header('location:'.$ReturnURL);
				exit;
			}
		}
	}
	else
	{
		if(Image::Insert($Image, $CurrentUser))
		{
			Tag2All::HandleTags($tags, $TagsThisImage, $TagsInDB, $CurrentUser, $ModelID, $SetID, $Image->getID(), NULL, TRUE);
			header('location:'.$ReturnURL);
			exit;
		}
	}
}

echo HTMLstuff::HtmlHeader(sprintf('%1$s - %2$s - %3$s',
		$Model->GetShortName(TRUE),
		$lang->g('NavigationImages'),
		$Image->getFileName()
	),
	$CurrentUser
);

if($ImageID)
{
	$width = $Image->getImageWidthToppedOff(400, 600);
	$height = $Image->getImageHeightToppedOff(400, 600);
	
	echo HTMLstuff::ImageLoading(
		sprintf('download_image.php?image_id=%1$d&width=%2$d&height=%3$d&portrait_only=true', $ImageID, $width, $height),
		$width,
		$height,
		htmlentities($Model->GetFullName()),
		htmlentities($Model->GetFullName())
	);
	
	echo '<div class="PhotoContainer Loading"></div>';
}

?>

<h2><?php echo sprintf(
	'<a href="index.php">%7$s</a> - <a href="model_view.php?model_id=%1$d">%4$s</a> - <a href="set.php?model_id=%1$d">%8$s</a> -  <a href="set_view.php?model_id=%1$d&amp;set_id=%2$d">%9$s %5$s</a> - <a href="image.php?model_id=%1$d&amp;set_id=%2$d">%10$s</a> - %6$s',
	$ModelID,
	$SetID,
	$ImageID,
	htmlentities($Model->GetShortName(TRUE)),
	htmlentities($Set->getName()),
	htmlentities($Image->getFileName()),
	$lang->g('NavigationHome'),
	$lang->g('NavigationSets'),
	$lang->g('NavigationSet'),
	$lang->g('NavigationImages')
)?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="ImageView" />

<div class="FormRow">
<label for="txtFilename"><?php echo $lang->g('LabelFilename')?>: <em>*</em></label>
<input type="text" id="txtFilename" name="txtFilename" maxlength="100" value="<?php echo $Image->getFileName()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtFileExtension"><?php echo $lang->g('LabelExtension')?>: <em>*</em></label>
<input type="text" id="txtFileExtension" name="txtFileExtension" maxlength="10" value="<?php echo $Image->getFileExtension()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtFilesize"><?php echo $lang->g('LabelFilesize')?> (bytes): <em>*</em></label>
<input type="text" id="txtFilesize" name="txtFilesize" maxlength="10" value="<?php echo $Image->getFileSize()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtFileChecksum"><?php echo $lang->g('LabelMD5Checksum')?>:</label>
<input type="text" id="txtFileChecksum" name="txtFileChecksum" maxlength="32" value="<?php echo $Image->getFileCheckSum()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtFileCRC32">CRC32:</label>
<input type="text" id="txtFileCRC32" name="txtFileCRC32" maxlength="8" value="<?php echo $Image->getFileCRC32()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtImageWidth"><?php echo $lang->g('LabelWidth')?> (pixels):</label>
<input type="text" id="txtImageWidth" name="txtImageWidth" maxlength="10" value="<?php echo $Image->getImageWidth()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtImageHeight"><?php echo $lang->g('LabelHeight')?> (pixels):</label>
<input type="text" id="txtImageHeight" name="txtImageHeight" maxlength="10" value="<?php echo $Image->getImageHeight()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtTags"><?php echo $lang->g('LabelTags')?> (CSV):</label>
<input type="text" id="txtTags" name="txtTags" maxlength="400" class="TagsBox" value="<?php echo Tag2All::Tags2AllCSV($TagsThisImage)?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow"><label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteImage ? $lang->g('ButtonDelete') : $lang->g('ButtonSave')?>"<?php echo HTMLstuff::DisabledStr($DisableDefaultButton)?> />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='<?php echo htmlentities($ReturnURL)?>';" />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonClearCacheImage')?>" onclick="window.location='cacheimage_delete.php?image_id=<?php echo $ImageID ?>';"<?php echo HTMLstuff::DisabledStr($DisableCacheDeleteButton)?> />
</div>

<div class="Separator"></div>

<?php echo HTMLstuff::Button('index.php')?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>