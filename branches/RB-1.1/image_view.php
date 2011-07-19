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
$CurrentUser = Authentication::Authenticate();

if(!array_key_exists('model_id', $_GET) || !$_GET['model_id'] || !is_numeric($_GET['model_id'])){
	header('location:index.php');
}
$ModelID = (int)$_GET['model_id'];

if(!array_key_exists('set_id', $_GET) || !$_GET['set_id'] || !is_numeric($_GET['set_id'])){
	header('location:set.php?model_id='.$ModelID);
}
$SetID = (int)$_GET['set_id'];

if(array_key_exists('image_id', $_GET) && $_GET['image_id'] && is_numeric($_GET['image_id'])){
	$ImageID = (int)$_GET['image_id'];
}else{
	$ImageID = null;
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
	{ header('location:index.php'); }
}

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'ImageView')
{
	$Image->setFileName($_POST['txtFilename']);
	$Image->setFileExtension($_POST['txtFileExtension']);
	$Image->setFileSize(intval($_POST['txtFilesize']));
	$Image->setFileCheckSum($_POST['txtFileChecksum']);
	$Image->setImageWidth(abs(intval($_POST['txtImageWidth'])));
	$Image->setImageHeight(abs(intval($_POST['txtImageHeight'])));
	
	if($Image->getImageWidth() == 0) { $Image->setImageHeight(0); }
	if($Image->getImageHeight() == 0) { $Image->setImageWidth(0); }
	
	if($Image->getID())
	{
		if($DeleteImage)
		{
			if(Image::DeleteImage($Image, $CurrentUser))
			{ header('location:'.$ReturnURL); }
		}
		else
		{
			if(Image::UpdateImage($Image, $CurrentUser))
			{ header('location:'.$ReturnURL); }
		}
	}
	else
	{
		if(Image::InsertImage($Image, $CurrentUser))
		{ header('location:'.$ReturnURL); }
	}
}

echo HTMLstuff::HtmlHeader($Model->GetShortName().' - Set '.$Set->getName().' - Image', $CurrentUser);

if($ImageID)
{
	echo HTMLstuff::ImageLoading(
		sprintf('download_image.php?image_id=%1$d&random_pic=true&portrait_only=true', $ImageID),
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
	htmlentities($Model->GetShortName()),
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

<div class="FormRow"><label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteImage ? 'Delete' : 'Save'; ?>" />
<input type="button" class="FormButton" value="Cancel" onclick="window.location='<?php echo htmlentities($ReturnURL); ?>';" />
</div>

<div class="Separator"></div>

<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter();
?>