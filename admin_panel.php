<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$ModelsOptions = '<option value=""></option>';
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

<script type="text/javascript">
//<![CDATA[
	
	function RedirToXML(){
		var includePic = $('#chkXMLIncludePic').is(':checked');
		var includeVid = $('#chkXMLIncludeVid').is(':checked');
		var url = 'download_xml.php';

		url += '?includeimages=' + (includePic ? 'true' : 'false');
		url += '&includevideos=' + (includeVid ? 'true' : 'false');		

		window.location = url;
		return true;
	}
           
	function RedirToIndex(){
		var modelId = parseInt( $('#selModel').val() );
		var indexWidth = parseInt( $('#txtIndexWidth').val() );
		var indexHeight = parseInt( $('#txtIndexHeight').val() );

		if(!isNaN(modelId) && !isNaN(indexWidth) && !isNaN(indexHeight))
		{
			indexWidth = (indexWidth <= 0 || indexWidth > 1200 ) ? 1200 : indexWidth;
			indexHeight = (indexHeight <= 0 || indexHeight > 1800) ? 1800 : indexHeight;

			var url = 'download_image.php' +
			'?index_id=' + modelId +
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

<h2><?php echo sprintf('<a href="index.php">Home</a> - Admin-panel'); ?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
<fieldset>

<div class="FormRow WideForm">
<label>To remove all files in the application's cache-folder that do not have a corresponding entry in the CacheImage-table.</label>
<input type="button" id="btnCleanCache" name="btnCleanCache" value="Clean" onclick="window.location='cacheimage_nuke.php';" />
<?php echo $CacheInSync; ?>
</div>

<hr />

<div class="FormRow WideForm">
<label>To download an XML-file, based on your own CandyDollDB-collection.</label>
<input type="button" id="btnDownloadExport" name="btnDownloadExport" value="Download" onclick="RedirToXML();" />
<br />
<label for="chkXMLIncludePic" style="float:none;width:auto;"><input type="checkbox" id="chkXMLIncludePic" name="chkXMLIncludePic" />&nbsp;Include images</label>
<label for="chkXMLIncludeVid" style="float:none;width:auto;"><input type="checkbox" id="chkXMLIncludeVid" name="chkXMLIncludeVid" />&nbsp;Include videos</label>
</div>

<hr />

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