<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

if(!$CurrentUser->hasPermission(RIGHT_SEARCH_DIRTY))
{
	$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
	Error::AddError($e);
	HTMLstuff::RefererRedirect();
}

HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$ModelID = Utils::SafeIntFromQS('model_id');

$Model = NULL;
$PreviousModel = NULL;
$SearchModel = '';
$SearchDate = '';
$FilterSPECIAL = FALSE;
$FilterPIC = FALSE;
$FilterVID = FALSE;
$SetRows = '';
$SetCount = 0;
$SetCountModel = 0;
$ImageCount = 0;
$VideoCount = 0;

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'DirtySetFilter')
{
	$SearchModel = $_SESSION['txtSearchModel']		= $_POST['txtSearchModel'];
	$SearchDate = $_SESSION['txtSearchDate'] 		= $_POST['txtSearchDate'];
	$FilterSPECIAL = $_SESSION['chkFilterSPECIAL']	= array_key_exists('chkFilterSPECIAL', $_POST);
	$FilterPIC = $_SESSION['chkFilterPIC']			= array_key_exists('chkFilterPIC', $_POST);
	$FilterVID = $_SESSION['chkFilterVID']			= array_key_exists('chkFilterVID', $_POST);
}
else
{
	$SearchModel = array_key_exists('txtSearchModel', $_SESSION) ? $_SESSION['txtSearchModel'] : '';
	$SearchDate = array_key_exists('txtSearchDate', $_SESSION) ? $_SESSION['txtSearchDate'] : '';
	$FilterSPECIAL = array_key_exists('chkFilterSPECIAL', $_SESSION) ? (bool)$_SESSION['chkFilterSPECIAL'] : TRUE;
	$FilterPIC = array_key_exists('chkFilterPIC', $_SESSION) ? (bool)$_SESSION['chkFilterPIC'] : FALSE;
	$FilterVID = array_key_exists('chkFilterVID', $_SESSION) ? (bool)$_SESSION['chkFilterVID'] : FALSE;
}

$Sets = Set::GetSets(new SetSearchParameters(
	FALSE,
	FALSE,
	is_null($ModelID) ? FALSE : $ModelID,
	FALSE,
	$SearchModel));
$Dates = Date::GetDates(new DateSearchParameters(
	FALSE,
	FALSE,
	FALSE,
	FALSE,
	is_null($ModelID) ? FALSE : $ModelID,
	FALSE,
	FALSE,
	$SearchModel));

if($Sets)
{
	/* @var $Set Set */
	foreach($Sets as $Set)
	{
		if(!$Set->getSetIsDirty())
		{ continue; }

		if($FilterSPECIAL && ($Set->getModel()->GetFullName() == 'VIP' || $Set->getModel()->GetFullName() == 'Interviews' || $Set->getModel()->GetFullName() == 'Promotions'))
		{ continue; }

		if($FilterPIC && $Set->getSetIsDirtyPic() && !$Set->getSetIsDirtyVid())
		{ continue; }

		if($FilterVID && $Set->getSetIsDirtyVid() && !$Set->getSetIsDirtyPic())
		{ continue; }

		$DatesThisSet = Date::FilterDates($Dates, NULL, $ModelID, $Set->getID());

		if($SearchDate && strtotime($SearchDate) !== FALSE)
		{
			$DatesThisSet = Date::FilterDates($DatesThisSet, NULL, $ModelID, $Set->getID(), NULL, strtotime($SearchDate));

			if(!$DatesThisSet)
			{ continue; }
		}

		$SetCount++;
		$Model = $Set->getModel();
		$ImageCount += $Set->getAmountPicsInDB();
		$VideoCount += $Set->getAmountVidsInDB();

		if(isset($PreviousModel) && $Model->getID() != $PreviousModel->getID())
		{
			$SetRows .= "\n<tr><td colspan=\"10\" style=\"height:10px;\"></td></tr>";
			$SetCountModel = 0;
		}
		else
		{
			$SetCountModel++;
		}

		$PreviousModel = $Model;

		$SetRows .= sprintf(
		"\n<tr class=\"Row%11\$d\">".
			"<td><a href=\"model_view.php?model_id=%10\$d\" title=\"%9\$s\">%8\$s</a></td>".
			"<td><a href=\"set_view.php?model_id=%10\$d&amp;set_id=%1\$d\">%6\$s</a></td>".
	    	"<td><a href=\"set_view.php?model_id=%10\$d&amp;set_id=%1\$d\">%7\$s</a></td>".
			"<td><a href=\"set_view.php?model_id=%10\$d&amp;set_id=%1\$d\">%12\$s</a></td>".
			"<td class=\"Center\"><a href=\"image.php?model_id=%10\$d&amp;set_id=%1\$d\">%2\$d%4\$s</a></td>".
			"<td class=\"Center\"><a href=\"import_image.php?set_id=%1\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"%13\$s\" title=\"%13\$s\" /></a></td>".
			"<td class=\"Center\"><a href=\"video.php?model_id=%10\$d&amp;set_id=%1\$d\">%3\$d%5\$s</a></td>".
			"<td class=\"Center\"><a href=\"import_video.php?set_id=%1\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"%14\$s\" title=\"%14\$s\" /></a></td>".
			"<td class=\"Center\"><a href=\"https://www.binsearch.info/?q=%9\$s%7\$s&amp;max=100&amp;adv_age=&amp;server=2\" rel=\"external\"><img src=\"images/button_search.png\" width=\"16\" height=\"16\" alt=\"BinSearch.info\" title=\"BinSearch.info\" /></a></td>".
			"<td class=\"Center\"><a href=\"https://www.google.com/search?q=%9\$s%7\$s\" rel=\"external\"><img src=\"images/button_search.png\" width=\"16\" height=\"16\" alt=\"Google.com\" title=\"Google.com\" /></a></td>".
        "</tr>",
		$Set->getID(),
		$Set->getAmountPicsInDB(),
		$Set->getAmountVidsInDB(),
		$Set->getSetIsDirtyPic() ? '<em> !</em>' : NULL,
		$Set->getSetIsDirtyVid() ? '<em> !</em>' : NULL,
		htmlentities($Set->getPrefix()),
		htmlentities($Set->getName()),
		htmlentities($Model->GetFullName()),
		htmlentities($Model->GetShortName()),
		$Model->getID(),
		$SetCount % 2 == 0 ? 2 : 1,
		Date::FormatDates($DatesThisSet, 'Y-m-d', TRUE),
		$lang->g('ButtonImportImages'),
		$lang->g('ButtonImportVideos')
		);
	}
}

echo HTMLstuff::HtmlHeader('Dirty sets', $CurrentUser);

?>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post" class="FilterForm" style="text-align:center">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="DirtySetFilter" />

<label for="txtSearchModel"><?php echo $lang->g('LabelModel')?></label>
<input type="text" id="txtSearchModel" name="txtSearchModel" maxlength="50" style="width:130px;" value="<?php echo $SearchModel?>" />

<label for="txtSearchDate"><?php echo $lang->g('LabelDate')?></label>
<input type="text" id="txtSearchDate" name="txtSearchDate" class="DatePicker" maxlength="10" style="width:100px;" value="<?php echo $SearchDate?>" />
<br />
<label for="chkFilterSPECIAL">NO-SPECIALS</label>
<input type="checkbox" id="chkFilterSPECIAL" name="chkFilterSPECIAL"<?php echo HTMLstuff::CheckedStr($FilterSPECIAL)?> />
<label for="chkFilterPIC">NO-PICS</label>
<input type="checkbox" id="chkFilterPIC" name="chkFilterPIC"<?php echo HTMLstuff::CheckedStr($FilterPIC)?> />
<label for="chkFilterVID">NO-VIDS</label>
<input type="checkbox" id="chkFilterVID" name="chkFilterVID"<?php echo HTMLstuff::CheckedStr($FilterVID)?> />

<input type="submit" id="btnSearch" name="btnSearch" value="<?php echo $lang->g('ButtonSearch')?>" />

</fieldset>
</form>

<h2><?php echo sprintf('<a href="index.php">%1$s</a> - %2$s', $lang->g('NavigationHome'), $lang->g('NavigationDirtySets'))?></h2>

<table>
	<thead>
		<tr>
			<th><?php echo $lang->g('NavigationModel')?></th>
			<th style="width: 50px;"><?php echo $lang->g('LabelPrefix')?></th>
			<th style="width: 80px;"><?php echo $lang->g('NavigationSet')?></th>
			<th style="width: 230px;"><?php echo $lang->g('LabelDates')?></th>
			<th style="width: 40px;" class="Center">#PIC</th>
			<th style="width: 22px;" class="Center">&nbsp;</th>
			<th style="width: 40px;" class="Center">#VID</th>
			<th style="width: 22px;" class="Center">&nbsp;</th>
			<th style="width: 22px;" class="Center">&nbsp;</th>
			<th style="width: 22px;" class="Center">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="10"><?php echo $lang->g('LabelTotalSetCount')?>: <?php printf("%1\$d (%2\$d %4\$s, %3\$d %5\$s)", $SetCount, $ImageCount, $VideoCount, strtolower($lang->g('NavigationImages')), strtolower($lang->g('NavigationVideos')))?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php echo $SetRows ? $SetRows : '<tr class="Row1"><td colspan="10">&nbsp;</td></tr>'?>
	</tbody>
</table>

<?php
echo HTMLstuff::Button('index.php');
echo HTMLstuff::HtmlFooter($CurrentUser);
?>