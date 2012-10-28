<?php
/* This file is part of CandyDollDB.

CandyDollDB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CandyDollDB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CandyDollDB. If not, see <http://www.gnu.org/licenses/>.
*/

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

if(!$CurrentUser->hasPermission(RIGHT_IMPORT_XML))
{
	$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
	Error::AddError($e);
	HTMLstuff::RefererRedirect();
}

HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'UploadXML')
{
	$f = $_FILES['fileXML'];
	if($f['error'] === UPLOAD_ERR_OK)
	{
		$d = new DOMDocument();

		if(@$d->load(realpath($f['tmp_name'])) === TRUE)
		{
			if(@$d->schemaValidate(realpath('./candydolldb.xsd')) === TRUE)
			{
				$tempFilename = sprintf('cache/%1$s.xml', Utils::UUID());
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
