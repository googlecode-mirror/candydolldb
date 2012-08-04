<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$ModelsOptions = '';
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

$CacheInSync = sprintf('<span>%1$s</span>', $lang->g('LabelOrphanFiles0'));
if($PhysicalCacheImageCount > count($CacheImages))
{
	$CacheInSync = '<span class="WarningRed">'.sprintf(
		$PhysicalCacheImageCount - count($CacheImages) == 1 ? $lang->g('LabelOrphanFiles1') : $lang->g('LabelOrphanFilesX'),
		$PhysicalCacheImageCount - count($CacheImages)
	).'</span>';
}
elseif($PhysicalCacheImageCount < count($CacheImages))
{
	$CacheInSync = '<span class="WarningRed">'.sprintf(
		count($CacheImages) - $PhysicalCacheImageCount == 1 ? $lang->g('LabelMissingFiles1') : $lang->g('LabelMissingFilesX'),
		count($CacheImages) - $PhysicalCacheImageCount
	).'</span>';
}

echo HTMLstuff::HtmlHeader($lang->g('NavigationAdminPanel'), $CurrentUser);

?>

<script type="text/javascript">
//<![CDATA[
	
	function RedirToXML(){
		var modelIdXml = parseInt( $('#selModelXml').val() );
		var includePic = $('#chkXMLIncludePic').is(':checked');
		var includeVid = $('#chkXMLIncludeVid').is(':checked');
		var taggedonly = $('#chkXMLIncludeTag').is(':checked');
		var url = 'download_xml.php';

		url += '?model_id=' + (isNaN(modelIdXml) || modelIdXml <= 0 ? '' : modelIdXml); 
		url += '&includeimages=' + (includePic ? 'true' : 'false');
		url += '&includevideos=' + (includeVid ? 'true' : 'false');		
		url += '&taggedonly=' + (taggedonly ? 'true' : 'false');

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

<h2><?php echo sprintf('<a href="index.php">%1$s</a> - %2$s', $lang->g('NavigationHome'), $lang->g('NavigationAdminPanel'))?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post">
<fieldset>

<div class="FormRow WideForm">
<label><?php echo $lang->g('LabelCleanCacheFolder')?></label>
	<input type="button" id="btnCleanCache" name="btnCleanCache" value="<?php echo $lang->g('ButtonClean')?>"
	<?php echo $CurrentUser->hasPermission(RIGHT_CACHE_CLEANUP) ? "onclick=\"window.location='cacheimage_nuke.php';\"" : HtmlStuff::DisabledStr(TRUE) ?>
	/>
<?php echo $CacheInSync?>
</div>

<hr />

<div class="FormRow WideForm">
<label><?php echo $lang->g('LabelDownloadXML')?></label>
<input type="button" id="btnDownloadExport" name="btnDownloadExport" value="<?php echo $lang->g('ButtonDownload')?>"
	<?php echo $CurrentUser->hasPermission(RIGHT_EXPORT_XML) ? "onclick=\"RedirToXML();\"" : HTMLstuff::DisabledStr(TRUE) ?>
/>
<br />
<label for="selModelXml" style="float:none;width:auto;"><?php echo $lang->g('LabelModel')?>: </label>
<select id="selModelXml" name="selModelXml"<?php echo $CurrentUser->hasPermission(RIGHT_EXPORT_XML) ? NULL : HTMLstuff::DisabledStr(TRUE)?>>
	<option value=""><?php echo $lang->g('LabelAllModels')?></option>
	<?php echo $ModelsOptions?>
</select>
<label for="chkXMLIncludePic" style="float:none;width:auto;"><input type="checkbox" id="chkXMLIncludePic" name="chkXMLIncludePic"<?php echo $CurrentUser->hasPermission(RIGHT_EXPORT_XML) ? NULL : HTMLstuff::DisabledStr(TRUE)?> />&nbsp;<?php echo $lang->g('LabelIncludeImages')?></label>
<label for="chkXMLIncludeVid" style="float:none;width:auto;"><input type="checkbox" id="chkXMLIncludeVid" name="chkXMLIncludeVid"<?php echo $CurrentUser->hasPermission(RIGHT_EXPORT_XML) ? NULL : HTMLstuff::DisabledStr(TRUE)?> />&nbsp;<?php echo $lang->g('LabelIncludeVideos')?></label>
<label for="chkXMLIncludeTag" style="float:none;width:auto;"><input type="checkbox" id="chkXMLIncludeTag" name="chkXMLIncludeTag"<?php echo $CurrentUser->hasPermission(RIGHT_EXPORT_XML) ? NULL : HTMLstuff::DisabledStr(TRUE)?> />&nbsp;<?php echo $lang->g('LabelIncludeTags')?></label>
</div>

<hr />

<div class="FormRow">
<label for="selModel"><?php echo $lang->g('LabelModel')?>: </label>
<select id="selModel" name="selModel"<?php echo $CurrentUser->hasPermission(RIGHT_EXPORT_INDEX) ? NULL : HTMLstuff::DisabledStr(TRUE)?>>
	<option value=""></option>
	<?php echo $ModelsOptions?>
</select>
</div>

<div class="FormRow">
<label for="txtIndexWidth"><?php echo $lang->g('LabelWidth')?>: </label>
<input type="text" id="txtIndexWidth" name="txtIndexWidth" value="1200"<?php echo $CurrentUser->hasPermission(RIGHT_EXPORT_INDEX) ? NULL : HTMLstuff::DisabledStr(TRUE)?> />
</div>

<div class="FormRow">
<label for="txtIndexHeight"><?php echo $lang->g('LabelHeight')?>: </label>
<input type="text" id="txtIndexHeight" name="txtIndexHeight" value="1800"<?php echo $CurrentUser->hasPermission(RIGHT_EXPORT_INDEX) ? NULL : HTMLstuff::DisabledStr(TRUE)?> />
</div>

<div class="FormRow WideForm">
<label><?php echo $lang->g('LabelDownloadIndex')?></label>
<input type="button" id="btnDownloadIndex" name="btnDownloadIndex" value="<?php echo $lang->g('ButtonDownload')?>"
	<?php echo $CurrentUser->hasPermission(RIGHT_EXPORT_INDEX) ? "onclick=\"RedirToIndex();\"" : HTMLstuff::DisabledStr(TRUE)?> />
</div>

<hr />

<?php echo HTMLstuff::Button('index.php')?>
</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>