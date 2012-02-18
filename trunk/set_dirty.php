<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();


$ModelID = Utils::SafeIntFromQS('model_id');


$Model = null;
$SearchModel = '';
$SearchDate = '';
$FilterVIP = false;
$SetRows = '';
$SetCount = 0;
$ImageCount = 0;
$VideoCount = 0;



if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'DirtySetFilter')
{
	$SearchModel = $_SESSION['txtSearchModel'] 	= $_POST['txtSearchModel'];
	$SearchDate = $_SESSION['txtSearchDate'] 	= $_POST['txtSearchDate'];
	$FilterVIP = $_SESSION['chkFilterVIP'] 		= array_key_exists('chkFilterVIP', $_POST);
}
else
{
	$SearchModel = array_key_exists('txtSearchModel', $_SESSION) ? $_SESSION['txtSearchModel'] : '';
	$SearchDate = array_key_exists('txtSearchDate', $_SESSION) ? $_SESSION['txtSearchDate'] : '';
	$FilterVIP = array_key_exists('chkFilterVIP', $_SESSION) ? (bool)$_SESSION['chkFilterVIP'] : true;
}

$WhereClause = sprintf(
	"model_id = IFNULL(%1\$s, model_id) AND CONCAT_WS(' ', model_firstname, model_lastname) LIKE '%%%2\$s%%' AND mut_deleted = -1",
	($ModelID ? $ModelID : 'NULL'),
	mysql_real_escape_string($SearchModel)
);

$Sets = Set::GetSets($WhereClause);
$Dates = Date::GetDates($WhereClause);

if($Sets)
{
	/* @var $Set Set */
	foreach($Sets as $Set)
	{
		if(!$Set->getSetIsDirty())
		{ continue; }
		
		if($FilterVIP && $Set->getModel()->GetFullName() == 'VIP')
		{ continue; }
		
		$DatesThisSet = Date::FilterDates($Dates, null, $ModelID, $Set->getID());
		
		if($SearchDate && strtotime($SearchDate) !== false)
		{
			$DatesThisSet = Date::FilterDates($DatesThisSet, null, $ModelID, $Set->getID(), null, strtotime($SearchDate));
			
			if(!$DatesThisSet)
			{ continue; }
		}
		
		$SetCount++;
		$Model = $Set->getModel();
		$ImageCount += $Set->getAmountPicsInDB();
		$VideoCount += $Set->getAmountVidsInDB();
		

		$SetRows .= sprintf(
		"\n<tr class=\"Row%11\$d\">".
			"<td><a href=\"model_view.php?model_id=%10\$d\" title=\"%9\$s\">%8\$s</a></td>".
			"<td><a href=\"set_view.php?model_id=%10\$d&amp;set_id=%1\$d\">%6\$s</a></td>".
	    	"<td><a href=\"set_view.php?model_id=%10\$d&amp;set_id=%1\$d\">%7\$s</a></td>".
			"<td><a href=\"set_view.php?model_id=%10\$d&amp;set_id=%1\$d\">%12\$s</a></td>".
			"<td class=\"Center\"><a href=\"image.php?model_id=%10\$d&amp;set_id=%1\$d\">%2\$d%4\$s</a></td>".
			"<td class=\"Center\"><a href=\"import_image.php?set_id=%1\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"Import set's images\" title=\"Import set's images\" /></a></td>".
			"<td class=\"Center\"><a href=\"video.php?model_id=%10\$d&amp;set_id=%1\$d\">%3\$d%5\$s</a></td>".
			"<td class=\"Center\"><a href=\"import_video.php?set_id=%1\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"Import set's videos\" title=\"Import set's videos\" /></a></td>".
			"<td class=\"Center\"><a href=\"https://www.binsearch.info/?q=%9\$s%7\$s&amp;max=100&adv_age=&amp;server=2\" rel=\"external\"><img src=\"images/button_search.png\" width=\"16\" height=\"16\" alt=\"Search on BinSearch.info\" title=\"Search on BinSearch.info\" /></a></td>".
			"<td class=\"Center\"><a href=\"http://www.google.com/search?q=%9\$s%7\$s\" rel=\"external\"><img src=\"images/button_search.png\" width=\"16\" height=\"16\" alt=\"Search on Google.com\" title=\"Search on Google.com\" /></a></td>".
        "</tr>",
		$Set->getID(),
		$Set->getAmountPicsInDB(),
		$Set->getAmountVidsInDB(),
		$Set->getSetIsDirtyPic() ? '<em> !</em>' : null,
		$Set->getSetIsDirtyVid() ? '<em> !</em>' : null,
		htmlentities($Set->getPrefix()),
		htmlentities($Set->getName()),
		htmlentities($Model->GetFullName()),
		htmlentities($Model->GetShortName()),
		$Model->getID(),
		$SetCount % 2 == 0 ? 2 : 1,
		Date::FormatDates($DatesThisSet, 'Y-m-d', true)
		);
	}
}

echo HTMLstuff::HtmlHeader('Dirty sets', $CurrentUser);

?>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="FilterForm">
<fieldset>

<legend>Find specific sets:</legend>
<input type="hidden" id="hidAction" name="hidAction" value="DirtySetFilter" />

<label for="txtSearchModel">Model</label>
<input type="text" id="txtSearchModel" name="txtSearchModel" maxlength="50" style="width:130px;" value="<?php echo $SearchModel; ?>" />

<label for="txtSearchDate">Date</label>
<input type="text" id="txtSearchDate" name="txtSearchDate" class="DatePicker" maxlength="10" style="width:100px;" value="<?php echo $SearchDate; ?>" />

<label for="chkFilterVIP">NO-VIP</label>
<input type="checkbox" id="chkFilterVIP" name="chkFilterVIP"<?php echo HTMLstuff::CheckedStr($FilterVIP); ?> />

<input type="submit" id="btnSearch" name="btnSearch" value="Search" />

</fieldset>
</form>

<h2><?php echo sprintf('<a href="index.php">Home</a> - Dirty sets'); ?></h2>

<table border="0" cellpadding="4" cellspacing="0">
	<thead>
		<tr>
			<th>Modelname</th>
			<th style="width: 50px;">Prefix</th>
			<th style="width: 100px;">Setname</th>
			<th style="width: 210px;">Dates</th>
			<th style="width: 80px;"># Img DB</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 80px;"># Vid DB</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="10">Total set count: <?php printf("%1\$d (%2\$d images, %3\$d videos)", $SetCount, $ImageCount, $VideoCount); ?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php echo $SetRows ? $SetRows : '<tr class="Row1"><td colspan="10">&nbsp;</td></tr>'; ?>
	</tbody>
</table>

<?php
echo HTMLstuff::Button('index.php');

echo HTMLstuff::HtmlFooter($CurrentUser);
?>
