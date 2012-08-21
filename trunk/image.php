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
$Model = NULL;
$Set = NULL;
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
						%23\$s
						%24\$s
						%25\$s
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
				($ImageCount % 3 == 0 ? "<div class=\"Clear\"></div>" : NULL),
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
				$lang->g('LabelDeleteImage'),
				
				$CurrentUser->hasPermission(RIGHT_IMAGE_EDIT) ?
					sprintf("<a href=\"image_view.php?model_id=%1\$d&amp;set_id=%2\$d&amp;image_id=%3\$d\"><img src=\"images/button_edit.png\" width=\"16\" height=\"16\" alt=\"%4\$s\" title=\"%4\$s\" /></a>", $ModelID, $SetID, $Image->getID(), $Image->getFileName()) :
					sprintf("<a href=\"#\"><img src=\"images/button_edit_invalid.png\" width=\"16\" height=\"16\" alt=\"%1\$s\" title=\"%1\$s\" /></a>", $lang->g('LabelNotAllowed')),
				
				$CurrentUser->hasPermission(RIGHT_EXPORT_ZIP) ?
					sprintf("<a href=\"download_zip.php?image_id=%1\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"%2\$s\" title=\"%2\$s\" /></a>", $Image->getID(), $lang->g('LabelDownloadImage')) :
					sprintf("<a href=\"#\"><img src=\"images/button_download_invalid.png\" width=\"16\" height=\"16\" alt=\"%1\$s\" title=\"%1\$s\" /></a>", $lang->g('LabelNotAllowed')),
					
				$CurrentUser->hasPermission(RIGHT_IMAGE_DELETE) ?
					sprintf("<a href=\"image_view.php?model_id=%1\$d&amp;set_id=%2\$d&amp;image_id=%3\$d&amp;cmd=%4\$s\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"%5\$s\" title=\"%5\$s\" /></a>", $ModelID, $SetID, $Image->getID(), COMMAND_DELETE, $lang->g('LabelDeleteImage')) :
					sprintf("<a href=\"#\"><img src=\"images/button_delete_invalid.png\" width=\"16\" height=\"16\" alt=\"%1\$s\" title=\"%1\$s\" /></a>", $lang->g('LabelNotAllowed'))
			);
			break;

			case 'detail':
				$ImageRows .= sprintf(
				"\n<tr class=\"Row%12\$d\">".
					"<td>%21\$s</td>".
					"<td class=\"Center\">%5\$s</td>".
					"<td class=\"Center\">%6\$s</td>".
					"<td class=\"Center\">%9\$d</td>".
					"<td class=\"Center\">%10\$d</td>".
					"<td><code>%7\$s</code></td>".
					"<td><code>%18\$s</code></td>".
					"<td class=\"Center\">%19\$s</td>".
					"<td class=\"Center\"><a href=\"download_image.php?image_id=%1\$d&amp;width=%13\$d&amp;height=%14\$d\" title=\"%4\$s\" rel=\"lightbox-gal\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" alt=\"%16\$s\" title=\"%16\$s\" /></a></td>".
					"<td class=\"Center\">%20\$s</td>".
				"</tr>",
				$Image->getID(),
				$Set->getID(),
				$Model->getID(),
				htmlentities($Image->getFileName()),
				htmlentities($Image->getFileExtension()),
				Utils::ReadableFilesize($Image->getFileSize()),
				htmlentities($Image->getFileCheckSum()),
				NULL,
				$Image->getImageWidth(),
				$Image->getImageHeight(),
				COMMAND_DELETE,
				$ImageCount % 2 == 0 ? 2 : 1,
				$Image->getImageWidthToppedOff(800, 600),
				$Image->getImageHeightToppedOff(800, 600),
				$lang->g('LabelDownloadImage'),
				$lang->g('LabelViewImage'),
				$lang->g('LabelDeleteImage'),
				$Image->getFileCRC32(),
				
				$CurrentUser->hasPermission(RIGHT_EXPORT_ZIP) ?
					sprintf("<a href=\"download_zip.php?image_id=%1\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"%2\$s\" title=\"%2\$s\" /></a>", $Image->getID(), $Image->getFileName()) :
					sprintf("<a href=\"#\"><img src=\"images/button_download_invalid.png\" width=\"16\" height=\"16\" alt=\"%1\$s\" title=\"%1\$s\" /></a>", $lang->g('LabelNotAllowed')),
				
				$CurrentUser->hasPermission(RIGHT_IMAGE_DELETE) ?
					sprintf("<a href=\"image_view.php?model_id=%1\$d&amp;set_id=%2\$d&amp;image_id=%3\$d&amp;cmd=%4\$s\" title=\"%5\$s\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"%5\$s\" /></a>", $ModelID, $SetID, $Image->getID(), COMMAND_DELETE, $lang->g('ButtonDelete')) :
					sprintf("<a href=\"#\"><img src=\"images/button_delete_invalid.png\" width=\"16\" height=\"16\" alt=\"%1\$s\" title=\"%1\$s\" /></a>", $lang->g('LabelNotAllowed')),

				$CurrentUser->hasPermission(RIGHT_IMAGE_EDIT) ?
					sprintf("<a href=\"image_view.php?model_id=%1\$d&amp;set_id=%2\$d&amp;image_id=%3\$d\">%4\$s</a>", $ModelID, $SetID, $Image->getID(), $Image->getFileName()) :
					sprintf("<a href=\"#\">%1\$s</a>", $Image->getFileName())
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
		$Model->GetShortName(TRUE),
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
	htmlentities($Model->GetShortName(TRUE)),
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
			<th style="width: 270px;"><?php echo $lang->g('LabelMD5Checksum')?></th>
			<th style="width: 80px;">CRC32</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;"><a href="#" title="<?php echo $lang->g('LabelViewSlideshow')?>" onclick="OpenSlideColorBox();"><img src="images/button_view.png" alt="View slideshow" width="16" height="16" /></a></th>
			<th style="width: 22px;">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="10"><?php echo $lang->g('LabelTotalImageCount')?>: <?php printf('%1$d', $ImageCount)?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php echo $ImageRows ? $ImageRows : '<tr class="Row1"><td colspan="10">&nbsp;</td></tr>'?>
	</tbody>
</table>
<?php
break;
}
?>

<div class="Separator"></div>

<?php
echo $CurrentUser->hasPermission(RIGHT_IMAGE_ADD) ? HTMLstuff::Button(sprintf('image_view.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), $lang->g('ButtonNewImage')) : '';
echo $CurrentUser->hasPermission(RIGHT_IMAGE_ADD) ? HTMLstuff::Button(sprintf('import_image.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), $lang->g('ButtonImportImages')) : '';
echo HTMLstuff::Button(sprintf('set.php?model_id=%1$d', $ModelID), $lang->g('NavigationSets'));
echo HTMLstuff::Button('index.php');

echo HTMLstuff::HtmlFooter($CurrentUser);
?>