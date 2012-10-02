<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$DBHostName = DBHOSTNAME;
$DBUserName = DBUSERNAME;
$DBPassword = DBPASSWORD;
$DBName = DBNAME;

$CandyPath = CANDYPATH;
$CandyVideoThumbPath = CANDYVIDEOTHUMBPATH;

if(array_key_exists('hidAction', $_POST) && isset($_POST['hidAction']) && $_POST['hidAction'] == 'EditConfiguration')
{
	$DBHostName = isset($_POST['txtDBHostName']) && strlen($_POST['txtDBHostName']) > 0 ? (string)$_POST['txtDBHostName']  : NULL;
	$DBUserName = isset($_POST['txtDBUserName']) && strlen($_POST['txtDBUserName']) > 0 ? (string)$_POST['txtDBUserName']  : NULL;
	$DBPassword = isset($_POST['txtDBPassword']) && strlen($_POST['txtDBPassword']) >= 0 ? (string)$_POST['txtDBPassword'] : NULL;
	$DBName 	= isset($_POST['txtDBName']) 	 && strlen($_POST['txtDBName']) > 0 ? 	  (string)$_POST['txtDBName'] 	   : NULL;

	$CandyPath 	= isset($_POST['txtCandyPath']) && strlen($_POST['txtCandyPath']) > 0 ? (string)$_POST['txtCandyPath'] : NULL;
	$CandyVideoThumbPath = isset($_POST['txtCandyVideoThumbPath']) && strlen($_POST['txtCandyVideoThumbPath']) > 0 ? (string)$_POST['txtCandyVideoThumbPath'] : NULL;
	
	// Checks
	
	// Read-write config.php
	if(($configfile = file_get_contents('config.php')) !== FALSE)
	{
		$configfile = preg_replace(
			array(
				"/define\('CANDYPATH'           [ \t]*,[ \t]*'[^']+?'\);/ix",
				"/define\('CANDYVIDEOTHUMBPATH' [ \t]*,[ \t]*'[^']+?'\);/ix",
				"/define\('DBHOSTNAME'          [ \t]*,[ \t]*'[^']+?'\);/ix",
				"/define\('DBUSERNAME'          [ \t]*,[ \t]*'[^']+?'\);/ix",
				"/define\('DBPASSWORD'          [ \t]*,[ \t]*'[^']+?'\);/ix",
				"/define\('DBNAME'              [ \t]*,[ \t]*'[^']+?'\);/ix"),
			array(
				sprintf("define('CANDYPATH', '%1\$s');", $CandyPath),
				sprintf("define('CANDYVIDEOTHUMBPATH', '%1\$s');", $CandyVideoThumbPath),
				sprintf("define('DBHOSTNAME', '%1\$s');", $DBHostName),
				sprintf("define('DBUSERNAME', '%1\$s');", $DBUserName),
				sprintf("define('DBPASSWORD', '%1\$s');", $DBPassword),
				sprintf("define('DBNAME', '%1\$s');", $DBName)),
			$configfile
		);
	
		if(@file_put_contents('config.php', $configfile) === FALSE)
		{
			$e = new Error(NULL, $lang->g('ErrorSetupWritingConfig'));
			Error::AddError($e);
		}
		else
		{
			$i = new Info($lang->g('MessageConfigWritten'));
			Info::AddInfo($i);
		}
	}
	else 
	{
		$e = new Error(NULL, $lang->g('ErrorSetupWritingConfig'));
		Error::AddError($e);
	}
}

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
           
	$(function(){
		SwitchTab('1');
	});
           
	function RedirToSFV(){
		var modelIdSfv = parseInt( $('#selModelSfv').val() );
		var includePath = $('input[name=radSfvPath]:checked').val();
		var url = 'download_sfv.php';

		url += '?model_id=' + (isNaN(modelIdSfv) || modelIdSfv <= 0 ? '' : modelIdSfv); 
		url += '&includepath=' + includePath;

		window.location = url;
		return true;
	}
	
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

	function RedirToCSV(){
		var modelIdCsv = parseInt( $('#selModelCsv').val() );
		var includePic = $('#chkCSVIncludePic').is(':checked');
		var includeVid = $('#chkCSVIncludeVid').is(':checked');
		var taggedonly = $('#chkCSVIncludeTag').is(':checked');
		var url = 'download_csv.php';

		url += '?model_id=' + (isNaN(modelIdCsv) || modelIdCsv <= 0 ? '' : modelIdCsv);

		window.location = url;
		return TRUE;
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

<ul class="TabHeader">
<li><a href="#" id="tablink1" onclick="SwitchTab('1');"><?php echo $lang->g('LabelConfiguration')?></a></li>
<li><a href="#" id="tablink2" onclick="SwitchTab('2');"><?php echo $lang->g('LabelCleanUp')?></a></li>
<li><a href="#" id="tablink3" onclick="SwitchTab('3');"><?php echo $lang->g('LabelExports')?></a></li>
</ul>

<div class="TabsContainer">

<div class="TabContent" id="tab1">
<fieldset>

	<input type="hidden" id="hidAction" name="hidAction" value="EditConfiguration" />
	
	<h3><?php echo $lang->g('LabelDatabase')?></h3>
	
	<div class="FormRow">
	<label for="txtDBHostName"><?php echo $lang->g('LabelHostname')?>: <em>*</em></label>
	<input type="text" id="txtDBHostName" name="txtDBHostName" maxlength="100" value="<?php echo $DBHostName?>" />
	</div>
	
	<div class="FormRow">
	<label for="txtDBUserName"><?php echo $lang->g('LabelUsername')?>: <em>*</em></label>
	<input type="text" id="txtDBUserName" name="txtDBUserName" maxlength="100" value="<?php echo $DBUserName?>" />
	</div>
	
	<div class="FormRow">
	<label for="txtDBPassword"><?php echo $lang->g('LabelPassword')?>: <em>*</em></label>
	<input type="text" id="txtDBPassword" name="txtDBPassword" maxlength="100" value="<?php echo $DBPassword?>" />
	</div>
	
	<div class="FormRow">
	<label for="txtDBName"><?php echo $lang->g('LabelDatabaseName')?>: <em>*</em></label>
	<input type="text" id="txtDBName" name="txtDBName" maxlength="100" value="<?php echo $DBName?>" />
	</div>
	
	<h3>Candydoll <?php echo $lang->g('LabelCollection')?></h3>
	
	<div class="FormRow">
	<label for="txtCandyPath"><?php echo $lang->g('LabelPathOnDisk')?>:</label>
	<input type="text" id="txtCandyPath" name="txtCandyPath" maxlength="255" value="<?php echo $CandyPath?>" />
	</div>
	
	<div class="FormRow">
	<label for="txtCandyVideoThumbPath"><?php echo $lang->g('LabelThumbnails')?>:</label>
	<input type="text" id="txtCandyVideoThumbPath" name="txtCandyVideoThumbPath" maxlength="255" value="<?php echo $CandyVideoThumbPath?>" />
	</div>
	
	<div class="Separator"></div>

	<input type="submit" id="btnSubmitConfig" name="btnSubmitConfig" value="<?php echo $lang->g('ButtonSave')?>" />	

</fieldset>
</div>

<div class="TabContent" id="tab2">
<fieldset>

	<div class="FormRow WideForm">
	<label><?php echo $lang->g('LabelCleanCacheFolder')?></label>
		<input type="button" id="btnCleanCache" name="btnCleanCache" value="<?php echo $lang->g('ButtonClean')?>"
		<?php echo $CurrentUser->hasPermission(RIGHT_CACHE_CLEANUP) ? "onclick=\"window.location='cacheimage_nuke.php';\"" : HtmlStuff::DisabledStr(TRUE) ?>
		/>
	<?php echo $CacheInSync?>
	</div>

</fieldset>
</div>

<div class="TabContent" id="tab3">
<fieldset>

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
	
	<div class="FormRow WideForm">
	<label><?php echo $lang->g('LabelDownloadSFV')?></label>
	<input type="button" id="btnDownloadSFV" name="btnDownloadSFV" value="<?php echo $lang->g('ButtonDownload')?>"
		<?php echo $CurrentUser->hasPermission(RIGHT_EXPORT_XML) ? "onclick=\"RedirToSFV();\"" : HTMLstuff::DisabledStr(TRUE) ?>
	/>
	<br />
	<label for="selModelSfv" style="float:none;width:auto;"><?php echo $lang->g('LabelModel')?>: </label>
	<select id="selModelSfv" name="selModelSfv"<?php echo HTMLstuff::DisabledStr(!$CurrentUser->hasPermission(RIGHT_EXPORT_XML))?>>
		<option value=""><?php echo $lang->g('LabelAllModels')?></option>
		<?php echo $ModelsOptions?>
	</select>
	<input type="radio" id="radSfvPathNone" name="radSfvPath" value="<?php echo EXPORT_PATH_OPTION_NONE?>"<?php echo HTMLstuff::DisabledStr(!$CurrentUser->hasPermission(RIGHT_EXPORT_XML))?> /> 
	<label for="radSfvPathNone" class="Radio"><?php echo $lang->g('LabelSFVPathNone')?></label>
	<input type="radio" id="radSfvPathRelative" name="radSfvPath" value="<?php echo EXPORT_PATH_OPTION_RELATIVE?>"<?php echo HTMLstuff::DisabledStr(!$CurrentUser->hasPermission(RIGHT_EXPORT_XML))?> checked="checked" /> 
	<label for="radSfvPathRelative" class="Radio"><?php echo $lang->g('LabelSFVPathRelative')?></label>
	<input type="radio" id="radSfvPathFull" name="radSfvPath" value="<?php echo EXPORT_PATH_OPTION_FULL?>"<?php echo HTMLstuff::DisabledStr(!$CurrentUser->hasPermission(RIGHT_EXPORT_XML))?> /> 
	<label for="radSfvPathFull" class="Radio"><?php echo $lang->g('LabelSFVPathFull')?></label>
	</div>
	
	<hr />
	
	<div class="FormRow WideForm">
	<label>Download CSV</label>
	<input type="button" id="btnDownloadCSV" name="btnDownloadCSV" value="<?php echo $lang->g('ButtonDownload')?>" onclick="RedirToCSV();"<?php echo HTMLstuff::DisabledStr(!$CurrentUser->hasPermission(RIGHT_EXPORT_CSV))?> />
	<br />
	<label for="selModelCsv" style="float:none;width:auto;"><?php echo $lang->g('LabelModel')?>: </label>
	<select id="selModelCsv" name="selModelCsv"<?php echo HTMLstuff::DisabledStr(!$CurrentUser->hasPermission(RIGHT_EXPORT_CSV))?>>
		<option value=""><?php echo $lang->g('LabelAllModels')?></option>
		<?php echo $ModelsOptions?>
	</select>
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

</fieldset>
</div>

</div>

</form>

<?php echo HTMLstuff::Button('index.php')?>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>