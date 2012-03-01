<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();


$SearchModel = '';
$SearchDirty = true;
$SearchClean = true;
$ModelRows = '';
$ModelCount = 0;


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'ModelFilter')
{
	$SearchModel = $_SESSION['txtSearchModel'] 	= $_POST['txtSearchModel'];
	$SearchDirty = $_SESSION['chkDirty'] 		= array_key_exists('chkDirty', $_POST);
	$SearchClean = $_SESSION['chkClean'] 		= array_key_exists('chkClean', $_POST);
}
else
{
	$SearchModel = array_key_exists('txtSearchModel', $_SESSION) ? $_SESSION['txtSearchModel'] : '';
	$SearchDirty = array_key_exists('chkDirty', $_SESSION) ? (bool)$_SESSION['chkDirty'] : true;
	$SearchClean = array_key_exists('chkClean', $_SESSION) ? (bool)$_SESSION['chkClean'] : true; 
}

$WhereClause = sprintf(
	"CONCAT_WS(' ', model_firstname, model_lastname) LIKE '%%%1\$s%%' AND mut_deleted = -1",
	mysql_real_escape_string($SearchModel)
);

$Models = Model::GetModels($WhereClause);
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
		
		/* @var $Set Set */
		foreach(Set::FilterSets($Sets, $Model->getID()) as $Set)
		{
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
			<img src=\"download_image.php?model_id=%2\$d&amp;portrait_only=true&amp;width=150&amp;height=225\" width=\"150\" height=\"225\" alt=\"%1\$s\" title=\"%1\$s\" />
			</a>
			</div>
			
			<div class=\"ThumbDataWrapper\">
			<ul>
			<li>Name: %1\$s</li>
			<li>Birthdate: %5\$s%6\$s</li>
			<li>Pic-sets: %8\$d%7\$s</li>
			<li>Vid-sets: %10\$d%9\$s</li>
			</ul>
			</div>
			
			<div class=\"ThumbButtonWrapper\">
			<a href=\"model_view.php?model_id=%2\$d\"><img src=\"images/button_edit.png\" width=\"16\" height=\"16\" title=\"Edit model\" alt=\"Edit model\"/></a>
			<a href=\"import_image.php?model_id=%2\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"Import model's images\" title=\"Import model's images\" /></a>
			<a href=\"import_video.php?model_id=%2\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"Import model's videos\" title=\"Import model's videos\" /></a>
			<a href=\"download_zip.php?model_id=%2\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"Download model's images\" title=\"Download model's images\" /></a>
			<a href=\"download_image.php?index_id=%2\$d&amp;width=500&amp;height=750\" rel=\"lightbox-index\" title=\"Index of %1\$s\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" alt=\"Index of %1\$s\" /></a>
			<a href=\"model_view.php?model_id=%2\$d&amp;cmd=%3\$s\" title=\"Delete model\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"Delete\" /></a>
			</div>
			
			</div>
			
			%4\$s",
			
			htmlentities($Model->GetFullName()),
			$Model->getID(),
			COMMAND_DELETE,
			($ModelCount % 4 == 0 ? "<div class=\"Clear\"></div>" : null),
			$Model->getBirthdate() > 0 ? date($DateStyleArray[$_SESSION["displaydateoptions"]], $Model->getBirthdate()) : '&nbsp;',
			$Model->getBirthdate() > 0 ? sprintf(' (%1$.1f)', Utils::CalculateAge($Model->getBirthdate())) : '&nbsp;',
			$DirtySetPicCount > 0 ? sprintf(', <em>%1$d dirty</em>', $DirtySetPicCount) : null,
			$SetPicCount,
			$DirtySetVidCount > 0 ? sprintf(', <em>%1$d dirty</em>', $DirtySetVidCount) : null,
			$SetVidCount
		);
		
	}
	unset($Model);
}

echo HTMLstuff::HtmlHeader('Home', $CurrentUser);

?>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="FilterForm">
<fieldset>

<legend>Find a specific model:</legend>
<input type="hidden" id="hidAction" name="hidAction" value="ModelFilter" />

<label for="txtSearchModel">Model</label>
<input type="text" id="txtSearchModel" name="txtSearchModel" maxlength="50" value="<?php echo $SearchModel; ?>" />

<label for="chkDirty">Dirty</label>
<input type="checkbox" id="chkDirty" name="chkDirty"<?php echo HTMLstuff::CheckedStr($SearchDirty); ?> />

<label for="chkClean">Clean</label>
<input type="checkbox" id="chkClean" name="chkClean"<?php echo HTMLstuff::CheckedStr($SearchClean); ?> />

<input type="submit" id="btnSearch" name="btnSearch" value="Search" />

<input type="button" id="btnSlideshow" name="btnSlideshow" value="Index-slideshow" onclick="OpenSlideColorBox();" />

</fieldset>
</form>

<h2>Home</h2>

<?php

echo "<div class=\"Clear\"></div>".$ModelRows."<div class=\"Clear\"></div>";

echo HTMLstuff::HtmlFooter($CurrentUser);

?>