<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$ModelID = Utils::SafeIntFromQS('model_id');

if(!isset($ModelID))
{
	header('location:index.php');
	exit;
}

$Model = null;
$SetRows = '';
$SetCount = 0;

$Sets = Set::GetSets(new SetSearchParameters(null, null,  $ModelID));
$Dates = Date::GetDates(new DateSearchParameters(null, null, null, null, $ModelID));

if($Sets)
{
	if($Sets[0]->getModel()->getFirstName() == 'VIP')
	{ usort($Sets, array('Set', 'CompareAsc')); }
	
	/* @var $Set Set */
	foreach($Sets as $Set)
	{
		$SetCount++;
		if(!$Model) { $Model = $Set->getModel(); }

		$DatesThisSet = Date::FilterDates($Dates, null, $ModelID, $Set->getID());
	
		$DatesOutput = '';
		if($DatesThisSet)
		{
			$DatesOutput = '<ul>';
			
			/* @var $date Date */
			foreach ($DatesThisSet as $date) {
				$DatesOutput .= sprintf(
					"<li>%1\$s (%2\$s)</li>",
					date($CurrentUser->getDateFormat(), $date->getTimeStamp()),
					($date->getDateKind() == DATE_KIND_VIDEO ? 'V' : ($date->getDateKind() == DATE_KIND_IMAGE ? 'P' : '?'))
				);
			}

			$DatesOutput .= '</ul>';
		}
		
		$SetRows .= sprintf(
			"<div class=\"SetThumbGalItem\">
			<h3 class=\"Hidden\">%1\$s %13\$s %2\$s</h3>
			
			<div class=\"SetThumbImageWrapper\">
			<a href=\"image.php?model_id=%8\$d&amp;set_id=%6\$d\">
			<img src=\"download_image.php?set_id=%6\$d&amp;landscape_only=true&amp;width=225&amp;height=150\" height=\"150\" alt=\"%1\$s %13\$s %2\$s\" title=\"%1\$s %13\$s %2\$s\" />
			</a>
			</div>
			
			<div class=\"SetThumbDataWrapper\">
			<ul>
			<li>%14\$s: %3\$s</li>
			<li>%15\$s: %2\$s</li>
			<li>%16\$s %9\$s</li>
			<li%11\$s>%17\$s: %4\$d</li>
			<li%12\$s>%18\$s: %5\$d</li>
			</ul>
			</div>
			
			<div class=\"SetThumbButtonWrapper\">
			<a href=\"set_view.php?model_id=%8\$d&amp;set_id=%6\$d\"><img src=\"images/button_edit.png\" width=\"16\" height=\"16\" title=\"%19\$s\" alt=\"%19\$s\"/></a>
			<a href=\"set_view.php?model_id=%8\$d&amp;set_id=%6\$d&amp;cmd=%7\$s\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" title=\"%20\$s\" alt=\"%20\$s\"/></a>
			<a href=\"import_image.php?set_id=%6\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" title=\"%21\$s\" alt=\"%21\$s\"/></a>
			<a href=\"import_video.php?set_id=%6\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" title=\"%22\$s\" alt=\"%22\$s\"/></a>
			<a href=\"download_zip.php?set_id=%6\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" title=\"%23\$s\" alt=\"%23\$s\"/></a>
			<a href=\"download_vid.php?set_id=%6\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" title=\"%24\$s\" alt=\"%24\$s\"/></a>
			<a href=\"image.php?model_id=%8\$d&amp;set_id=%6\$d\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" title=\"%25\$s\" alt=\"%25\$s\"/></a>
			<a href=\"video.php?model_id=%8\$d&amp;set_id=%6\$d\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" title=\"%26\$s\" alt=\"%26\$s\"/></a>
			</div>
			
			</div>
			
			%10\$s",
			
			htmlentities($Model->GetFullName()),
			htmlentities($Set->getName()),
			htmlentities($Set->getPrefix()),
			$Set->getAmountPicsInDB(),
			$Set->getAmountVidsInDB(),
			$Set->getID(),
			COMMAND_DELETE,
			$Model->getID(),
			$DatesOutput,
			($SetCount % 3 == 0 ? "<div class=\"Clear\"></div>" : null),
			($Set->getSetIsDirtyPic() ? " class=\"Dirty\"" : null),
			($Set->getSetIsDirtyVid() ? " class=\"Dirty\"" : null),
			strtolower($lang->g('NavigationSet')),
			$lang->g('LabelPrefix'),
			$lang->g('LabelName'),
			$lang->g('LabelDates'),
			$lang->g('NavigationImages'),
			$lang->g('NavigationVideos'),
			$lang->g('LabelEditSet'),
			$lang->g('LabelDeleteSet'),
			$lang->g('ButtonImportImages'),
			$lang->g('ButtonImportVideos'),
			$lang->g('LabelDownloadImages'),
			$lang->g('LabelDownloadVideos'),
			$lang->g('LabelViewImages'),
			$lang->g('LabelViewVideos')
		);
	}
}
else
{
	$Models = Model::GetModels(new ModelSearchParameters($ModelID));
	if($Models) { $Model = $Models[0]; }
}

if(!$Model){
	header('location:index.php');
	exit;
}

echo HTMLstuff::HtmlHeader(sprintf('%1$s - %2$s',
		$Model->GetShortName(true),
		$lang->g('NavigationSets')
	),
	$CurrentUser
);

?>

<h2><?php echo sprintf(
	'<a href="index.php">%3$s</a> - <a href="model_view.php?model_id=%1$d">%2$s</a> - %4$s',
	$ModelID,
	htmlentities($Model->GetShortName(true)),
	$lang->g('NavigationHome'),
	$lang->g('NavigationSets')
)?></h2>

<div class="Separator"></div>

<?php

echo $SetRows . "<div class=\"Clear\"></div>";

echo HTMLstuff::Button(sprintf('set_view.php?model_id=%1$d', $ModelID), $lang->g('ButtonNewSet'));

echo HTMLstuff::Button(sprintf('import_image.php?model_id=%1$d', $ModelID), $lang->g('ButtonImportImages'));

echo HTMLstuff::Button(sprintf('import_video.php?model_id=%1$d', $ModelID), $lang->g('ButtonImportVideos'));

echo HTMLstuff::Button('index.php');

echo HTMLstuff::HtmlFooter($CurrentUser);

?>