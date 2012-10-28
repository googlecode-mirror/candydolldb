<?php
/* This file is part of CandyDollDB.

CandyDollDB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CandyDollDB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CandyDollDB. If not, see <http://www.gnu.org/licenses/>.
*/

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$ModelID = Utils::SafeIntFromQS('model_id');

if(!isset($ModelID))
{
	header('location:index.php');
	exit;
}

$Model = NULL;
$SetRows = '';
$SetCount = 0;
$Video = NULL;

$Videos = Video::GetVideos(new VideoSearchParameters(FALSE, FALSE, FALSE, FALSE, $ModelID));
$Sets = Set::GetSets(new SetSearchParameters(FALSE, FALSE,  $ModelID));
$Dates = Date::GetDates(new DateSearchParameters(FALSE, FALSE, FALSE, FALSE, $ModelID));

if($Sets)
{
	if($Sets[0]->getModel()->getFirstName() == 'VIP')
	{ usort($Sets, array('Set', 'CompareAsc')); }
	
	/* @var $Set Set */
	foreach($Sets as $Set)
	{
		$SetCount++;
		if(!$Model) { $Model = $Set->getModel(); }

		$DatesThisSet = Date::FilterDates($Dates, NULL, $ModelID, $Set->getID());
		$Video = Video::Filter($Videos, $ModelID, $Set->getID());

		if($Video)
		{ $Video = $Video[0]; }

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
			%27\$s
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
			%28\$s
			%29\$s
			%30\$s
			%31\$s
			%32\$s
			%33\$s
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
			($SetCount % 3 == 0 ? "<div class=\"Clear\"></div>" : NULL),
			($Set->getSetIsDirtyPic() ? " class=\"Dirty\"" : NULL),
			($Set->getSetIsDirtyVid() ? " class=\"Dirty\"" : NULL),
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
			$lang->g('LabelViewVideos'),
			
			$Video && $Set->getContainsWhat() == SET_CONTENT_VIDEO ?
				
				sprintf("<a href=\"video.php?model_id=%1\$d&amp;set_id=%2\$d\">".
					"<img src=\"download_image.php?video_id=%6\$d&amp;landscape_only=true&amp;width=225&amp;height=150\" height=\"150\" alt=\"%3\$s %4\$s %5\$s\" title=\"%3\$s %4\$s %5\$s\" />".
					"</a>",
					$Model->getID(),
					$Set->getID(),
					htmlentities($Model->GetFullName()),
					strtolower($lang->g('NavigationSet')),
					htmlentities($Set->getName()),
					$Video->getID())
				
				: sprintf("<a href=\"image.php?model_id=%1\$d&amp;set_id=%2\$d\">".
					"<img src=\"download_image.php?set_id=%2\$d&amp;landscape_only=true&amp;width=225&amp;height=150\" height=\"150\" alt=\"%3\$s %4\$s %5\$s\" title=\"%3\$s %4\$s %5\$s\" />".
					"</a>",
					$Model->getID(),
					$Set->getID(),
					htmlentities($Model->GetFullName()),
					strtolower($lang->g('NavigationSet')),
					htmlentities($Set->getName())
				),
			
			$CurrentUser->hasPermission(RIGHT_SET_EDIT) ?
				sprintf("<a href=\"set_view.php?model_id=%1\$d&amp;set_id=%2\$d\"><img src=\"images/button_edit.png\" width=\"16\" height=\"16\" title=\"%3\$s\" alt=\"%3\$s\"/></a>", $ModelID, $Set->getID(), $lang->g('LabelEditSet')) :
				sprintf("<a href=\"#\"><img src=\"images/button_edit_invalid.png\" width=\"16\" height=\"16\" title=\"%1\$s\" alt=\"%1\$s\"/></a>", $lang->g('LabelNotAllowed')),
			
			$CurrentUser->hasPermission(RIGHT_SET_DELETE) ?
				sprintf("<a href=\"set_view.php?model_id=%1\$d&amp;set_id=%2\$d&amp;cmd=%4\$s\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" title=\"%3\$s\" alt=\"%3\$s\"/></a>", $ModelID, $Set->getID(), $lang->g('LabelDeleteSet'), COMMAND_DELETE) :
				sprintf("<a href=\"#\"><img src=\"images/button_delete_invalid.png\" width=\"16\" height=\"16\" title=\"%1\$s\" alt=\"%1\$s\"/></a>", $lang->g('LabelNotAllowed')),
		
			$CurrentUser->hasPermission(RIGHT_IMAGE_ADD) ?
				sprintf("<a href=\"import_image.php?model_id=%1\$d&amp;set_id=%2\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"%3\$s\" title=\"%3\$s\" /></a>", $ModelID, $Set->getID(),$lang->g('ButtonImportImages')) :
				sprintf("<a href=\"#\"><img src=\"images/button_upload_invalid.png\" width=\"16\" height=\"16\" alt=\"%1\$s\" title=\"%1\$s\" /></a>",$lang->g('LabelNotAllowed')),
        	
			$CurrentUser->hasPermission(RIGHT_VIDEO_ADD) ?
				sprintf("<a href=\"import_video.php?model_id=%1\$d&amp;set_id=%2\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" alt=\"%3\$s\" title=\"%3\$s\" /></a>",$ModelID, $Set->getID(), $lang->g('ButtonImportVideos')) :
				sprintf("<a href=\"#\"><img src=\"images/button_upload_invalid.png\" width=\"16\" height=\"16\" alt=\"%1\$s\" title=\"%1\$s\" /></a>",$lang->g('LabelNotAllowed')),
				
			$CurrentUser->hasPermission(RIGHT_EXPORT_ZIP) ?
				sprintf("<a href=\"download_zip.php?model_id=%1\$d&amp;set_id=%2\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"%3\$s\" title=\"%3\$s\" /></a>",$ModelID, $Set->getID(),$lang->g('LabelDownloadImages')) :
				sprintf("<a href=\"#\"><img src=\"images/button_download_invalid.png\" width=\"16\" height=\"16\" title=\"%1\$s\" alt=\"%1\$s\"/></a>",$lang->g('LabelNotAllowed')),

			$CurrentUser->hasPermission(RIGHT_EXPORT_VIDEO) ?
				sprintf("<a href=\"download_vid.php?set_id=%1\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" title=\"%2\$s\" alt=\"%2\$s\"/></a>", $Set->getID(), $lang->g('LabelDownloadVideo')) :
				sprintf("<a href=\"#\"><img src=\"images/button_download_invalid.png\" width=\"16\" height=\"16\" title=\"%1\$s\" alt=\"%1\$s\"/></a>",$lang->g('LabelNotAllowed'))
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
		$Model->GetShortName(TRUE),
		$lang->g('NavigationSets')
	),
	$CurrentUser
);

?>

<h2><?php echo sprintf(
	'<a href="index.php">%3$s</a> - <a href="model_view.php?model_id=%1$d">%2$s</a> - %4$s',
	$ModelID,
	htmlentities($Model->GetShortName(TRUE)),
	$lang->g('NavigationHome'),
	$lang->g('NavigationSets')
)?></h2>

<div class="Separator"></div>

<?php

echo $SetRows . "<div class=\"Clear\"></div>";

echo $CurrentUser->hasPermission(RIGHT_SET_ADD) ? HTMLstuff::Button(sprintf('set_view.php?model_id=%1$d', $ModelID), $lang->g('ButtonNewSet')) : '';

echo $CurrentUser->hasPermission(RIGHT_IMAGE_ADD) ? HTMLstuff::Button(sprintf('import_image.php?model_id=%1$d', $ModelID), $lang->g('ButtonImportImages')) : '';

echo $CurrentUser->hasPermission(RIGHT_VIDEO_ADD) ? HTMLstuff::Button(sprintf('import_video.php?model_id=%1$d', $ModelID), $lang->g('ButtonImportVideos')) : '';

echo HTMLstuff::Button('index.php');

echo HTMLstuff::HtmlFooter($CurrentUser);

?>
