<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();


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

$WhereClause = sprintf('model_id = %1$d AND set_id = %2$d AND mut_deleted = -1', $ModelID, $SetID);
$Images = Image::GetImages($WhereClause);

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
				<ul>
				<li>Filename: %4\$s.%5\$s</li>
				<li>Filesize: %13\$s</li>
				<li>Width: %14\$d</li>
				<li>Height: %15\$d</li>
				<li>Checksum: %16\$s</li>
				</ul>
				</div>
				<div class=\"ImageButtonWrapper\">
				<a href=\"image_view.php?model_id=%6\$d&amp;set_id=%7\$d&amp;image_id=%8\$d\"><img src=\"images/button_edit.png\" width=\"16\" height=\"16\" title=\"Edit model\" alt=\"Edit model\"/></a>
				<a href=\"download_zip.php?image_id=%8\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"Download image\" title=\"Download image\" /></a>
				<a href=\"image_view.php?model_id=%6\$d&amp;set_id=%7\$d&amp;image_id=%8\$d&amp;cmd=%12\$s\" title=\"Delete image\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"Delete\" /></a>
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
				htmlentities($Image->getFileCheckSum())
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
				"<td class=\"Center\"><a href=\"download_zip.php?image_id=%1\$d\"><img src=\"images/button_download.png\" width=\"16\" height=\"16\" alt=\"Download image\" title=\"Download image\" /></a></td>".
				"<td class=\"Center\"><a href=\"download_image.php?image_id=%1\$d&amp;width=%13\$d&amp;height=%14\$d\" title=\"%4\$s\" rel=\"lightbox-gal\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" alt=\"View image\" title=\"View image\" /></a></td>".
				"<td class=\"Center\"><a href=\"image_view.php?model_id=%3\$d&amp;set_id=%2\$d&amp;image_id=%1\$d&amp;cmd=%11\$s\" title=\"Delete image\"><img src=\"images/button_delete.png\" width=\"16\" height=\"16\" alt=\"Delete\" /></a></td>".
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
				$Image->getImageHeightToppedOff(800, 600)
				);
			break;
		}
	}
}

if(!$Set)
{
	$Set = Set::GetSets(sprintf('model_id = %1$d AND set_id = %2$d', $ModelID, $SetID));
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

echo HTMLstuff::HtmlHeader(sprintf('%1$s - Set %2$s - Images', $Model->GetShortName(true), $Set->getName()), $CurrentUser);

?>

<h2><?php echo sprintf(
	'<a href="index.php">Home</a> - <a href="model_view.php?model_id=%1$d">%3$s</a> - <a href="set.php?model_id=%1$d">Sets</a> - <a href="set_view.php?model_id=%1$d&amp;set_id=%2$d">Set %4$s</a> - Images',
	$ModelID,
	$SetID,
	htmlentities($Model->GetShortName(true)),
	htmlentities($Set->getName())
); ?></h2>

<?php
switch($CurrentUser->getImageview())
{
case 'thumb':
?>
	<div class="Clear"></div>
	<?php echo $ImageRows ?>
	<div class="Clear"></div>
	<div style="font-weight:bold;text-align:center">Total image count: <?php printf('%1$d', $ImageCount); ?></div>
<?
break;

case 'detail':
?>
<table border="0" cellpadding="4" cellspacing="0">
	<thead>
		<tr>
			<th>Filename</th>
			<th style="width: 60px;">Extension</th>
			<th style="width: 80px;">Filesize</th>
			<th style="width: 65px;">Width</th>
			<th style="width: 65px;">Height</th>
			<th style="width: 160px;">Checksum</th>
			<th style="width: 22px;">&nbsp;</th>
			<th style="width: 22px;"><a href="#" title="View slideshow" onclick="OpenSlideColorBox();"><img src="images/button_view.png" alt="View slideshow" width="16" height="16" /></a></th>
			<th style="width: 22px;">&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="9">Total image count: <?php printf('%1$d', $ImageCount); ?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php echo $ImageRows ? $ImageRows : '<tr class="Row1"><td colspan="9">&nbsp;</td></tr>'; ?>
	</tbody>
</table>
<?php
break;
}
?>

<div class="Separator"></div>

<?php
echo HTMLstuff::Button(sprintf('image_view.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), 'New image');

echo HTMLstuff::Button(sprintf('import_image.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), 'Import images');

echo HTMLstuff::Button(sprintf('set.php?model_id=%1$d', $ModelID), 'Sets');

echo HTMLstuff::Button('index.php');

echo HTMLstuff::HtmlFooter($CurrentUser);
?>