<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();
$ModelID = Utils::SafeIntFromQS('model_id');
$IncludeImages = Utils::SafeBoolFromQS('includeimages');
$IncludeVideos = Utils::SafeBoolFromQS('includevideos');


$Models = Model::GetModels();
$Sets = Set::GetSets();
$Dates = Date::GetDates();
$Tag2Alls = Tag2All::GetTag2Alls();


header('Content-Type: text/xml');
header('Content-Disposition: attachment; filename=CandyDollDB.xml');


$xmlw = new XMLWriter();
$xmlw->openUri('php://output');
$xmlw->setIndent(true);
$xmlw->setIndentString("\t");
$xmlw->startDocument('1.0', 'UTF-8');

$xmlw->startElement('Models');
$xmlw->writeAttributeNs('xmlns', 'xsi', null, 'http://www.w3.org/2001/XMLSchema-instance');
$xmlw->writeAttributeNs('xsi', 'noNamespaceSchemaLocation', null, 'candydolldb.xsd');
$xmlw->writeAttribute('xmlns', null);


/* @var $Model Model */
foreach ($Models as $Model)
{
	// Provide a one-model-only export for impatient developers
	if($ModelID && $Model->getID() !== $ModelID)
	{ continue; }
	
	$xmlw->startElement('Model');
	$xmlw->writeAttribute('firstname', $Model->getFirstName());
	$xmlw->writeAttribute('lastname', $Model->getLastName());
	$xmlw->writeAttribute('birthdate', $Model->getBirthdate() > 0 ? date('Y-m-d', $Model->getBirthdate()) : null);
	
	$TagsThisModel = Tag2All::FilterTag2Alls($Tag2Alls, null, $Model->getID(), null, null, null);
	$xmlw->writeAttribute('tags', Tag2All::Tags2AllCSV($TagsThisModel));
	
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
			$TagsThisSet = Tag2All::FilterTag2Alls($Tag2Alls, null, null, $Set->getID(), null, null);
			
			$xmlw->startElement('Set');
				$xmlw->writeAttribute('name', $Set->getName());
				$xmlw->writeAttribute('date_pic', Date::FormatDates($PicDatesThisSet, 'Y-m-d', false, ' '));
				$xmlw->writeAttribute('date_vid', Date::FormatDates($VidDatesThisSet, 'Y-m-d', false, ' '));
				$xmlw->writeAttribute('tags', Tag2All::Tags2AllCSV($TagsThisSet));
				
			if($IncludeImages)
			{
				$where = sprintf('model_id = %1$d AND set_id = %2$d AND mut_deleted = -1', $Model->getID(), $Set->getID());
				$ImagesThisSet = Image::GetImages($where);
				
				if($ImagesThisSet)
				{
					$xmlw->startElement('Images');
					
					/* @var $Image Image */
					foreach($ImagesThisSet as $Image)
					{
						$TagsThisImage = Tag2All::FilterTag2Alls($Tag2Alls, null, null, null, $Image->getID(), null);
						
						$xmlw->startElement('Image');
							$xmlw->writeAttribute('name', $Image->getFileName());
							$xmlw->writeAttribute('extension', $Image->getFileExtension());
							$xmlw->writeAttribute('filesize', $Image->getFileSize());
							$xmlw->writeAttribute('height', $Image->getImageHeight());
							$xmlw->writeAttribute('width', $Image->getImageWidth());
							$xmlw->writeAttribute('checksum', $Image->getFileCheckSum());
							$xmlw->writeAttribute('tags', Tag2All::Tags2AllCSV($TagsThisImage));
						$xmlw->endElement();
					}
					$xmlw->endElement();
					unset($ImagesThisSet);
				}
			}
			
			if($IncludeVideos)
			{
				$where = sprintf('model_id = %1$d AND set_id = %2$d AND mut_deleted = -1', $Model->getID(), $Set->getID());
				$VideosThisSet = Video::GetVideos($where);
				
				if($VideosThisSet)
				{
					$xmlw->startElement('Videos');
					
					/* @var $Video Video */
					foreach($VideosThisSet as $Video)
					{
						$TagsThisVideo = Tag2All::FilterTag2Alls($Tag2Alls, null, null, null, null, $Video->getID());
						
						$xmlw->startElement('Video');
							$xmlw->writeAttribute('name', $Video->getFileName());
							$xmlw->writeAttribute('extension', $Video->getFileExtension());
							$xmlw->writeAttribute('filesize', $Video->getFileSize());
							$xmlw->writeAttribute('checksum', $Video->getFileCheckSum());
							$xmlw->writeAttribute('tags', Tag2All::Tags2AllCSV($TagsThisVideo));
						$xmlw->endElement();
					}
					$xmlw->endElement();
					unset($VideosThisSet);
				}
			}
			$xmlw->endElement();
		}
		$xmlw->endElement();
		
		if($Model->getRemarks())
		{
			$xmlw->startElement('Remarks');
			$xmlw->text($Model->getRemarks());
			$xmlw->endElement();
		}
	}
	$xmlw->endElement();
	$xmlw->flush();
}

$xmlw->endElement();
$xmlw->endDocument();

exit;

?>