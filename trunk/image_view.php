<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$ModelID = Utils::SafeIntFromQS('model_id');
$SetID = Utils::SafeIntFromQS('set_id');
$ImageID = Utils::SafeIntFromQS('image_id');

$TagsThisImage = Tag2All::GetTag2Alls(sprintf('image_id = %1$d', $ImageID));
$TagsInDB = Tag::GetTags();

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

/* @var $Image Image */
/* @var $Set Set */
/* @var $Model Model */
if($ImageID != null)
{
	$WhereClause = sprintf('model_id = %1$d AND set_id = %2$d AND image_id = %3$d AND mut_deleted = -1', $ModelID, $SetID, $ImageID);
	$Images = Image::GetImages($WhereClause);

	if($Images)
	{ $Image = $Images[0]; }
	else
	{ header('location:set.php?model_id='.$ModelID); }

	$Set = $Image->getSet();
	$Model = $Set->getModel();
}
else
{
	$Image = new Image(null, 'New');
	$Set = Set::GetSets(sprintf('set_id = %1d AND mut_deleted = -1', $SetID));

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
	$Image->setFileName($_POST['txtFilename']);
	$Image->setFileExtension($_POST['txtFileExtension']);
	$Image->setFileSize(intval($_POST['txtFilesize']));
	$Image->setFileCheckSum($_POST['txtFileChecksum']);
	$Image->setImageWidth(abs(intval($_POST['txtImageWidth'])));
	$Image->setImageHeight(abs(intval($_POST['txtImageHeight'])));
	
	$tags = Tag::GetTagArray($_POST['txtTags']);
	
	if($Image->getImageWidth() == 0) { $Image->setImageHeight(0); }
	if($Image->getImageHeight() == 0) { $Image->setImageWidth(0); }
	
	if($Image->getID())
	{
		if($DeleteImage)
		{
			if(Image::DeleteImage($Image, $CurrentUser))
			{
				header('location:'.$ReturnURL);
				exit;
			}
		}
		else
		{
			if(Image::UpdateImage($Image, $CurrentUser))
			{
				Tag2All::HandleTags($tags, $TagsThisImage, $TagsInDB, $CurrentUser, null, null, $Image->getID(), null);
				header('location:'.$ReturnURL);
				exit;
			}
		}
	}
	else
	{
		if(Image::InsertImage($Image, $CurrentUser))
		{
			$imageid = $db->GetLatestID();
			if($imageid) {
				$Image->setID($imageid);
			}
			
			Tag2All::HandleTags($tags, $TagsThisImage, $TagsInDB, $CurrentUser, null, null, $Image->getID(), null);
			header('location:'.$ReturnURL);
			exit;
		}
	}
}

echo HTMLstuff::HtmlHeader($Model->GetShortName(true).' - Set '.$Set->getName().' - Image', $CurrentUser);

if($ImageID)
{
	echo HTMLstuff::ImageLoading(
		sprintf('download_image.php?image_id=%1$d&width=400&height=600&portrait_only=true', $ImageID),
		400,
		600,
		htmlentities($Model->GetFullName()),
		htmlentities($Model->GetFullName())
	);
	
	echo '<div class="PhotoContainer Loading"></div>';
}

?>

<h2><?php echo sprintf(
	'<a href="index.php">Home</a> - <a href="model_view.php?model_id=%1$d">%4$s</a> - <a href="set.php?model_id=%1$d">Sets</a> -  <a href="set_view.php?model_id=%1$d&amp;set_id=%2$d">Set %5$s</a> - <a href="image.php?model_id=%1$d&amp;set_id=%2$d">Images</a> - %6$s',
	$ModelID,
	$SetID,
	$ImageID,
	htmlentities($Model->GetShortName(true)),
	htmlentities($Set->getName()),
	htmlentities($Image->getFileName())
); ?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>" method="post">
<fieldset><legend>Please fill in these fields:</legend>

<input type="hidden" id="hidAction" name="hidAction" value="ImageView" />

<div class="FormRow">
<label for="txtFilename">Filename: <em>*</em></label>
<input type="text" id="txtFilename" name="txtFilename" maxlength="100" value="<?php echo $Image->getFileName();?>"<?php echo HTMLstuff::DisabledStr($DeleteImage); ?> />
</div>

<div class="FormRow">
<label for="txtFileExtension">Extension: <em>*</em></label>
<input type="text" id="txtFileExtension" name="txtFileExtension" maxlength="10" value="<?php echo $Image->getFileExtension();?>"<?php echo HTMLstuff::DisabledStr($DeleteImage); ?> />
</div>

<div class="FormRow">
<label for="txtFilesize">Filesize (bytes): <em>*</em></label>
<input type="text" id="txtFilesize" name="txtFilesize" maxlength="10" value="<?php echo $Image->getFileSize(); ?>"<?php echo HTMLstuff::DisabledStr($DeleteImage); ?> />
</div>

<div class="FormRow">
<label for="txtFileChecksum">Checksum: <em>*</em></label>
<input type="text" id="txtFileChecksum" name="txtFileChecksum" maxlength="32" value="<?php echo $Image->getFileCheckSum();?>"<?php echo HTMLstuff::DisabledStr($DeleteImage); ?> />
</div>

<div class="FormRow">
<label for="txtImageWidth">Width (pixels):</label>
<input type="text" id="txtImageWidth" name="txtImageWidth" maxlength="10" value="<?php echo $Image->getImageWidth(); ?>"<?php echo HTMLstuff::DisabledStr($DeleteImage); ?> />
</div>

<div class="FormRow">
<label for="txtImageHeight">Height (pixels):</label>
<input type="text" id="txtImageHeight" name="txtImageHeight" maxlength="10" value="<?php echo $Image->getImageHeight(); ?>"<?php echo HTMLstuff::DisabledStr($DeleteImage); ?> />
</div>

<div class="FormRow">
<label for="txtTags">Tags (CSV):</label>
<input type="text" id="txtTags" name="txtTags" maxlength="200" class="TagsBox" value="<?php echo Tag2All::Tags2AllCSV($TagsThisImage); ?>"<?php echo HTMLstuff::DisabledStr($DeleteImage); ?> />
</div>

<div class="FormRow"><label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteImage ? 'Delete' : 'Save'; ?>" />
<input type="button" class="FormButton" value="Cancel" onclick="window.location='<?php echo htmlentities($ReturnURL); ?>';" />
<input type="button" class="FormButton" value="Clear cacheimage" onclick="window.location='cacheimage_delete.php?image_id=<?php echo $ImageID ?>';"<?php echo HTMLstuff::DisabledStr($DeleteImage); ?> />
</div>

<div class="Separator"></div>

<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>