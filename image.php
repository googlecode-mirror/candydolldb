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
	header('location:set.php?model_id'.$ModelID);
	exit;
}


/* @var $Model Model */
/* @var $Set Set */
$Model = null;
$Set = null;
$ImageRows = '';
$ImageCount = 0;

$Images = Image::GetImages(new ImageSearchParameters(FALSE, FALSE, $SetID, FALSE, $ModelID));

if($Images)
{
	/* @var $Image Image */
	foreach($Images as $Image)
	{
		$ImageCount++;
		if(!$Set) { $Set = $Image->getSet(); }
		if(!$Model) { $Model = $Set->getModel(); }
		switch($CurrentUser->getImageview())
		{
			case 'thumb':
				$ImageRows .= sprintf(
				"<div class=\"SetThumbGalItem\">
					<h3 class=\"Hidden\">%4\$s.%5\$s</h3>
					<div class=\"SetThumbImageWrapper\">
						<a href=\"download_image.php?image_id=%8\$d&amp;width=%10\$d&amp;height=%11\$d\" title=\"%1\$s\" rel=\"lightbox-gal\">
							<img src=\"download_image.php?image_id=%8\$d&amp;width=225&amp;height=150\" height=\"150\" alt=\"%4\$s.%5\$s\" />
						</a>
					</div>
					<div class=\"ThumbDataWrapper\">
						<ul style=\"padding-left:0;text-align:center;\">
							<li>%16\$s: %4\$s.%5\$s</li>
							<li>%17\$s: %13\$s</li>
							<li>%18\$s: %14\$d</li>
							<li>%19\$s: %15\$d</li>
						</ul>
					</div>
					<div class=\"ImageButtonWrapper\">
						<a href=\"image_view.php?model_id=%6\$d&amp;set_id=%7\$d&amp;image_id=%8\$d\"><img src=\"images/button_edit.png\" width=\"16\" height=\"16\" title=\"%20\$s\" alt=\"%20\$s\"/></a>
						<a href=\"download_zip.php?image_id=%8\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"%21\$s\" title=\"%21\$s\" /></a>
						<a href=\"image_view.php?model_id=%6\$d&amp;set_id=%7\$d&amp;image_id=%8\$d&amp;cmd=%12\$s\" title=\"%22\$s\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"%22\$s\" /></a>
					</div>
				</div>
				%9\$s",
				htmlentities($Image->getSet()->getModel()->GetFullName()),
				htmlentities($Image->getSet()->getPrefix()),
				htmlentities($Image->getSet()->getName()),
				htmlentities($Image->getFileName()),
				htmlentities($Image->getFileExtension()),
				$Image->getSet()->getModel()->getID(),
				$Image->getSet()->getID(),
				$Image->getID(),
				($ImageCount % 3 == 0 ? "<div class=\"Clear\"></div>" : null),
				$Image->getImageWidthToppedOff(800, 600),
				$Image->getImageHeightToppedOff(800, 600),
				COMMAND_DELETE,
				Utils::ReadableFilesize($Image->getFileSize()),
				$Image->getImageWidth(),
				$Image->getImageHeight(),
				$lang->g('LabelFilename'),
				$lang->g('LabelFilesize'),
				$lang->g('LabelWidth'),
				$lang->g('LabelHeight'),
				$lang->g('LabelEditModel'),
				$lang->g('LabelDownloadImage'),
				$lang->g('LabelDeleteImage')
			);
			break;

			case 'detail':
				$ImageRows .= sprintf(
				"\n<tr class=\"Row%12\$d\">".
					"<td><a href=\"image_view.php?model_id=%3\$d&amp;set_id=%2\$d&amp;image_id=%1\$d\">%4\$s</a></td>".
					"<td class=\"Center\">%5\$s</td>".
					"<td class=\"Center\">%6\$s</td>".
					"<td class=\"Center\">%9\$d</td>".
					"<td class=\"Center\">%10\$d</td>".
					"<td>%7\$s</td>".
					"<td class=\"Center\"><a href=\"download_zip.php?image_id=%1\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"%15\$s\" title=\"%15\$s\" /></a></td>".
					"<td class=\"Center\"><a href=\"download_image.php?image_id=%1\$d&amp;width=%13\$d&amp;height=%14\$d\" title=\"%4\$s\" rel=\"lightbox-gal\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" alt=\"%16\$s\" title=\"%16\$s\" /></a></td>".
					"<td class=\"Center\"><a href=\"image_view.php?model_id=%3\$d&amp;set_id=%2\$d&amp;image_id=%1\$d&amp;cmd=%11\$s\" title=\"%17\$s\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"%17\$s\" /></a></td>".
				"</tr>",
				$Image->getID(),
				$Set->getID(),
				$Model->getID(),
				htmlentities($Image->getFileName()),
				htmlentities($Image->getFileExtension()),
				Utils::ReadableFilesize($Image->getFileSize()),
				htmlentities($Image->getFileCheckSum()),
				null,
				$Image->getImageWidth(),
				$Image->getImageHeight(),
				COMMAND_DELETE,
				$ImageCount % 2 == 0 ? 2 : 1,
				$Image->getImageWidthToppedOff(800, 600),
				$Image->getImageHeightToppedOff(800, 600),
				$lang->g('LabelDownloadImage'),
				$lang->g('LabelViewImage'),
				$lang->g('LabelDeleteImage')
			);
			break;
		}
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

echo HTMLstuff::HtmlHeader(sprintf('%1$s - %2$s %3$s - %4$s',
		$Model->GetShortName(true),
		$lang->g('NavigationSet'),
		$Set->getName(),
		$lang->g('NavigationImages')
	),
	$CurrentUser
);

?>

<h2><?php echo sprintf(
	'<a href="index.php">%5$s</a> - <a href="model_view.php?model_id=%1$d">%3$s</a> - <a href="set.php?model_id=%1$d">%6$s</a> - <a href="set_view.php?model_id=%1$d&amp;set_id=%2$d">%7$s %4$s</a> - %8$s',
	$ModelID,
	$SetID,
	htmlentities($Model->GetShortName(true)),
	htmlentities($Set->getName()),
	$lang->g('NavigationHome'),
	$lang->g('NavigationSets'),
	$lang->g('NavigationSet'),
	$lang->g('NavigationImages')
)?></h2>

<?php
switch($CurrentUser->getImageview())
{
case 'thumb':
?>
	<div class="Clear"></div>
	<?php echo $ImageRows ?>
	<div class="Clear"></div>
	<div style="font-weight:bold;text-align:center"><?php echo $lang->g('LabelTotalImageCount')?>: <?php printf('%1$d', $ImageCount)?></div>
<?
break;

case 'detail':
?>
<table>
	<thead>
		<tr>
			<th><?php echo $lang->g('LabelFilename')?></th>
			<th class="Center" style="width: 60px;"><?php echo $lang->g('LabelExtension')?></th>
			<th class="Center" style="width: 80px;"><?php echo $lang->g('LabelFilesize')?></th>
			<th class="Center" style="width: 65px;"><?php echo $lang->g('LabelWidth')?></th>
			<th class="Center" style="width: 65px;"><?php echo $lang->g('LabelHeight')?></th>
			<th class="Center" style="width: 160px;"><?php echo $lang->g('LabelChecksum')?></th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;"><a href="#" title="<?php echo $lang->g('LabelViewSlideshow')?>" onclick="OpenSlideColorBox();"><img src="images/button_view.png" alt="View slideshow" width="16" height="16" /></a></th>
			<th style="width: 22px;">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="9"><?php echo $lang->g('LabelTotalImageCount')?>: <?php printf('%1$d', $ImageCount)?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php echo $ImageRows ? $ImageRows : '<tr class="Row1"><td colspan="9">&nbsp;</td></tr>'?>
	</tbody>
</table>
<?php
break;
}
?>

<div class="Separator"></div>

<?php
echo HTMLstuff::Button(sprintf('image_view.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), $lang->g('ButtonNewImage'));
echo HTMLstuff::Button(sprintf('import_image.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), $lang->g('ButtonImportImages'));
echo HTMLstuff::Button(sprintf('set.php?model_id=%1$d', $ModelID), $lang->g('NavigationSets'));
echo HTMLstuff::Button('index.php');

echo HTMLstuff::HtmlFooter($CurrentUser);
?>