<?php

include('cd.php');
ini_set('max_execution_time', '3600');
ob_start();
$CurrentUser = Authentication::Authenticate();

$ModelID = Utils::SafeIntFromQS('model_id');
$IncludeImages = Utils::SafeBoolFromQS('includeimages');
$IncludeVideos = Utils::SafeBoolFromQS('includevideos');
$TaggedOnly = Utils::SafeBoolFromQS('taggedonly');

$Models = Model::GetModels(new ModelSearchParameters(
	is_null($ModelID) ? FALSE : $ModelID));
$Sets = Set::GetSets(new SetSearchParameters(
	FALSE,
	FALSE,
	is_null($ModelID) ? FALSE : $ModelID));
$Dates = Date::GetDates();
$Tag2Alls = Tag2All::GetTag2Alls();


$outfile = 'CandyDollDB.xml';
if($ModelID && count($Models) > 0){
	$Model = $Models[0];
	$outfile = sprintf('CandyDollDB %1$s%2$s%3$s.xml',
		$Model->GetFullName(),
		(($IncludeImages && $IncludeVideos) ? ' '.$lang->g('LabelComplete') : ($IncludeImages ? ' '.$lang->g('NavigationImages') : ($IncludeVideos ? ' '.$lang->g('NavigationVideos') : NULL))),
		(($IncludeImages || $IncludeVideos) && $TaggedOnly) ? ' '.$lang->g('LabelTagged') : NULL
	);
}

header('Content-Type: text/xml');
header(sprintf('Content-Disposition: attachment; filename="%1$s"', $outfile));

$xmlw = new XMLWriter();
$xmlw->openUri('php://output');
$xmlw->setIndent(TRUE);
$xmlw->setIndentString("\t");
$xmlw->startDocument('1.0', 'UTF-8');

$xmlw->startElement('Models');
$xmlw->writeAttributeNs('xmlns', 'xsi', NULL, 'http://www.w3.org/2001/XMLSchema-instance');
$xmlw->writeAttributeNs('xsi', 'noNamespaceSchemaLocation', NULL, 'candydolldb.xsd');
$xmlw->writeAttribute('xmlns', NULL);


function XmlOutputModel($Model,$TaggedOnly)
{
	global $xmlw, $Sets, $Dates, $Tag2Alls, $IncludeImages, $IncludeVideos;
	
	$xmlw->startElement('Model');
	$xmlw->writeAttribute('firstname', $Model->getFirstName());
	$xmlw->writeAttribute('lastname', $Model->getLastName());
	$xmlw->writeAttribute('birthdate', $Model->getBirthdate() > 0 ? date('Y-m-d', $Model->getBirthdate()) : NULL);
	
	$TagsThisModel = Tag2All::Filter($Tag2Alls, NULL, $Model->getID(), FALSE, FALSE, FALSE);
	$TagsThisModelOnly = Tag2All::Filter($TagsThisModel, NULL, $Model->getID(), NULL, NULL, NULL);
	$xmlw->writeAttribute('tags', Tag2All::Tags2AllCSV($TagsThisModelOnly));
	
	$SetsThisModel = Set::Filter($Sets, $Model->getID());
	if($SetsThisModel)
	{
		$xmlw->startElement('Sets');
	
		$DatesThisModel = Date::FilterDates($Dates, NULL, $Model->getID());

		if($Model->getFirstName() == 'VIP')
		{ usort($SetsThisModel, array('Set', 'CompareASC')); }

		foreach ($SetsThisModel as $Set)
		{
			$PicDatesThisSet = Date::FilterDates($DatesThisModel, NULL, NULL, $Set->getID(), DATE_KIND_IMAGE);
			$VidDatesThisSet = Date::FilterDates($DatesThisModel, NULL, NULL, $Set->getID(), DATE_KIND_VIDEO);
			$TagsThisSet = Tag2All::Filter($TagsThisModel, NULL, $Model->getID(), $Set->getID(), NULL, NULL);
	
			$xmlw->startElement('Set');
				
				if(($Model->getFirstName() == 'VIP') && !is_numeric($Set->getName()))
				{ $xmlw->writeAttribute('name', sprintf('SP_%1$s',$Set->getName())); }
				else
				{ $xmlw->writeAttribute('name', $Set->getName()); }
			
				$xmlw->writeAttribute('date_pic', Date::FormatDates($PicDatesThisSet, 'Y-m-d', FALSE, ' '));
				$xmlw->writeAttribute('date_vid', Date::FormatDates($VidDatesThisSet, 'Y-m-d', FALSE, ' '));
				$xmlw->writeAttribute('tags', Tag2All::Tags2AllCSV($TagsThisSet));
	
			if($IncludeImages)
			{
				$ImagesThisSet = Image::GetImages(new ImageSearchParameters(FALSE, FALSE, $Set->getID(), FALSE, $Model->getID()));
	
				if($ImagesThisSet)
				{
					$xmlw->startElement('Images');
						
					/* @var $Image Image */
					foreach($ImagesThisSet as $Image)
					{
						$TagsThisImage = Tag2All::Filter($TagsThisModel, NULL, $Model->getID(), $Set->getID(), $Image->getID(), NULL);
						if(($TaggedOnly === TRUE && $TagsThisImage) || $TaggedOnly === FALSE)
						{
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
					}
					$xmlw->endElement();
					$xmlw->flush();
					ob_flush();
					flush();
					unset($ImagesThisSet);
				}
			}
				
			if($IncludeVideos)
			{
				$VideosThisSet = Video::GetVideos(new VideoSearchParameters(FALSE, FALSE, $Set->getID(), FALSE, $Model->getID()));
	
				if($VideosThisSet)
				{
					$xmlw->startElement('Videos');
						
					/* @var $Video Video */
					foreach($VideosThisSet as $Video)
					{
						$TagsThisVideo = Tag2All::Filter($TagsThisModel, NULL, $Model->getID(), $Set->getID(), NULL, $Video->getID());
						if(($TaggedOnly === TRUE && $TagsThisVideo) || $TaggedOnly === FALSE)
						{
							$xmlw->startElement('Video');
								$xmlw->writeAttribute('name', $Video->getFileName());
								$xmlw->writeAttribute('extension', $Video->getFileExtension());
								$xmlw->writeAttribute('filesize', $Video->getFileSize());
								$xmlw->writeAttribute('checksum', $Video->getFileCheckSum());
								$xmlw->writeAttribute('tags', Tag2All::Tags2AllCSV($TagsThisVideo));
							$xmlw->endElement();
						}
					}
					$xmlw->endElement();
					$xmlw->flush();
					ob_flush();
					flush();
					unset($VideosThisSet);
				}
			}
			$xmlw->endElement();
		}
		$xmlw->endElement();
		$xmlw->flush();
		ob_flush();
		flush();
	
		if($Model->getRemarks())
		{
			$xmlw->startElement('Remarks');
			$xmlw->text($Model->getRemarks());
			$xmlw->endElement();
		}
	}
	$xmlw->endElement();
	$xmlw->flush();
	ob_flush();
	flush();
}


/* @var $SpecialModels array(Model) */
$SpecialModels = array();


/* @var $Model Model */
foreach ($Models as $Model)
{
	if($ModelID && $Model->getID() !== $ModelID)
	{ continue; }
	
	if( $Model->getFirstName() == 'Interviews' ||
		$Model->getFirstName() == 'Promotions' ||
		$Model->getFirstName() == 'VIP')
	{
		$SpecialModels[] = $Model;
		continue;
	}
	
	XmlOutputModel($Model, $TaggedOnly);
}

foreach ($SpecialModels as $Model)
{
	XmlOutputModel($Model, $TaggedOnly);
}

$xmlw->endElement();
$xmlw->endDocument();
ob_end_flush();
flush();

exit;

?>