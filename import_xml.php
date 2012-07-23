<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'UploadXML')
{
	$f = $_FILES['fileXML'];
	if($f['error'] === UPLOAD_ERR_OK)
	{
		$d = new DOMDocument();

		if(@$d->load(realpath($f['tmp_name'])) === true)
		{
			if(@$d->schemaValidate(realpath('./candydolldb.xsd')) === true)
			{
				$tempFilename = sprintf('cache/%1$s.xml', Utils::GUID());
				$d->saveHTMLFile($tempFilename);
				
				header('location:setup_data.php?file='.urlencode($tempFilename));
				exit;
			}
			else
			{
				$e = new XMLerror(XML_ERR_SCHEMA_VALID);
				Error::AddError($e);
			}
		}
		else
		{
			$e = new XMLerror(XML_ERR_XML_VALID);
			Error::AddError($e);
		}
	}
	else
	{
		$e = new UploadError($f['error']);
		Error::AddError($e);
	}
}

echo HTMLstuff::HtmlHeader($lang->g('NavigationImportXML'), $CurrentUser); ?>

<h2><?php echo sprintf('<a href="index.php">%1$s</a> - %2$s',
		$lang->g('NavigationHome'),
		$lang->g('NavigationImportXML')
)?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post" enctype="multipart/form-data">
<fieldset>

<script type="text/javascript">
//<![CDATA[

	$(document).ready(function(){
		$('#fileXML').change(function(){
			$('#radUp').attr('checked', 'checked');
		});
	});
           
	function HandleXMLImport(){
		if($('#radIn').is(':checked')){
			window.location = 'setup_data.php';
			return false;
		}
		return true;
	}
           
//]]>
</script>

<input type="hidden" id="hidAction" name="hidAction" value="UploadXML" />

<div class="FormRow">
<label><?php echo $lang->g('LabelXMLFile')?>: <em>*</em></label>
<input type="radio" name="inOrUp" value="IN" id="radIn" />&nbsp;&nbsp;&nbsp;<label class="Radio" for="radIn">setup_data.xml</label>
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="radio" name="inOrUp" value="UP" id="radUp" />&nbsp;&nbsp;&nbsp;<input type="file" id="fileXML" name="fileXML" />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $lang->g('ButtonImportXML')?>" onclick="return HandleXMLImport();" />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='index.php';" />
</div>

<div class="Separator"></div>

<?php echo HTMLstuff::Button()?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>