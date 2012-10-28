<?php

include('cd.php');
ini_set('max_execution_time', '3600');
ob_start();
$CurrentUser = Authentication::Authenticate();

$Date = NULL;
$UpdatesPic = NULL;
$UpdatesVid = NULL;
$file = file_get_contents('http://www.candydoll.tv/update.html');
$lines = explode("\n", $file);
$key = array_search('<div class="text-upd">', $lines);
$key +=1;
if (preg_match('%images/([0-9]+)%i', $file, $matches))
{ $Date = strtotime(preg_replace('/([0-9]{2})([0-9]{2})([0-9]{2})/i', '20\1-\2-\3', $matches[1])); }
else
{ $Date = NULL; }

$picarray = array();
$vidarray = array();
for($i = $key; $i < ($key + 9); $i++)
{
	if($i == ($key + 8))
	{
		$vip = str_replace(".","",substr(strip_tags($lines[$i]), 4));
		$result = preg_replace('/(VIP).+?([0-9]{2,3}).+?([0-9]{2,3})/i', '\1\2_\1\3', $vip);
		if($result)
		{
		$vip = explode("_",$result);
		$UpdatesPic .= $vip[0];
		$UpdatesVid .= $vip[1];
		}
		else
		{
		$UpdatesPic += "WTF";
		}
	}
	else
	{
		($i < ($key + 4)) ? $UpdatesPic .= str_replace(".","",substr(strip_tags($lines[$i]), 4))."\n" : $UpdatesVid .= str_replace(".","",substr(strip_tags($lines[$i]), 4))."\n" ;
	}
}
$UpdatesPic = preg_replace('/ Photo/i', '', $UpdatesPic);
$UpdatesVid = preg_replace('/ Movie/i', '', $UpdatesVid);



$Models = Model::GetModels(new ModelSearchParameters());
$Sets = Set::GetSets(new SetSearchParameters());
$Dates = Date::GetDates();
$Tag2Alls = Tag2All::GetTag2Alls();

//if(array_key_exists('date', $_GET))
//{
//	$Date = strtotime($_GET['date']);
//	{ $Date = $Date === FALSE ? NULL : $Date; }
//}

if(!array_key_exists('hidAction', $_POST))
{
	if(!is_null($Date) && $Date != -1)
	{
		$DatesToShow = Date::FilterDates($Dates, NULL, NULL, NULL, NULL, $Date);

		if($DatesToShow)
		{
			/* @var $d Date */
			/* @var $m Model */
			/* @var $s Set */
			foreach ($DatesToShow as $d)
			{
				$m = Model::Filter($Models, $d->getSet()->getModelID());
				$m = $m ? $m[0] : NULL;
				if(is_null($m)) { continue; }

				$s = Set::Filter($Sets, $m->getID(), $d->getSetID());
				$s = $s ? $s[0] : NULL;
				if(is_null($s)) { continue; }

/*				switch($d->getDateKind())
				{
					case DATE_KIND_IMAGE:
						$UpdatesPic .= sprintf('%1$s%2$s%3$s', $m->GetShortName(), $s->getName(), PHP_EOL);
						break;

					case DATE_KIND_VIDEO:
						$UpdatesVid .= sprintf('%1$s%2$s%3$s', $m->GetShortName(), $s->getName(), PHP_EOL);
						break;
				}
*/			}
		}
	}
}
else if($_POST['hidAction'] == 'ComposeXML')
{
		$outfile = 'CandyDollDB.xml';

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

	$Date = strtotime($_POST['txtDate']);
	{ $Date = $Date === FALSE ? NULL : $Date; }

	$UpdatesPic = !empty($_POST['txtUpdatesPic']) ? $_POST['txtUpdatesPic'] : NULL;
	$UpdatesVid = !empty($_POST['txtUpdatesVid']) ? $_POST['txtUpdatesVid'] : NULL;

	$ModelsToShow = array();
	$SetsToShow = array();
	$matchesPic = array();
	$matchesVid = array();

	function XmlOutputModel($Model)
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
			{
				usort($SetsThisModel, array('Set', 'CompareASC'));
			}

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

	if(!is_null($Date) && (!is_null($UpdatesPic) || !is_null($UpdatesVid)))
	{
		$mPic = preg_match_all('/(?<ModelName>[A-Z]+)(?<SetName>[0-9]+)/i', $UpdatesPic, $matchesPic, PREG_SPLIT_NO_EMPTY);
		$mVid = preg_match_all('/(?<ModelName>[A-Z]+)(?<SetName>[0-9]+)/i', $UpdatesVid, $matchesVid, PREG_SPLIT_NO_EMPTY);

		foreach($matchesPic["ModelName"] as $mPict)
		{
			$mInDB = Model::Filter($Models, NULL, NULL, NULL, $mPict);

			$name = preg_match('/^(?<FirstName>[A-Z]+[a-z]*)(?<LastNameInitial>[A-Z])??$/', $mPict, $splitName);
			$fname = $name ? $splitName['FirstName'] : NULL;
			$lname = $name && array_key_exists('LastNameInitial', $splitName) ? $splitName['LastNameInitial'] : NULL;

			$mInDB = $mInDB ? $mInDB[0] : new Model(NULL, $fname, $lname);


			/* @var $ModelInDB Model */
			foreach ($Models as $ModelInDB)
			{
				$mInDB = $ModelInDB->GetShortName() == $mPict ? $ModelInDB : new Model(NULL, $mPict);
				XmlOutputModel($mInDB);
			}
/*			foreach($Models as $ModelInDB)
			{

				var_dump($mPic);
				var_dump($mInDB);
				break;
			}*/
		}

//		var_dump($matchesPic);
//		var_dump($matchesVid);
//		die();


	}




	/* @var $Model Model
	foreach ($Models as $Model)
	{
		XmlOutputModel($Model);
	}*/

		$xmlw->endElement();
		$xmlw->endDocument();
		ob_end_flush();
		flush();

		exit;

}

echo HTMLstuff::HtmlHeader('Compose XML', $CurrentUser);
?>

<h2><?php echo sprintf('<a href="index.php">%1$s</a> - Compose XML',
	$lang->g('NavigationHome')
)?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="ComposeXML" />

<div class="FormRow">
<label for="txtDate">Date:</label>
<input type="text" id="txtDate" name="txtDate" class="DatePicker"maxlength="10" value="<?php echo $Date ? date('Y-m-d', $Date) : NULL?>" />
</div>

<div class="FormRow">
<label for="txtUpdatesPic">Updates (pic):</label>
<textarea id="txtUpdatesPic" name="txtUpdatesPic" cols="42" rows="8"><?php echo $UpdatesPic?></textarea>
</div>

<div class="FormRow">
<label for="txtUpdatesVid">Updates (vid):</label>
<textarea id="txtUpdatesVid" name="txtUpdatesVid" cols="42" rows="8"><?php echo $UpdatesVid?></textarea>
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" class="FormButton" value="Download" />
<input type="button" class="FormButton" value="Cancel" onclick="window.location='index.php';" />
</div>

<div class="Separator"></div>

<?php echo HTMLstuff::Button()?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>