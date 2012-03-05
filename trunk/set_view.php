<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$ModelID = Utils::SafeIntFromQS('model_id');
$SetID = Utils::SafeIntFromQS('set_id');

if(!isset($ModelID))
{
	header('location:index.php');
	exit;
}

$NoErrorDuringPostback = true;
$DeleteSet = (array_key_exists('cmd', $_GET) && $_GET['cmd'] && ($_GET['cmd'] == COMMAND_DELETE));
$ReturnURL = sprintf('set.php?model_id=%1$d', $ModelID);
$DatesThisSet = array();


/* @var $Set Set */
/* @var $Model Model */
if($SetID != null)
{
	$WhereClause = sprintf('model_id = %1$d AND set_id = %2$d AND mut_deleted = -1', $ModelID, $SetID);
	$Sets = Set::GetSets($WhereClause);

	if($Sets)
	{ $Set = $Sets[0]; }
	else
	{
		header('location:index.php');
		exit;
	}
	
	$Model = $Set->getModel();
	$DatesThisSet = Date::GetDates(
		sprintf('set_id = %1$d AND mut_deleted = -1', $Set->getID())
	);
}
else
{
	$Set = new Set(null, 'New');
	$Model = Model::GetModels(sprintf('model_id = %1$d AND mut_deleted = -1', $ModelID));
	
	if($Model) { $Model = $Model[0]; }
	else
	{
		header('location:index.php');
		exit;
	}
	
	$Set->setModel($Model);
}

$DatesThisSet[] = new Date(null, $Set);


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'SetView')
{
	$Set->setPrefix($_POST['txtPrefix']);
	$Set->setName($_POST['txtName']);
	//$Set->setTags($_POST['txtTags']);

	if(array_key_exists('radContains', $_POST) && $_POST['radContains'])
	{ $Set->setContainsWhat(intval($_POST['radContains'])); }

	if($Set->getID())
	{
		if($DeleteSet)
		{
			if(Set::DeleteSet($Set, $CurrentUser))
			{
				$CacheImages = CacheImage::GetCacheImages(sprintf('index_id = %1$d', $Model->getID()));
				CacheImage::DeleteImages($CacheImages, $CurrentUser);
				
				header('location:'.$ReturnURL);
				exit;
			}
		}
		else
		{
			$NoErrorDuringPostback = Set::UpdateSet($Set, $CurrentUser);
		}
	}
	else
	{
		if(($NoErrorDuringPostback = Set::InsertSet($Set, $CurrentUser)))
		{
			$CacheImages = CacheImage::GetCacheImages(sprintf('index_id = %1$d', $Model->getID()));
			CacheImage::DeleteImages($CacheImages, $CurrentUser);
			
			$setid = $db->GetLatestID();
			if($setid) { $Set->setID($setid); }
		}
	}
	
	$Set->setDatesPic(
		HTMLstuff::DatesFromPOST($_POST, $Set, DATE_KIND_IMAGE)
	);
	
	$Set->setDatesVid(
		HTMLstuff::DatesFromPOST($_POST, $Set, DATE_KIND_VIDEO)
	);
	
	/* @var $Date Date */
	/* @var $dateInDb Date */
	foreach ($Set->getDatesPic() as $Date)
	{
		if($Date->getID())
		{
			if($Date->getTimeStamp() == -1)
			{ Date::DeleteDate($Date, $CurrentUser); }
			else
			{ Date::UpdateDate($Date, $CurrentUser); }
		}
		else if($Date->getTimeStamp() > 0)
		{
			Date::InsertDate($Date, $CurrentUser);
		}
	}
	
	foreach ($Set->getDatesVid() as $Date)
	{
		if($Date->getID())
		{
			if($Date->getTimeStamp() == -1)
			{ Date::DeleteDate($Date, $CurrentUser); }
			else
			{ Date::UpdateDate($Date, $CurrentUser); }
		}
		else if($Date->getTimeStamp() > 0)
		{
			Date::InsertDate($Date, $CurrentUser);
		}
	}

	if($NoErrorDuringPostback)
	{
		header('location:'.$ReturnURL);
		exit;
	}
}

echo HTMLstuff::HtmlHeader($Model->GetShortName(true).' - Set', $CurrentUser);

if($SetID)
{
	echo HTMLstuff::ImageLoading(
		sprintf('download_image.php?set_id=%1$d&width=400&height=600&portrait_only=true', $SetID),
		400,
		600,
		htmlentities($Model->GetFullName()),
		htmlentities($Model->GetFullName())
	);
	
	echo '<div class="PhotoContainer Loading"></div>';
}

?>

<h2><?php echo sprintf('<a href="index.php">Home</a> - <a href="model_view.php?model_id=%1$d">%2$s</a> - <a href="set.php?model_id=%1$d">Sets</a> - %3$s',
	$ModelID,
	htmlentities($Model->GetShortName(true)),
	htmlentities($Set->getName())
); ?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>" method="post">
<fieldset>
<legend>Please fill in these fields:</legend>

<input type="hidden" id="hidAction" name="hidAction" value="SetView" />

<div class="FormRow">
<label for="txtPrefix">Prefix:</label>
<input type="text" id="txtPrefix" name="txtPrefix" maxlength="100" value="<?php echo $Set->getPrefix();?>"<?php echo HTMLstuff::DisabledStr($DeleteSet); ?> />
</div>

<div class="FormRow">
<label for="txtName">Name: <em>*</em></label>
<input type="text" id="txtName" name="txtName" maxlength="100" value="<?php echo $Set->getName();?>"<?php echo HTMLstuff::DisabledStr($DeleteSet); ?> />
</div>

<div class="FormRow">
<label>Contains: </label>
<input type="radio" id="radImages" name="radContains" value="<?php echo SET_CONTENT_IMAGE; ?>"<?php echo ($Set->getContainsWhat() & SET_CONTENT_IMAGE) > 0 ? ' checked="checked"' : null; ?><?php echo HTMLstuff::DisabledStr($DeleteSet); ?> /> 
<label for="radImages" class="Radio">Images</label>
<input type="radio" id="radVideos" name="radContains" value="<?php echo SET_CONTENT_VIDEO; ?>"<?php echo ($Set->getContainsWhat() & SET_CONTENT_VIDEO) > 0 ? ' checked="checked"' : null; ?><?php echo HTMLstuff::DisabledStr($DeleteSet); ?> /> 
<label for="radVideos" class="Radio">Videos</label>
<input type="radio" id="radBoth" name="radContains" value="<?php echo (SET_CONTENT_IMAGE + SET_CONTENT_VIDEO); ?>"<?php echo (($Set->getContainsWhat() & SET_CONTENT_IMAGE) > 0 && ($Set->getContainsWhat() & SET_CONTENT_VIDEO) > 0) ? ' checked="checked"' : null; ?><?php echo HTMLstuff::DisabledStr($DeleteSet); ?> /> 
<label for="radBoth" class="Radio">Both</label>
</div>

<?php

/* @var $Date Date */
foreach ($DatesThisSet as $Date)
{
	if($Date->getDateKind() == DATE_KIND_IMAGE || $Date->getDateKind() == DATE_KIND_UNKNOWN)
	{
		if(!$DeleteSet || $Date->getTimeStamp() > 0)
		{
			echo HTMLstuff::DateFormField(
				$Date->getID(),
				Date::FormatDates(array($Date), 'Y-m-d'),
				DATE_KIND_IMAGE,
				$DeleteSet
			);
		}
	}
}

foreach ($DatesThisSet as $Date)
{
	if($Date->getDateKind() == DATE_KIND_VIDEO || $Date->getDateKind() == DATE_KIND_UNKNOWN)
	{
		if(!$DeleteSet || $Date->getTimeStamp() > 0)
		{
			echo HTMLstuff::DateFormField(
				$Date->getID(),
				Date::FormatDates(array($Date), 'Y-m-d'),
				DATE_KIND_VIDEO,
				$DeleteSet
			);
		}
	}
}
?>

<div class="FormRow">
<label for="txtTags">Tags (CSV):</label>
<input type="text" id="txtTags" name="txtTags" maxlength="200" class="TagsBox" value="<?php echo null; ?>"<?php echo HTMLstuff::DisabledStr($DeleteSet); ?> />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteSet ? 'Delete' : 'Save'; ?>" />
<input type="button" class="FormButton" value="Cancel" onclick="window.location='<?php echo $ReturnURL; ?>';" />
<input type="button" class="FormButton" value="Clear cacheimage(s)" onclick="window.location='cacheimage_delete.php?set_id=<?php echo $SetID ?>';"<?php echo HTMLstuff::DisabledStr($DeleteSet); ?> />
</div>

<div class="Separator"></div>


<?php
	if($Set && ($Set->getContainsWhat() & SET_CONTENT_IMAGE) > 0) 
	{ echo HTMLstuff::Button(sprintf('image.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), 'Images'); }
	
	if($Set && ($Set->getContainsWhat() & SET_CONTENT_VIDEO) > 0) 
	{ echo HTMLstuff::Button(sprintf('video.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), 'Videos'); }
?>

<?php echo HTMLstuff::Button(sprintf('set.php?model_id=%1$d', $ModelID), 'Sets'); ?>

<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>