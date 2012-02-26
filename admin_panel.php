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

$ModelsOptions = '<option value=\"\"></option>';
$Models = Model::GetModels();

/* @var $Model Model */
foreach ($Models as $Model){
	$ModelsOptions .= sprintf("<option value=\"%1\$d\">%2\$s</option>",
		$Model->getID(),
		htmlentities($Model->GetFullName())
	);
}

$CacheFolder = 'cache';
$CacheImages = CacheImage::GetCacheImages();

/* @var $it RecursiveDirectoryIterator */
$it = new RecursiveDirectoryIterator(
	$CacheFolder,
	FileSystemIterator::SKIP_DOTS | FileSystemIterator::CURRENT_AS_FILEINFO
);

$PhysicalCacheImageCount = 0;

/* @var $file SplFileInfo */
foreach($it as $file){
	if($file->isFile()){
		$PhysicalCacheImageCount++;
	}
}

$CacheInSync = '<span>No orphan files</span>';
if($PhysicalCacheImageCount > count($CacheImages))
{
	$CacheInSync = sprintf('<span class="WarningRed">%1$d orphan file%2$s</span>',
		$PhysicalCacheImageCount - count($CacheImages),
		$PhysicalCacheImageCount - count($CacheImages) == 1 ? null : 's'
	);
}
elseif($PhysicalCacheImageCount < count($CacheImages))
{
	$CacheInSync = sprintf('<span class="WarningRed">%1$d missing file%2$s</span>',
		count($CacheImages) - $PhysicalCacheImageCount,
		count($CacheImages) - $PhysicalCacheImageCount == 1 ? null : 's'
	);
}

echo HTMLstuff::HtmlHeader('Admin-panel', $CurrentUser);

?>

<h2><?php echo sprintf('<a href="index.php">Home</a> - Admin-panel'); ?></h2>

<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
<fieldset>

<div class="FormRow WideForm">
<label>To remove all files in the application's cache-folder that do not have a corresponding entry in the CacheImage-table.</label>
<input type="button" id="btnCleanCache" name="btnCleanCache" value="Clean" onclick="window.location='cacheimage_nuke.php';" />
<?php echo $CacheInSync; ?>
</div>

<hr />

<div class="FormRow WideForm">
<label>To download an XML-file, similar to the weekly updated setup_data.xml, based on your own CandyDollDB-collection.</label>
<input type="button" id="btnDownloadExport" name="btnDownloadExport" value="Download" onclick="window.location='download_xml.php';" />
</div>

<hr />

<script type="text/javascript">
//<![CDATA[
	function RedirToIndex(){
		var modelId = parseInt( $('#selModel').val() );
		var indexWidth = parseInt( $('#txtIndexWidth').val() );
		var indexHeight = parseInt( $('#txtIndexHeight').val() );

		if(!isNaN(modelId) && !isNaN(indexWidth) && !isNaN(indexHeight))
		{
			indexWidth = (indexWidth <= 0 || indexWidth > 1200 ) ? 1200 : indexWidth;
			indexHeight = (indexHeight <= 0 || indexHeight > 1800) ? 1800 : indexHeight;

			var url = 'download_image.php?' +
			'index_id=' + modelId +
			'&width=' + indexWidth +
			'&height=' + indexHeight +
			'&download=true';

			window.location = url;
			return true;
		}

		return false;
	}
//]]>
</script>

<div class="FormRow">
<label for="selModel">Model: </label>
<select id="selModel" name="selModel"><?php echo $ModelsOptions; ?></select>
</div>

<div class="FormRow">
<label for="txtIndexWidth">Width: </label>
<input type="text" id="txtIndexWidth" name="txtIndexWidth" value="1200" />
</div>

<div class="FormRow">
<label for="txtIndexHeight">Height: </label>
<input type="text" id="txtIndexHeight" name="txtIndexHeight" value="1800" />
</div>

<div class="FormRow WideForm">
<label>To download an automatically generated custom size index of a given model. Size is maxed to 1200x1800 pixels.</label>
<input type="button" id="btnDownloadIndex" name="btnDownloadIndex" value="Download" onclick="RedirToIndex();" />
</div>

<hr />

<?php echo HTMLstuff::Button('index.php'); ?>
</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>