<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$ModelID = Utils::SafeIntFromQS('model_id');
$SetID = Utils::SafeIntFromQS('set_id');

$TagsInDB = Tag::GetTags();
$TagsThisSet = Tag2All::GetTag2Alls(new Tag2AllSearchParameters(
	FALSE, FALSE, FALSE,
	$ModelID, FALSE,
	$SetID, FALSE,
	FALSE, FALSE,
	FALSE, FALSE,
	FALSE, FALSE, TRUE, TRUE));

if(!isset($ModelID))
{
	header('location:index.php');
	exit;
}

$NoErrorDuringPostback = TRUE;
$DeleteSet = (array_key_exists('cmd', $_GET) && $_GET['cmd'] && ($_GET['cmd'] == COMMAND_DELETE));

$DisableControls =
	$DeleteSet ||
	(!$CurrentUser->hasPermission(RIGHT_SET_EDIT) && !is_null($SetID)) ||
	(!$CurrentUser->hasPermission(RIGHT_SET_ADD) && is_null($SetID));

$DisableDefaultButton =
	(!$CurrentUser->hasPermission(RIGHT_SET_DELETE) && !is_null($SetID) && $DeleteSet) ||
	(!$CurrentUser->hasPermission(RIGHT_SET_EDIT) && !is_null($SetID)) ||
	(!$CurrentUser->hasPermission(RIGHT_SET_ADD) && is_null($SetID));

$ReturnURL = sprintf('set.php?model_id=%1$d', $ModelID);
$DatesThisSet = array();


/* @var $Set Set */
/* @var $Model Model */
if($SetID != NULL)
{
	$Sets = Set::GetSets(new SetSearchParameters($SetID, FALSE, $ModelID));

	if($Sets)
	{ $Set = $Sets[0]; }
	else
	{
		header('location:index.php');
		exit;
	}
	
	$Model = $Set->getModel();
	$DatesThisSet = Date::GetDates(new DateSearchParameters(FALSE, FALSE, $Set->getID()));
}
else
{
	$Set = new Set(NULL, $lang->g('New'));
	$Model = Model::GetModels(new ModelSearchParameters($ModelID));
	
	if($Model) { $Model = $Model[0]; }
	else
	{
		header('location:index.php');
		exit;
	}
	
	$Set->setModel($Model);
}

$DatesThisSet[] = new Date(NULL, DATE_KIND_UNKNOWN, -1,
	$Set->getID(), $Set->getPrefix(), $Set->getName(), $Set->getContainsWhat(),
	$Model->getID(), $Model->getFirstName(), $Model->getLastName());

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'SetView')
{
	$Set->setPrefix($_POST['txtPrefix']);
	$Set->setName($_POST['txtName']);

	$tags = Tag::GetTagArray($_POST['txtTags']);

	if(array_key_exists('radContains', $_POST) && $_POST['radContains'])
	{ $Set->setContainsWhat(intval($_POST['radContains'])); }

	if($Set->getID())
	{
		if($DeleteSet)
		{
			if($CurrentUser->hasPermission(RIGHT_SET_DELETE) && Set::Delete($Set, $CurrentUser))
			{
				$CacheImages = CacheImage::GetCacheImages(new CacheImageSearchParameters(FALSE, FALSE, $Model->getID()));
				CacheImage::DeleteMulti($CacheImages, $CurrentUser);
				
				header('location:'.$ReturnURL);
				exit;
			}
		}
		else if($CurrentUser->hasPermission(RIGHT_SET_EDIT))
		{
			$NoErrorDuringPostback = Set::Update($Set, $CurrentUser);
			
			if($NoErrorDuringPostback){
				Tag2All::HandleTags($tags, $TagsThisSet, $TagsInDB, $CurrentUser, $ModelID, $Set->getID(), NULL, NULL);
			}
		}
	}
	else if($CurrentUser->hasPermission(RIGHT_SET_ADD))
	{
		if(($NoErrorDuringPostback = Set::Insert($Set, $CurrentUser)))
		{
			$CacheImages = CacheImage::GetCacheImages(new CacheImageSearchParameters(FALSE, FALSE, $Model->getID()));
			CacheImage::DeleteMulti($CacheImages, $CurrentUser);
			
			Tag2All::HandleTags($tags, $TagsThisSet, $TagsInDB, $CurrentUser, $ModelID, $Set->getID(), NULL, NULL);
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
			{ Date::Delete($Date, $CurrentUser); }
			else
			{ Date::Update($Date, $CurrentUser); }
		}
		else if($Date->getTimeStamp() > 0)
		{
			Date::Insert($Date, $CurrentUser);
		}
	}
	
	foreach ($Set->getDatesVid() as $Date)
	{
		if($Date->getID())
		{
			if($Date->getTimeStamp() == -1)
			{ Date::Delete($Date, $CurrentUser); }
			else
			{ Date::Update($Date, $CurrentUser); }
		}
		else if($Date->getTimeStamp() > 0)
		{
			Date::Insert($Date, $CurrentUser);
		}
	}

	if($NoErrorDuringPostback)
	{
		header('location:'.$ReturnURL);
		exit;
	}
}

echo HTMLstuff::HtmlHeader(sprintf('%1$s - %2$s - %3$s',
		$Model->GetShortName(TRUE),
		$lang->g('NavigationSets'),
		$Set->getName()
	),
	$CurrentUser
);

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

<h2><?php echo sprintf(
	'<a href="index.php">%5$s</a> - <a href="model_view.php?model_id=%1$d">%3$s</a> - <a href="set.php?model_id=%1$d">%6$s</a> - %7$s %4$s',
	$ModelID,
	$SetID,
	htmlentities($Model->GetShortName(TRUE)),
	htmlentities($Set->getName()),
	$lang->g('NavigationHome'),
	$lang->g('NavigationSets'),
	$lang->g('NavigationSet')
)?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="SetView" />

<div class="FormRow">
<label for="txtPrefix"><?php echo $lang->g('LabelPrefix')?>:</label>
<input type="text" id="txtPrefix" name="txtPrefix" maxlength="100" value="<?php echo $Set->getPrefix()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label for="txtName"><?php echo $lang->g('LabelName')?>: <em>*</em></label>
<input type="text" id="txtName" name="txtName" maxlength="100" value="<?php echo $Set->getName()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label><?php echo $lang->g('LabelContains')?>: </label>
<input type="radio" id="radImages" name="radContains" value="<?php echo SET_CONTENT_IMAGE?>"<?php echo ($Set->getContainsWhat() & SET_CONTENT_IMAGE) > 0 ? ' checked="checked"' : NULL?><?php echo HTMLstuff::DisabledStr($DisableControls)?> /> 
<label for="radImages" class="Radio"><?php echo $lang->g('NavigationImages')?></label>
<input type="radio" id="radVideos" name="radContains" value="<?php echo SET_CONTENT_VIDEO?>"<?php echo ($Set->getContainsWhat() & SET_CONTENT_VIDEO) > 0 ? ' checked="checked"' : NULL?><?php echo HTMLstuff::DisabledStr($DisableControls)?> /> 
<label for="radVideos" class="Radio"><?php echo $lang->g('NavigationVideos')?></label>
<input type="radio" id="radBoth" name="radContains" value="<?php echo (SET_CONTENT_IMAGE + SET_CONTENT_VIDEO)?>"<?php echo (($Set->getContainsWhat() & SET_CONTENT_IMAGE) > 0 && ($Set->getContainsWhat() & SET_CONTENT_VIDEO) > 0) ? ' checked="checked"' : NULL?><?php echo HTMLstuff::DisabledStr($DisableControls)?> /> 
<label for="radBoth" class="Radio"><?php echo $lang->g('LabelBoth')?></label>
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
				$DisableControls
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
				$DisableControls
			);
		}
	}
}
?>

<div class="FormRow">
<label for="txtTags"><?php echo $lang->g('LabelTags')?> (CSV):</label>
<input type="text" id="txtTags" name="txtTags" maxlength="400" class="TagsBox" value="<?php echo Tag2All::Tags2AllCSV($TagsThisSet)?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteSet ? $lang->g('ButtonDelete') : $lang->g('ButtonSave')?>"<?php echo HTMLstuff::DisabledStr($DisableDefaultButton)?> />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='<?php echo $ReturnURL?>';" />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonClearCacheImage')?>" onclick="window.location='cacheimage_delete.php?set_id=<?php echo $SetID ?>';"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="Separator"></div>

<?php
	if($Set && ($Set->getContainsWhat() & SET_CONTENT_IMAGE) > 0) 
	{ echo HTMLstuff::Button(sprintf('image.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), $lang->g('NavigationImages')); }
	
	if($Set && ($Set->getContainsWhat() & SET_CONTENT_VIDEO) > 0) 
	{ echo HTMLstuff::Button(sprintf('video.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), $lang->g('NavigationVideos')); }
?>

<?php echo HTMLstuff::Button(sprintf('set.php?model_id=%1$d', $ModelID), $lang->g('NavigationSets'))?>

<?php echo HTMLstuff::Button('index.php')?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>