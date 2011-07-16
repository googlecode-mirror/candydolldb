<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

if(!array_key_exists('model_id', $_GET) || !$_GET['model_id'] || !is_numeric($_GET['model_id'])){
	header('location:index.php');
}

$ModelID = (int)$_GET['model_id'];
$Model = null;
$SetRows = '';
$SetCount = 0;
$ImageCount = 0;
$VideoCount = 0;

$WhereClause = sprintf('model_id = %1$d AND mut_deleted = -1', $ModelID);
$Sets = Set::GetSets($WhereClause);
$Dates = Date::GetDates($WhereClause);


if($Sets)
{
	/* @var $Set Set */
	foreach($Sets as $Set)
	{
		$SetCount++;
		if(!$Model) { $Model = $Set->getModel(); }

		$ImageCount += $Set->getAmountPicsInDB();
		$VideoCount += $Set->getAmountVidsInDB();

		$DatesThisSet = Date::FilterDates($Dates, $ModelID, $Set->getID());
		$DatesImage = Date::FilterDates($DatesThisSet, null, null, DATE_KIND_IMAGE);
		$DatesVideo = Date::FilterDates($DatesThisSet, null, null, DATE_KIND_VIDEO);
		
		$SetRows .= sprintf(
		"\n<tr class=\"Row%15\$d\">".
        	"<td><a href=\"set_view.php?model_id=%13\$d&amp;set_id=%1\$d\">%9\$s</a></td>".
	    	"<td><a href=\"set_view.php?model_id=%13\$d&amp;set_id=%1\$d\">%10\$s</a></td>".
        	"<td><a href=\"set_view.php?model_id=%13\$d&amp;set_id=%1\$d\">%4\$s</a></td>".
			"<td><a href=\"set_view.php?model_id=%13\$d&amp;set_id=%1\$d\">%16\$s</a></td>".
			"<td class=\"Center\"%6\$s><a href=\"image.php?model_id=%13\$d&amp;set_id=%1\$d\">%2\$d%5\$s</a></td>".
			"<td class=\"Center\"><a href=\"import_image.php?set_id=%1\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"Import set's images\" title=\"Import set's images\" /></a></td>".
			"<td class=\"Center\"><a href=\"download_zip.php?set_id=%1\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"Download set's images\" title=\"Download set's images\" /></a></td>".
			"<td class=\"Center\"%8\$s><a href=\"video.php?model_id=%13\$d&amp;set_id=%1\$d\">%3\$d%7\$s</a></td>".
			"<td class=\"Center\"><a href=\"import_video.php?set_id=%1\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"Import set's videos\" title=\"Import set's videos\" /></a></td>".
			"<td class=\"Center\"><a href=\"download_vid.php?set_id=%1\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"Download set's video\" title=\"Download set's video\" /></a></td>".
			"<td class=\"Center\"><a href=\"set_view.php?model_id=%13\$d&amp;set_id=%1\$d&amp;cmd=%14\$s\" title=\"Delete set\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"Delete\" /></a></td>".
        "</tr>",
		$Set->getID(),
		$Set->getAmountPicsInDB(),
		$Set->getAmountVidsInDB(),
	 	Date::FormatDates($DatesImage, 'j F Y'),
		$Set->getSetIsDirtyPic() ? '<em> !</em>' : null,
		$Set->getSetIsDirtyPic() ? ' title="Incomplete or empty set"' : null,
		$Set->getSetIsDirtyVid() ? '<em> !</em>' : null,
		$Set->getSetIsDirtyVid() ? ' title="Incomplete or empty set"' : null,
		htmlentities($Set->getPrefix()),
		htmlentities($Set->getName()),
		htmlentities($Model->GetFullName()),
		htmlentities($Model->GetShortName()),
		$Model->getID(),
		COMMAND_DELETE,
		$SetCount % 2 == 0 ? 2 : 1,
		Date::FormatDates($DatesVideo, 'j F Y')
		);
	}
}
else
{
	$WhereClause = sprintf('model_id = %1$d AND mut_deleted = -1', $ModelID);
	$Models = Model::GetModels($WhereClause);
	if($Models) { $Model = $Models[0]; }
}

echo HTMLstuff::HtmlHeader($Model->GetShortName().' - Sets', $CurrentUser);

?>

<h2><?php echo sprintf('<a href="index.php">Home</a> - <a href="model_view.php?model_id=%1$d">%2$s</a> - Sets',
	$ModelID,
	htmlentities($Model->GetShortName(true))
); ?></h2>

<table border="0" cellpadding="4" cellspacing="0">
	<thead>
		<tr>
			<th style="width: 80px;">Prefix</th>
			<th>Name</th>
			<th style="width: 160px;">Date (pics)</th>
			<th style="width: 160px;">Date (vids)</th>
			<th style="width: 80px;"># Img DB</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 80px;"># Vid DB</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="11">Total set count: <?php printf("%1\$d (%2\$d images, %3\$d videos)", $SetCount, $ImageCount, $VideoCount); ?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php echo $SetRows ? $SetRows : '<tr class="Row1"><td colspan="11">&nbsp;</td></tr>'; ?>
	</tbody>
</table>

<?php
echo HTMLstuff::Button(sprintf('set_view.php?model_id=%1$d', $ModelID), 'New set');

echo HTMLstuff::Button(sprintf('import_image.php?model_id=%1$d', $ModelID), 'Import all images');

echo HTMLstuff::Button(sprintf('import_video.php?model_id=%1$d', $ModelID), 'Import all videos');

echo HTMLstuff::Button('index.php');

echo HTMLstuff::HtmlFooter();
?>
