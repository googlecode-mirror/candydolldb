<?php
/*	This file is part of CandyDollDB.

CandyDollDB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CandyDollDB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CandyDollDB.  If not, see <http://www.gnu.org/licenses/>.
*/

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$ModelID = Utils::SafeIntFromQS('model_id');

if(!isset($ModelID))
{
	header('location:index.php');
	exit;
}

$Model = null;
$SetRows = '';
$SetCount = 0;

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

		$DatesThisSet = Date::FilterDates($Dates, null, $ModelID, $Set->getID());
	
		$DatesOutput = '';
		if($DatesThisSet)
		{
			$DatesOutput = '<ul>';
			
			/* @var $date Date */
			foreach ($DatesThisSet as $date) {
				$DatesOutput .= sprintf(
					"<li>%1\$s (%2\$s)</li>",
					date('j F Y', $date->getTimeStamp()),
					($date->getDateKind() == DATE_KIND_VIDEO ? 'V' : ($date->getDateKind() == DATE_KIND_IMAGE ? 'P' : '?'))
				);
			}

			$DatesOutput .= '</ul>';
		}
		
		$SetRows .= sprintf(
			"<div class=\"SetThumbGalItem\">
			<h3 class=\"Hidden\">%1\$s set %2\$s</h3>
			
			<div class=\"SetThumbImageWrapper\">
			<a href=\"image.php?model_id=%8\$d&amp;set_id=%6\$d\">
			<img src=\"download_image.php?set_id=%6\$d&amp;landscape_only=true&amp;width=225&amp;height=150\" height=\"150\" alt=\"%1\$s set %2\$s\" title=\"%1\$s set %2\$s\" />
			</a>
			</div>
			
			<div class=\"SetThumbDataWrapper\">
			<ul>
			<li>Prefix: %3\$s</li>
			<li>Name: %2\$s</li>
			<li>Dates %9\$s</li>
			<li%11\$s>Images: %4\$d</li>
			<li%12\$s>Videos: %5\$d</li>
			</ul>
			</div>
			
			<div class=\"SetThumbButtonWrapper\">
			<a href=\"set_view.php?model_id=%8\$d&amp;set_id=%6\$d\"><img src=\"images/button_edit.png\" width=\"16\" height=\"16\" title=\"Edit set\" alt=\"Edit set\"/></a>
			<a href=\"set_view.php?model_id=%8\$d&amp;set_id=%6\$d&amp;cmd=%7\$s\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" title=\"Delete set\" alt=\"Delete set\"/></a>
			<a href=\"import_image.php?set_id=%6\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" title=\"Import images\" alt=\"Import images\"/></a>
			<a href=\"import_video.php?set_id=%6\$d\"><img src=\"images/button_upload.png\" width=\"16\" height=\"16\" title=\"Import videos\" alt=\"Import videos\"/></a>
			<a href=\"download_zip.php?set_id=%6\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" title=\"Download images\" alt=\"Download images\"/></a>
			<a href=\"download_vid.php?set_id=%6\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" title=\"Download videos\"alt=\"Download videos\"/></a>
			<a href=\"image.php?model_id=%8\$d&amp;set_id=%6\$d\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" title=\"View images\"alt=\"View images\"/></a>
			<a href=\"video.php?model_id=%8\$d&amp;set_id=%6\$d\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" title=\"View videos\"alt=\"View videos\"/></a>
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
			($Set->getSetIsDirtyVid() ? " class=\"Dirty\"" : null)
		);
	}
}
else
{
	$WhereClause = sprintf('model_id = %1$d AND mut_deleted = -1', $ModelID);
	$Models = Model::GetModels($WhereClause);
	if($Models) { $Model = $Models[0]; }
}

if(!$Model){
	header('location:index.php');
	exit;
}

echo HTMLstuff::HtmlHeader($Model->GetShortName(true).' - Sets', $CurrentUser);

?>

<h2><?php echo sprintf('<a href="index.php">Home</a> - <a href="model_view.php?model_id=%1$d">%2$s</a> - Sets',
	$ModelID,
	htmlentities($Model->GetShortName(true))
); ?></h2>

<div class="Separator"></div>

<?php

echo $SetRows . "<div class=\"Clear\"></div>";

echo HTMLstuff::Button(sprintf('set_view.php?model_id=%1$d', $ModelID), 'New set');

echo HTMLstuff::Button(sprintf('import_image.php?model_id=%1$d', $ModelID), 'Import all images');

echo HTMLstuff::Button(sprintf('import_video.php?model_id=%1$d', $ModelID), 'Import all videos');

echo HTMLstuff::Button('index.php');

echo HTMLstuff::HtmlFooter($CurrentUser);

?>