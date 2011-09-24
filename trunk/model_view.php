<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

if(array_key_exists('model_id', $_GET) && $_GET['model_id'] && is_numeric($_GET['model_id'])){
	$ModelID = (int)$_GET['model_id'];
}else{
	$ModelID = null;
}

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

	if($Model->getID())
	{
		if($DeleteModel)
		{
		    if(Model::DeleteModel($Model, $CurrentUser))
		    { header('location:index.php'); }
		}
		else
		{
		    if(Model::UpdateModel($Model, $CurrentUser))
		    { header('location:index.php'); }
		}
	}
	else
	{
		if(Model::InsertModel($Model, $CurrentUser))
		{ header('location:index.php'); }
	}
}

echo HTMLstuff::HtmlHeader($Model->GetShortName(), $CurrentUser);

if($ModelID)
{
	echo HTMLstuff::ImageLoading(
		sprintf('download_image.php?model_id=%1$d&random_pic=true&portrait_only=true', $ModelID),
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
<label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteModel ? 'Delete' : 'Save'; ?>" />
<input type="button" class="FormButton" value="Cancel" onclick="window.location='index.php';" />
</div>

<div class="Separator"></div>

<?php echo $ModelID ? HTMLstuff::Button(sprintf('set.php?model_id=%1$d', $ModelID), 'Sets') : ''; ?>

<?php echo $ModelID ? HTMLstuff::Button(sprintf('download_index.php?model_id=%1$d&amp;width=500&amp;height=750', $ModelID), 'Thumbnails', 'rel="lightbox"') : ''; ?>

<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter();
?>