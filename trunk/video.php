<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$ModelID = Utils::SafeIntFromQS('model_id');
$SetID = Utils::SafeIntFromQS('set_id');

if(!isset($ModelID))
{
	header('location:index.php');
	exit;
}

if(!isset($SetID))
{
	header('location:set.php?model_id='.$ModelID);
	exit;
}

/* @var $Model Model */
/* @var $Set Set */
$Model = NULL;
$Set = NULL;
$VideoRows = '';
$VideoCount = 0;

$Videos = Video::GetVideos(new VideoSearchParameters(FALSE, FALSE, $SetID, FALSE, $ModelID));

if($Videos)
{
	/* @var $Video Video */
	foreach($Videos as $Video)
	{
		$VideoCount++;
		if(!$Set) { $Set = $Video->getSet(); }
		if(!$Model) { $Model = $Set->getModel(); }

		$VideoRows .= sprintf(
		"\n<tr class=\"Row%10\$d\">".
    		"<td><a href=\"video_view.php?model_id=%3\$d&amp;set_id=%2\$d&amp;video_id=%1\$d\">%4\$s</a></td>".
        	"<td class=\"Center\">%5\$s</td>".
	    	"<td class=\"Center\">%6\$s</td>".
        	"<td>%7\$s</td>".
			"<td class=\"Center\"><a href=\"download_vid.php?video_id=%1\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"%11\$s\" title=\"%11\$s\" /></a></td>".
			"<td class=\"Center\"><a href=\"download_image.php?video_id=%1\$d&amp;width=800&amp;height=600\" rel=\"lightbox-thumb\" title=\"Thumbnails of %4\$s\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" alt=\"Thumbnails of %4\$s\" /></a></td>".
			"<td class=\"Center\"><a href=\"video_view.php?model_id=%3\$d&amp;set_id=%2\$d&amp;video_id=%1\$d&amp;cmd=%9\$s\" title=\"%13\$s\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"%12\$s\" /></a></td>".
        "</tr>",
		$Video->getID(),
		$Set->getID(),
		$Model->getID(),
		htmlentities($Video->getFileName()),
		htmlentities($Video->getFileExtension()),
		Utils::ReadableFilesize($Video->getFileSize()),
		htmlentities($Video->getFileCheckSum()),
		NULL,
		COMMAND_DELETE,
		$VideoCount % 2 == 0 ? 2 : 1,
		$lang->g('LabelDownloadVideo'),
		$lang->g('ButtonDelete'),
		$lang->g('LabelDeleteVideo')
		);
	}
}

if(!$Set)
{
	$Set = Set::GetSets(new SetSearchParameters($SetID, FALSE, $ModelID));
	if($Set)
	{
		$Set = $Set[0];
		$Model = $Set->getModel();
	}
	else
	{
		header('location:index.php');
		exit;
	}
}

echo HTMLstuff::HtmlHeader(sprintf('%1$s - %3$s %2$s - %4$s',
	$Model->GetShortName(TRUE),
	$Set->getName(),
	$lang->g('NavigationSet'),
	$lang->g('NavigationVideos')
), $CurrentUser);
?>

<h2><?php echo sprintf(
	'<a href="index.php">%5$s</a> - <a href="model_view.php?model_id=%1$d">%3$s</a> - <a href="set.php?model_id=%1$d">%6$s</a> - <a href="set_view.php?model_id=%1$d&amp;set_id=%2$d">%7$s %4$s</a> - %8$s',
	$ModelID,
	$SetID,
	htmlentities($Model->GetShortName(TRUE)),
	htmlentities($Set->getName()),
	$lang->g('NavigationHome'),
	$lang->g('NavigationSets'),
	$lang->g('NavigationSet'),
	$lang->g('NavigationVideos')
)?></h2>

<div class="Separator"></div>

<table>
	<thead>
		<tr>
			<th><?php echo $lang->g('LabelFilename')?></th>
			<th style="width: 60px;"><?php echo $lang->g('LabelExtension')?></th>
			<th style="width: 80px;"><?php echo $lang->g('LabelFilesize')?></th>
			<th style="width: 290px;"><?php echo $lang->g('LabelChecksum')?></th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="7"><?php printf('%1$s: %2$d', $lang->g('LabelTotalVideoCount'), $VideoCount)?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php echo $VideoRows ? $VideoRows : '<tr class="Row1"><td colspan="7">&nbsp;</td></tr>'?>
	</tbody>
</table>

<?php

echo HTMLstuff::Button(sprintf('video_view.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), $lang->g('LabelNewVideo'));

echo HTMLstuff::Button(sprintf('import_video.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), $lang->g('ButtonImportVideos'));

echo HTMLstuff::Button(sprintf('set.php?model_id=%1$d', $ModelID), $lang->g('NavigationSets'));

echo HTMLstuff::Button();

echo HTMLstuff::HtmlFooter($CurrentUser);
?>