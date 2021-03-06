<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$SearchModel = '';
$SearchDirty = TRUE;
$SearchClean = TRUE;
$OrderBy = 1;
$OrderMode = 'DESC';
$OrderClause = NULL;
$ModelRows = '';
$ModelCount = 0;
$SetCount = 0;

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'ModelFilter')
{
	$SearchModel = $_SESSION['txtIndexSearchModel'] = $_POST['txtIndexSearchModel'];
	$SearchDirty = $_SESSION['chkDirty'] 			= array_key_exists('chkDirty', $_POST);
	$SearchClean = $_SESSION['chkClean'] 			= array_key_exists('chkClean', $_POST);
	$OrderBy 	 = $_SESSION['selIndexOrderBy']		= in_array($_POST['selOrderBy'], array(1,2,3,4,5,6)) ? $_POST['selOrderBy'] : 1;
	$OrderMode 	 = $_SESSION['radIndexOrderByMode']	= in_array($_POST['radSORT'], array('ASC', 'DESC')) ? $_POST['radSORT'] : 'ASC';
}
else
{
	$SearchModel = array_key_exists('txtIndexSearchModel', $_SESSION) 	? $_SESSION['txtIndexSearchModel'] 		: '';
	$SearchDirty = array_key_exists('chkDirty', $_SESSION) 				? (bool)$_SESSION['chkDirty'] 			: TRUE;
	$SearchClean = array_key_exists('chkClean', $_SESSION) 				? (bool)$_SESSION['chkClean'] 			: TRUE;
	$OrderBy	 = array_key_exists('selIndexOrderBy', $_SESSION) 		? intval($_SESSION['selIndexOrderBy']) 	: 1;
	$OrderMode = array_key_exists('radIndexOrderByMode', $_SESSION) 	? $_SESSION['radIndexOrderByMode'] 		: 'ASC';
}

switch($OrderBy){
	case 1:
		$OrderClause = 'model_firstname '.$OrderMode.', model_lastname ASC';
		break;
	
	case 2:
		$OrderClause = 'model_lastname '.$OrderMode.', model_firstname ASC';
		break;

	case 3:
		$OrderClause = 'model_birthdate '.$OrderMode.', model_firstname ASC, model_lastname ASC';
		break;
	
	case 4:
		$OrderClause = 'model_setcount '.$OrderMode.', model_firstname ASC, model_lastname ASC';
		break;
	
	case 5:
		$OrderClause = 'model_firstset '.$OrderMode.', model_firstname ASC, model_lastname ASC';
		break;
		
	case 6:
		$OrderClause = 'model_lastset '.$OrderMode.', model_firstname ASC, model_lastname ASC';
		break;
}

$Models = Model::GetModels(new ModelSearchParameters(FALSE, FALSE, FALSE, FALSE, $SearchModel), $OrderClause);
$Sets = Set::GetSets();

if($Models)
{
	/* @var $Model Model */
	foreach($Models as $Model)
	{
		$SetPicCount = 0;
		$SetVidCount = 0;
		$DirtySetCount = 0;
		$DirtySetPicCount = 0;
		$DirtySetVidCount = 0;

		$Dates = Date::GetDates(new DateSearchParameters(FALSE, FALSE, FALSE, FALSE, $Model->getID()));
		if($Dates)
		{
			$datestart = Date::SmallestDate($Dates);
			$datestartshow = date($CurrentUser->getDateFormat(), $datestart->getTimeStamp());
			$dateend = Date::LargestDate($Dates);
			$dateendshow = date($CurrentUser->getDateFormat(), $dateend->getTimeStamp());
		}
		else
		{
			$datestartshow = NULL;
			$dateendshow = NULL;
		}
		
		/* @var $Set Set */
		foreach(Set::Filter($Sets, $Model->getID()) as $Set)
		{
			$SetCount++;
			if($Set->getSetIsDirty())
			{
				if($Set->getSetIsDirtyPic())
				{ $DirtySetPicCount++; }
				
				if($Set->getSetIsDirtyVid())
				{ $DirtySetVidCount++; }
				
				$DirtySetCount++;
			}
			
			if(($Set->getContainsWhat() & SET_CONTENT_IMAGE) > 0)
			{ $SetPicCount++; }
				
			if(($Set->getContainsWhat() & SET_CONTENT_VIDEO) > 0)
			{ $SetVidCount++; }
		}
		unset($Set);
		
		if( !($SearchDirty && $SearchClean) && (	// NOT both checkboxes checked AND (
			(!$SearchDirty && !$SearchClean) ||		// both checkboxes unchecked OR
			($SearchClean && $DirtySetCount > 0) ||	// clean requested, yet this model is dirty OR
			($SearchDirty && $DirtySetCount == 0) )	// dirty requested, yet this model is clean )
		){
			continue;
		}
		
		$ModelCount++;
		
		$ModelRows .= sprintf(
			"<div class=\"ThumbGalItem\">
			<h3 class=\"Hidden\">%1\$s</h3>
			
			<div class=\"ThumbImageWrapper\">
			<a href=\"set.php?model_id=%2\$d\">
			<img src=\"download_image.php?model_id=%2\$d&amp;portrait_only=true&amp;width=150&amp;height=225&amp;set_type=%25\$d\" width=\"150\" height=\"225\" alt=\"%1\$s\" title=\"%1\$s\" />
			</a>
			</div>
			
			<div class=\"ThumbDataWrapper\">
			<ul>
			<li>%11\$s: %1\$s</li>
			<li>%12\$s: %5\$s%6\$s</li>
			<li>%23\$s: %21\$s</li>
			<li>%24\$s: %22\$s</li>
			<li>%13\$s: %8\$d%7\$s</li>
			<li>%14\$s: %10\$d%9\$s</li>
			</ul>
			</div>
			
			<div class=\"ThumbButtonWrapper\">
			%29\$s
			%27\$s
			%28\$s
			%30\$s
			%31\$s
			%26\$s
			</div>
			
			</div>
			
			%4\$s",
			
			htmlentities($Model->GetFullName()),
			$Model->getID(),
			COMMAND_DELETE,
			($ModelCount % 4 == 0 ? "<div class=\"Clear\"></div>" : NULL),
			$Model->getBirthdate() > 0 ? date($CurrentUser->getDateFormat(), $Model->getBirthdate()) : '&nbsp;',
			$Model->getBirthdate() > 0 ? sprintf(' (%1$.1f)', Utils::CalculateAge($Model->getBirthdate())) : '&nbsp;',
			$DirtySetPicCount > 0 ? sprintf(', <em>%1$d %2$s</em>', $DirtySetPicCount, strtolower($lang->g('LabelDirty'))) : NULL,
			$SetPicCount,
			$DirtySetVidCount > 0 ? sprintf(', <em>%1$d %2$s</em>', $DirtySetVidCount, strtolower($lang->g('LabelDirty'))) : NULL,
			$SetVidCount,
			$lang->g('LabelName'),
			$lang->g('LabelBirthdateShort'),
			$lang->g('LabelPicSets'),
			$lang->g('LabelVidSets'),
        		NULL,
        		NULL,
        		NULL,
			$lang->g('LabelDownloadImages'),
			$lang->g('LabelIndexOf'),
        		NULL,
			$datestartshow,
			$dateendshow,
			$lang->g('LabelStartDate'),
        	$lang->g('LabelLastUpdated'),
        		NULL,
        	
        	$CurrentUser->hasPermission(RIGHT_MODEL_DELETE) ?
        		sprintf("<a href=\"model_view.php?model_id=%1\$d&amp;cmd=%2\$s\" title=\"%3\$s\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"%3\$s\" /></a>",$Model->getID(),COMMAND_DELETE,$lang->g('LabelDeleteModel')) :
        		sprintf("<a href=\"#\" title=\"%1\$s\"><img src=\"images/button_delete_invalid.png\" width=\"16\" height=\"16\" alt=\"%1\$s\" /></a>",$lang->g('LabelNotAllowed')),
        	
        	$CurrentUser->hasPermission(RIGHT_IMAGE_ADD) ?
        		sprintf("<a href=\"import_image.php?model_id=%1\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"%2\$s\" title=\"%2\$s\" /></a>",$Model->getID(),$lang->g('ButtonImportImages')) :
        		sprintf("<a href=\"#\"><img src=\"images/button_upload_invalid.png\" width=\"16\" height=\"16\" alt=\"%1\$s\" title=\"%1\$s\" /></a>",$lang->g('LabelNotAllowed')),
        	
        	$CurrentUser->hasPermission(RIGHT_VIDEO_ADD) ?
        		sprintf("<a href=\"import_video.php?model_id=%1\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"%2\$s\" title=\"%2\$s\" /></a>",$Model->getID(),$lang->g('ButtonImportVideos')) :
        		sprintf("<a href=\"#\"><img src=\"images/button_upload_invalid.png\" width=\"16\" height=\"16\" alt=\"%1\$s\" title=\"%1\$s\" /></a>",$lang->g('LabelNotAllowed')),
        	
        	$CurrentUser->hasPermission(RIGHT_MODEL_EDIT) ?
        		sprintf("<a href=\"model_view.php?model_id=%1\$d\"><img src=\"images/button_edit.png\" width=\"16\" height=\"16\" title=\"%2\$s\" alt=\"%2\$s\"/></a>",$Model->getID(),$lang->g('LabelEditModel')) :
        		sprintf("<a href=\"#\"><img src=\"images/button_edit_invalid.png\" width=\"16\" height=\"16\" title=\"%1\$s\" alt=\"%1\$s\"/></a>",$lang->g('LabelNotAllowed')),
        	
        	$CurrentUser->hasPermission(RIGHT_EXPORT_ZIP) ?
        		sprintf("<a href=\"download_zip.php?model_id=%1\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"%2\$s\" title=\"%2\$s\" /></a>",$Model->getID(),$lang->g('LabelDownloadImages')) :
        		sprintf("<a href=\"#\"><img src=\"images/button_download_invalid.png\" width=\"16\" height=\"16\" title=\"%1\$s\" alt=\"%1\$s\"/></a>",$lang->g('LabelNotAllowed')),
				
			$CurrentUser->hasPermission(RIGHT_EXPORT_INDEX) ?
				sprintf("<a href=\"download_image.php?index_id=%1\$d&amp;width=500&amp;height=750\" rel=\"lightbox-index\" title=\"%2\$s %3\$s\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" alt=\"%2\$s %3\$s\" /></a>", $Model->getID(), $lang->g('LabelIndexOf'), $Model->GetFullName()) :
				sprintf("<a href=\"#\"><img src=\"images/button_view_invalid.png\" width=\"16\" height=\"16\" title=\"%1\$s\" alt=\"%1\$s\"/></a>",$lang->g('LabelNotAllowed'))
		);
	}
	unset($Model);
}

echo HTMLstuff::HtmlHeader($lang->g('NavigationHome'), $CurrentUser);

?>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post" class="FilterForm">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="ModelFilter" />

<label for="txtIndexSearchModel"><?php echo $lang->g('LabelModel')?></label>
<input type="text" id="txtIndexSearchModel" name="txtIndexSearchModel" maxlength="50" value="<?php echo $SearchModel?>" style="width:100px;" />

<label for="chkDirty"><?php echo $lang->g('LabelDirty')?>
<input type="checkbox" id="chkDirty" name="chkDirty"<?php echo HTMLstuff::CheckedStr($SearchDirty)?> /></label>

<label for="chkClean"><?php echo $lang->g('LabelClean')?>
<input type="checkbox" id="chkClean" name="chkClean"<?php echo HTMLstuff::CheckedStr($SearchClean)?> /></label>

<label for="selOrderBy"><?php echo $lang->g('LabelSorting')?></label>
<select id="selOrderBy" name="selOrderBy">
	<option value="1"<?php echo HTMLstuff::SelectedStr($OrderBy==1)?>><?php echo $lang->g('LabelFirstname')?></option>
	<option value="2"<?php echo HTMLstuff::SelectedStr($OrderBy==2)?>><?php echo $lang->g('LabelLastname')?></option>
	<option value="3"<?php echo HTMLstuff::SelectedStr($OrderBy==3)?>><?php echo $lang->g('LabelBirthdate')?></option>
	<option value="4"<?php echo HTMLstuff::SelectedStr($OrderBy==4)?>><?php echo $lang->g('LabelSetCount')?></option>
	<option value="5"<?php echo HTMLstuff::SelectedStr($OrderBy==5)?>><?php echo $lang->g('LabelFirstAppearance')?></option>
	<option value="6"<?php echo HTMLstuff::SelectedStr($OrderBy==6)?>><?php echo $lang->g('LabelLastAppearance')?></option>
</select>

<label for="radASC"><?php echo $lang->g('LabelSortingASC')?><input type="radio" id="radASC" name="radSORT" value="ASC"<?php echo HTMLstuff::CheckedStr($OrderMode=='ASC')?> /></label>
<label for="radDESC"><?php echo $lang->g('LabelSortingDESC')?><input type="radio" id="radDESC" name="radSORT" value="DESC"<?php echo HTMLstuff::CheckedStr($OrderMode=='DESC')?> /></label>

<input type="submit" id="btnSearch" name="btnSearch" value="<?php echo $lang->g('ButtonSearch')?>" />

<input type="button" id="btnSlideshow" name="btnSlideshow" value="<?php echo $lang->g('ButtonIndexSlideshow')?>" onclick="OpenSlideColorBox();" />

</fieldset>
</form>

<h2><?php echo $lang->g('NavigationHome')?></h2>

<?php
echo "<div class=\"Clear\"></div>".$ModelRows."<div class=\"Clear\"></div>";
?>

<div style="font-weight:bold;text-align:center"><?php echo $lang->g('LabelTotalModelCount')?>: <?php printf('%1$d', $ModelCount)?> | <?php echo $lang->g('LabelTotalSetCount')?>: <?php printf('%1$d', $SetCount)?></div>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>