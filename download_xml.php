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


$Models = Model::GetModels();
$Sets = Set::GetSets();
$Dates = Date::GetDates();
//$Images = Image::GetImages();
//$Videos = Video::GetVideos();


$xmlw = new XMLWriter();
$xmlw->openMemory();
$xmlw->setIndent(true);
$xmlw->setIndentString("\t");
$xmlw->startDocument('1.0', 'UTF-8');

$xmlw->startElement('Models');
$xmlw->writeAttributeNs('xmlns', 'xsi', null, 'http://www.w3.org/2001/XMLSchema-instance');
$xmlw->writeAttributeNs('xsi', 'noNamespaceSchemaLocation', null, 'cdtvdb.xsd');
$xmlw->writeAttribute('xmlns', null);


/* @var $Model Model */
foreach ($Models as $Model)
{
	$xmlw->startElement('Model');
	$xmlw->writeAttribute('firstname', $Model->getFirstName());
	$xmlw->writeAttribute('lastname', $Model->getLastName());
	$xmlw->writeAttribute('birthdate', $Model->getBirthdate() > 0 ? date('Y-m-d', $Model->getBirthdate()) : null);
	
	$SetsThisModel = Set::FilterSets($Sets, $Model->getID());
	if($SetsThisModel)
	{
		$xmlw->startElement('Sets');
		
		$DatesThisModel = Date::FilterDates($Dates, null, $Model->getID());
		
		/* @var $Set Set */
		foreach ($SetsThisModel as $Set)
		{
			$PicDatesThisSet = Date::FilterDates($DatesThisModel, null, null, $Set->getID(), DATE_KIND_IMAGE);
			$VidDatesThisSet = Date::FilterDates($DatesThisModel, null, null, $Set->getID(), DATE_KIND_VIDEO);
			
			$xmlw->startElement('Set');
				$xmlw->writeAttribute('name', $Set->getName());
				$xmlw->writeAttribute('date_pic', Date::FormatDates($PicDatesThisSet, 'Y-m-d', false, ' '));
				$xmlw->writeAttribute('date_vid', Date::FormatDates($VidDatesThisSet, 'Y-m-d', false, ' '));
			$xmlw->endElement();
		}
		$xmlw->endElement();
	}
	$xmlw->endElement();
}

$xmlw->endElement();
$xmlw->endDocument();

header('Content-Type: text/xml');
header('Content-Disposition: attachment; filename=CandyDollDB.xml');
echo $xmlw->outputMemory(true);
exit;

?>