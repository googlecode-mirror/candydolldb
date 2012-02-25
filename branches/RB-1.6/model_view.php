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

$ModelID = Utils::SafeIntFromQS('model_id');

$DeleteModel = (array_key_exists('cmd', $_GET) && $_GET['cmd'] && ($_GET['cmd'] == COMMAND_DELETE)); 

if($ModelID)
{
	$WhereClause = sprintf('model_id = %1$d AND mut_deleted = -1', $ModelID);
	$Models = Model::GetModels($WhereClause);

	if($Models)
	{ $Model = $Models[0]; }
	else
	{ HTMLstuff::RefererRedirect(); }
}
else
{
	$Model = new Model(null, 'New model');
}

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'ModelView')
{
	$Model->setFirstName($_POST['txtFirstName']);
	$Model->setLastName($_POST['txtLastName']);

	if($_POST['txtBirthDate'] && $_POST['txtBirthDate'] != 'YYYY-MM-DD' && strtotime($_POST['txtBirthDate']) !== false)
	{ $Model->setBirthDate(strtotime($_POST['txtBirthDate'])); }
	else
	{ $Model->setBirthDate(-1); }

	$Model->setRemarks($_POST['txtRemarks']);
	
	if($Model->getID())
	{
		if($DeleteModel)
		{
		    if(Model::DeleteModel($Model, $CurrentUser))
		    {
		    	header('location:index.php');
		    	exit;
		    }
		}
		else
		{
		    if(Model::UpdateModel($Model, $CurrentUser))
		    {
		    	header('location:index.php');
		    	exit;
		    }
		}
	}
	else
	{
		if(Model::InsertModel($Model, $CurrentUser))
		{
			header('location:index.php');
		    exit;
		}
	}
	
	
}

echo HTMLstuff::HtmlHeader($Model->GetFullName(), $CurrentUser);

if($ModelID)
{
	echo HTMLstuff::ImageLoading(
		sprintf('download_image.php?model_id=%1$d&width=400&height=600&portrait_only=true', $ModelID),
		400,
		600,
		htmlentities($Model->GetFullName()),
		htmlentities($Model->GetFullName())
	);
	
	echo '<div class="PhotoContainer Loading"></div>';
}


?>

<h2><?php echo sprintf('<a href="index.php">Home</a> - %1$s',
	htmlentities($Model->GetFullName())
); ?></h2>

<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
<fieldset><legend>Please fill in these fields:</legend>

<input type="hidden" id="hidAction" name="hidAction" value="ModelView" />

<div class="FormRow">
<label for="txtFirstName">Firstname: <em>*</em></label>
<input type="text" id="txtFirstName" name="txtFirstName" maxlength="100" value="<?php echo $Model->getFirstName();?>"<?php echo HTMLstuff::DisabledStr($DeleteModel); ?> />
</div>

<div class="FormRow">
<label for="txtLastName">Lastname:</label>
<input type="text" id="txtLastName" name="txtLastName" maxlength="100" value="<?php echo $Model->getLastName();?>"<?php echo HTMLstuff::DisabledStr($DeleteModel); ?> />
</div>

<div class="FormRow">
<label for="txtBirthDate">Birthdate:</label>
<input type="text" id="txtBirthDate" name="txtBirthDate" class="DatePicker"	maxlength="10" value="<?php echo $Model->getBirthDate() > 0 ? date('Y-m-d', $Model->getBirthDate()) : null; ?>"<?php echo HTMLstuff::DisabledStr($DeleteModel); ?> />
</div>

<div class="FormRow">
<label for="txtRemarks">Remarks:</label>
<textarea id="txtRemarks" name="txtRemarks" cols="42" rows="16" <?php echo HTMLstuff::DisabledStr($DeleteModel); ?>><?php echo $Model->getRemarks(); ?></textarea>
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteModel ? 'Delete' : 'Save'; ?>" />
<input type="button" class="FormButton" value="Cancel" onclick="window.location='index.php';" />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="button" class="FormButton" value="Clear cacheimage" onclick="window.location='cacheimage_delete.php?model_id=<?php echo $ModelID ?>';"<?php echo HTMLstuff::DisabledStr($DeleteModel); ?> />
<input type="button" class="FormButton" value="Clear thumbnailscache" onclick="window.location='cacheimage_delete.php?index_id=<?php echo $ModelID ?>';"<?php echo HTMLstuff::DisabledStr($DeleteModel); ?> />
</div>

<div class="Separator"></div>

<?php echo $ModelID ? HTMLstuff::Button(sprintf('set.php?model_id=%1$d', $ModelID), 'Sets') : ''; ?>

<?php echo $ModelID ? HTMLstuff::Button(sprintf('download_image.php?index_id=%1$d&amp;width=500&amp;height=750', $ModelID), 'Thumbnails', 'rel="lightbox"') : ''; ?>

<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>