<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'UploadXML')
{
	if($_FILES['fileXML']['error'] === UPLOAD_ERR_OK)
	{
		//$d = new DOMDocument();
		//$d->load('setup_data.xml');
		/*var_dump(
			$d->schemaValidate('candydolldb.xsd')
		);*/
	}
	else
	{
		$e = new UploadError();
		$e->setErrorNumber($_FILES['fileXML']['error']);
		$e->setErrorMessage(UploadError::TranslateUploadError($_FILES['fileXML']['error']));
		Error::AddError($e);
	}
	
	//
}

echo HTMLstuff::HtmlHeader($lang->g('NavigationImportXML'), $CurrentUser); ?>

<h2><?php echo sprintf('<a href="index.php">%1$s</a> - %2$s',
		$lang->g('NavigationHome'),
		$lang->g('NavigationImportXML')
)?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post" enctype="multipart/form-data">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="UploadXML" />

<div class="FormRow">
<label for="fileXML"><?php echo $lang->g('LabelXMLFile')?>: <em>*</em></label>
<input type="file" id="fileXML" name="fileXML" />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $lang->g('ButtonImportXML')?>" />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='index.php';" />
</div>

<div class="Separator"></div>

<?php echo HTMLstuff::Button()?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>