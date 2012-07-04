<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$ModelID = Utils::SafeIntFromQS('model_id');
$SetID = Utils::SafeIntFromQS('set_id');
$VideoID = Utils::SafeIntFromQS('video_id');

$TagsThisVideo = Tag2All::GetTag2Alls(sprintf('model_id = %1$d AND set_id = %2$d AND image_id is null AND video_id = %3$d', $ModelID, $SetID, $VideoID));
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

$DeleteVideo = (array_key_exists('cmd', $_GET) && $_GET['cmd'] && ($_GET['cmd'] == COMMAND_DELETE));
$ReturnURL = sprintf('video.php?model_id=%1$d&set_id=%2$d', $ModelID, $SetID);


/* @var $Video Video */
/* @var $Set Set */
/* @var $Model Model */
if($VideoID != null)
{
	$WhereClause = sprintf('model_id = %1$d AND set_id = %2$d AND video_id = %3$d AND mut_deleted = -1', $ModelID, $SetID, $VideoID);
	$Videos = Video::GetVideos($WhereClause);

	if($Videos)
	{ $Video = $Videos[0]; }
	else
	{
		header('location:set.php?model_id='.$ModelID);
		exit;
	}

	$Set = $Video->getSet();
	$Model = $Set->getModel();
}
else
{
	$Video = new Video(null, $lang->g('LabelNew'));
	$Set = Set::GetSets(sprintf('set_id = %1d AND mut_deleted = -1', $SetID));

	if($Set)
	{
		$Set = $Set[0];
		$Model = $Set->getModel();
		$Video->setSet($Set);
	}
	else
	{
		header('location:index.php');
		exit;
	}
}


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'VideoView')
{
	$Video->setFileName($_POST['txtFilename']);
	$Video->setFileExtension($_POST['txtFileExtension']);
	$Video->setFileSize(intval($_POST['txtFilesize']));
	$Video->setFileCheckSum($_POST['txtFileChecksum']);
	
	$tags = Tag::GetTagArray($_POST['txtTags']);
	
	if($Video->getID())
	{
		if($DeleteVideo)
		{
			if(Video::DeleteVideo($Video, $CurrentUser))
			{
				header('location:'.$ReturnURL);
				exit;
			}
		}
		else
		{
			if(Video::UpdateVideo($Video, $CurrentUser))
			{
				Tag2All::HandleTags($tags, $TagsThisVideo, $TagsInDB, $CurrentUser, $ModelID, $SetID, null, $Video->getID(), null);
				header('location:'.$ReturnURL);
				exit;
			}
		}
	}
	else
	{
		if(Video::InsertVideo($Video, $CurrentUser))
		{
			$videoid = $db->GetLatestID();
			if($videoid) {
				$Video->setID($videoid);
			}
			
			Tag2All::HandleTags($tags, $TagsThisVideo, $TagsInDB, $CurrentUser, $ModelID, $SetID, null, $Video->getID());
			header('location:'.$ReturnURL);
			exit;
		}
	}
}

echo HTMLstuff::HtmlHeader(sprintf('%1$s - %2$s %3$s - %4$s',
	$Model->GetShortName(true),
	$lang->g('NavigationSet'),
	$Set->getName(),
	$lang->g('NavigationVideos')
), $CurrentUser);

?>

<h2><?php echo sprintf(
	'<a href="index.php">%7$s</a> - <a href="model_view.php?model_id=%1$d">%4$s</a> - <a href="set.php?model_id=%1$d">%8$s</a> -  <a href="set_view.php?model_id=%1$d&amp;set_id=%2$d">%9$s %5$s</a> - <a href="video.php?model_id=%1$d&amp;set_id=%2$d">%10$s</a> - %6$s',
	$ModelID,
	$SetID,
	$VideoID,
	htmlentities($Model->GetShortName(true)),
	htmlentities($Set->getName()),
	htmlentities($Video->getFileName()),
	$lang->g('NavigationHome'),
	$lang->g('NavigationSets'),
	$lang->g('NavigationSet'),
	$lang->g('NavigationVideos')
)?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="VideoView" />

<div class="FormRow">
<label for="txtFilename"><?php echo $lang->g('LabelFilename')?>: <em>*</em></label>
<input type="text" id="txtFilename" name="txtFilename" maxlength="100" value="<?php echo $Video->getFileName()?>"<?php echo HTMLstuff::DisabledStr($DeleteVideo)?> />
</div>

<div class="FormRow">
<label for="txtFileExtension"><?php echo $lang->g('LabelExtension')?>: <em>*</em></label>
<input type="text" id="txtFileExtension" name="txtFileExtension" maxlength="10" value="<?php echo $Video->getFileExtension()?>"<?php echo HTMLstuff::DisabledStr($DeleteVideo)?> />
</div>

<div class="FormRow">
<label for="txtFilesize"><?php echo $lang->g('LabelFilesize')?> (bytes): <em>*</em></label>
<input type="text" id="txtFilesize" name="txtFilesize" maxlength="10" value="<?php echo $Video->getFileSize()?>"<?php echo HTMLstuff::DisabledStr($DeleteVideo)?> />
</div>

<div class="FormRow">
<label for="txtFileChecksum"><?php echo $lang->g('LabelChecksum')?>: <em>*</em></label>
<input type="text" id="txtFileChecksum" name="txtFileChecksum" maxlength="32" value="<?php echo $Video->getFileCheckSum()?>"<?php echo HTMLstuff::DisabledStr($DeleteVideo)?> />
</div>

<div class="FormRow">
<label for="txtTags"><?php echo $lang->g('LabelTags')?> (CSV):</label>
<input type="text" id="txtTags" name="txtTags" maxlength="200" class="TagsBox" value="<?php echo Tag2All::Tags2AllCSV($TagsThisVideo)?>"<?php echo HTMLstuff::DisabledStr($DeleteVideo)?> />
</div>

<div class="FormRow"><label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteVideo ? $lang->g('ButtonDelete') : $lang->g('ButtonSave')?>" />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='<?php echo htmlentities($ReturnURL)?>';" />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonClearCacheImage')?>" onclick="window.location='cacheimage_delete.php?video_id=<?php echo $VideoID ?>';"<?php echo HTMLstuff::DisabledStr($DeleteVideo)?> />
</div>

<div class="Separator"></div>

<?php echo $VideoID ? HTMLstuff::Button(sprintf('download_image.php?video_id=%1$d&amp;width=800&amp;height=600', $VideoID), $lang->g('LabelThumbnails'), ' rel="lightbox"') : ''?>
<?php echo HTMLstuff::Button()?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>