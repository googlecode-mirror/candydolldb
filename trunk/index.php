<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();


$SearchModel = '';
$SearchDirty = true;
$SearchClean = true;
$ModelRows = '';
$ModelCount = 0;
$SetCount = 0;


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
		$DirtySetCount = 0;
		
		/* @var $Set Set */
		foreach(Set::FilterSets($Sets, $Model->getID()) as $Set)
		{
			if($Set->getSetIsDirty())
			{ $DirtySetCount++; }
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
		
		$SetCount += $Model->getSetCount();

		$ModelRows .= sprintf(
		"\n<tr class=\"Row%13\$d\">".
    		"<td title=\"%5\$s\"><a href=\"model_view.php?model_id=%1\$d\">%6\$s</a></td>".
	        "<td class=\"Center\"%11\$s><a href=\"set.php?model_id=%1\$d\">%2\$d%10\$s</a></td>".
	    	"<td>%3\$s</td>".
        	"<td>%4\$s</td>".
        	"<td class=\"Center\">%7\$s</td>".
			"<td%9\$s>%8\$s</td>".
			"<td class=\"Center\"><a href=\"import_image.php?model_id=%1\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"Import model's sets and images\" title=\"Import model's sets and images\" /></a></td>".
			"<td class=\"Center\"><a href=\"import_video.php?model_id=%1\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"Import model's sets and videos\" title=\"Import model's sets and videos\" /></a></td>".
			"<td class=\"Center\"><a href=\"download_zip.php?model_id=%1\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"Download model's images\" title=\"Download model's images\" /></a></td>".
			"<td class=\"Center\"><a href=\"download_image.php?model_id=%1\$d&amp;random_pic=true\" rel=\"lightbox-gal\" title=\"%6\$s\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" alt=\"%6\$s\" /></a></td>".
			"<td class=\"Center\"><a href=\"download_image.php?model_id=%1\$d\" rel=\"lightbox-index\" title=\"Index of %6\$s\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" alt=\"Index of %6\$s\" /></a></td>".
			"<td class=\"Center\"><a href=\"model_view.php?model_id=%1\$d&amp;cmd=%12\$s\" title=\"Delete model\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"Delete\" /></a></td>".
        "</tr>",
		$Model->getID(),
		$Model->getSetCount(),
		htmlentities($Model->getFirstName()),
		htmlentities($Model->getLastName()),
		htmlentities($Model->GetShortName()),
		htmlentities($Model->GetFullName()),
		$Model->getBirthdate() > 0 ? sprintf('%1$.1f', Utils::CalculateAge($Model->getBirthdate())) : '&nbsp;',
		$Model->getBirthdate() > 0 ? date('j F Y', $Model->getBirthdate()) : '&nbsp;',
		$Model->getBirthdate() > 0 ? ' title="'.date('l', $Model->getBirthdate()).'"' : null,
		$DirtySetCount > 0 ? '<em> !</em>' : null,
		$DirtySetCount > 0 ? sprintf(' title="%1$d empty or incomplete set(s)"', $DirtySetCount) : null,
		COMMAND_DELETE,
		$ModelCount % 2 == 0 ? 2 : 1
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

</fieldset>
</form>

<h2>Home</h2>

<table border="0" cellpadding="4" cellspacing="0">
	<thead>
		<tr>
			<th>Model</th>
			<th style="width: 60px;"># Sets</th>
			<th style="width: 110px;">Firstname</th>
			<th style="width: 120px;">Lastname</th>
			<th style="width: 40px;">Age</th>
			<th style="width: 140px;">Birthdate</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="12">Total model count: <?php echo sprintf("%1\$d (%2\$d sets)", $ModelCount, $SetCount); ?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php echo $ModelRows ? $ModelRows : '<tr class="Row1"><td colspan="12">&nbsp;</td></tr>'; ?>
	</tbody>
</table>

<?php

echo HTMLstuff::Button(sprintf('model_view.php'), 'New model');
echo HTMLstuff::Button(sprintf('set_dirty.php'), 'Dirty sets');
echo HTMLstuff::Button(sprintf('user.php'), 'Users');
echo HTMLstuff::Button(sprintf('setup_data.php'), 'Process data (XML)');

echo HTMLstuff::HtmlFooter();
?>