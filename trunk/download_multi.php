<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$Models = null;
$ModelsOptions = null;
$SetsOptions = null;
$ImagesOptions = null;

$ButtonText = $lang->g('ButtonNext');
$Models = Model::GetModels();
$UseSubfoldersInDownload = array_key_exists('chkSubfolders', $_POST) && isset($_POST['chkSubfolders']);

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'DownloadMulti')
{
	$ModelsOptions = null;
	$SetsOptions = null;
	
	$SelectedModelIDs = array_key_exists('selModels', $_POST) ? Utils::SafeInts($_POST['selModels']) : array();

	/* @var $Model Model */
	foreach ($Models as $Model)
	{
		$ModelsOptions .= sprintf("<option value=\"%1\$d\"%3\$s>%2\$s</option>",
			$Model->getID(),
			htmlentities($Model->GetFullName()),
			in_array($Model->getID(), $SelectedModelIDs) ? ' selected="selected"' : null
		);
	}
	
	if($SelectedModelIDs)
	{
		$Sets = Set::GetSets(sprintf('mut_deleted = -1 AND model_id IN ( %1$s )', join(',', $SelectedModelIDs)));
		$SelectedSetIDs = array_key_exists('selSets', $_POST) ? Utils::SafeInts($_POST['selSets']) : array();
		
		/* @var $Set Set */
		foreach ($Sets as $Set)
		{
			$SetsOptions .= sprintf("<option value=\"%1\$d\"%5\$s>%2\$s - %3\$s%4\$s</option>",
				$Set->getID(),
				htmlentities($Set->getModel()->GetFullName()),
				htmlentities($Set->getPrefix()),
				htmlentities($Set->getName()),
				in_array($Set->getID(), $SelectedSetIDs) ? ' selected="selected"' : null
			);
		}
		
		if($SelectedSetIDs)
		{
			$Images = Image::GetImages(sprintf('mut_deleted = -1 AND set_id IN ( %1$s )', join(',', $SelectedSetIDs)));
			$SelectedImageIDs = array_key_exists('selImages', $_POST) ? Utils::SafeInts($_POST['selImages']) : array();
			 
			$ButtonText = $lang->g('ButtonDownload');

			/* @var $Image Image */
			foreach ($Images as $Image)
			{
				if(!in_array($Image->getSet()->getModel()->getID(), $SelectedModelIDs))
				{ continue; }
				
				$ImagesOptions .= sprintf("<option value=\"%1\$d\"%7\$s>%2\$s - %5\$s.%6\$s</option>",
					$Image->getID(),
					htmlentities($Image->getSet()->getModel()->GetFullName()),
					htmlentities($Image->getSet()->getPrefix()),
					htmlentities($Image->getSet()->getName()),
					htmlentities($Image->getFileName()),
					htmlentities($Image->getFileExtension()),
					in_array($Image->getID(), $SelectedImageIDs) ? ' selected="selected"' : null
				);
			}
			
			if($SelectedImageIDs)
			{
				header(
					sprintf('location:download_zip.php?image_ids=%1$s&usesub=%2$s',
						join(',', $SelectedImageIDs),
						$UseSubfoldersInDownload ? 'true' : 'false'
					)
				);
				exit;
			}
		}
	}
}
else
{
	/* @var $Model Model */
	foreach ($Models as $Model){
		$ModelsOptions .= sprintf("<option value=\"%1\$d\">%2\$s</option>",
			$Model->getID(),
			htmlentities($Model->GetFullName())
		);
	}
}

echo HTMLstuff::HtmlHeader('Download', $CurrentUser);
?>

<h2><?php echo sprintf('<a href="index.php">%1$s</a> - %2$s', $lang->g('NavigationHome'), $lang->g('NavigationMultiDownload'))?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="DownloadMulti" />

<select id="selModels" name="selModels[]" multiple="multiple" style="width:200px;height:400px;float:left;">
<?php echo $ModelsOptions?>
</select>

<select id="selSets" name="selSets[]" multiple="multiple" style="width:250px;height:400px;float:left;margin-left:20px;">
<?php echo ($SetsOptions ? $SetsOptions : '<option value=""></option>')?>
</select>

<select id="selImages" name="selImages[]" multiple="multiple" style="width:450px;height:400px;float:left;margin-left:20px;">
<?php echo ($ImagesOptions ? $ImagesOptions : '<option value=""></option>')?>
</select>

<div class="Clear"></div>

<ol>
<li><?php echo $lang->g('LabelMultiDownloadStep1')?></li>
<li><?php echo $lang->g('LabelMultiDownloadStep2')?></li>
<li><?php echo $lang->g('LabelMultiDownloadStep3')?></li>
</ol>

<div class="FormRow">
<label style="width:auto;" for="chkSubfolders"><?php echo $lang->g('LabelMultiDownloadUseSubfolders')?></label>&nbsp;<input type="checkbox" id="chkSubfolders" name="chkSubfolders"<?php echo HTMLstuff::CheckedStr($UseSubfoldersInDownload)?> />
</div>

<div class="Clear Separator"></div>

<div class="FormRow">
<input type="submit" class="FormButton" value="<?php echo $ButtonText?>" />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonReset')?>" onclick="window.location='download_multi.php';" />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='index.php';" />
</div>

</fieldset>
</form>

<?php

echo HTMLstuff::HtmlFooter($CurrentUser);

?>