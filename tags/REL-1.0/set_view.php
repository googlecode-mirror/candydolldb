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

if(!array_key_exists('model_id', $_GET) || !$_GET['model_id'] || !is_numeric($_GET['model_id'])){
	header('location:index.php');
}
$ModelID = (int)$_GET['model_id'];

if(array_key_exists('set_id', $_GET) && $_GET['set_id'] && is_numeric($_GET['set_id'])){
	$SetID = (int)$_GET['set_id'];
}else{
	$SetID = null;
}

$DeleteSet = (array_key_exists('cmd', $_GET) && $_GET['cmd'] && ($_GET['cmd'] == COMMAND_DELETE));
$ReturnURL = sprintf('set.php?model_id=%1$d', $ModelID);

/* @var $Set Set */
/* @var $Model Model */
if($SetID != null)
{
	$WhereClause = sprintf('model_id = %1$d AND set_id = %2$d AND mut_deleted = -1', $ModelID, $SetID);
	$Sets = Set::GetSets($WhereClause);

	if($Sets)
	{ $Set = $Sets[0]; }
	else
	{ header('location:index.php'); }
	
	$Model = $Set->getModel();
}
else
{
	$Set = new Set(null, 'New');
	$Model = Model::GetModels(sprintf('model_id = %1$d AND mut_deleted = -1', $ModelID));
	
	if($Model) { $Model = $Model[0]; }
	else { header('location:index.php'); }
	
	$Set->setModel($Model);
}


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'SetView')
{
	$Set->setPrefix($_POST['txtPrefix']);
	$Set->setName($_POST['txtName']);

	if($_POST['txtDatePic'] && $_POST['txtDatePic'] != 'YYYY-MM-DD' && strtotime($_POST['txtDatePic']) !== false)
	{ $Set->setDatePic(strtotime($_POST['txtDatePic'])); }
	else
	{ $Set->setDatePic(-1); }
	
	if($_POST['txtDateVid'] && $_POST['txtDateVid'] != 'YYYY-MM-DD' && strtotime($_POST['txtDateVid']) !== false)
	{ $Set->setDateVid(strtotime($_POST['txtDateVid'])); }
	else
	{ $Set->setDateVid(-1); }
	
	if($_POST['radContains'])
	{ $Set->setContainsWhat(intval($_POST['radContains'])); }

	if($Set->getID())
	{
		if($DeleteSet)
		{
			if(Set::DeleteSet($Set, $CurrentUser))
			{ header('location:'.$ReturnURL); }
		}
		else
		{
			if(Set::UpdateSet($Set, $CurrentUser))
			{ header('location:'.$ReturnURL); }
		}
	}
	else
	{
		if(Set::InsertSet($Set, $CurrentUser))
		{ header('location:'.$ReturnURL); }
	}
}

if($SetID)
{ $ImageTag = sprintf('<img src="download_image.php?set_id=%1$d&amp;portrait_only=true" width="400" height="600" alt="%2$s" title="%2$s" />', $SetID, htmlentities($Model->GetFullName())); }

echo HTMLstuff::HtmlHeader($Model->GetShortName(), $CurrentUser);

?>

<div class="PhotoContainer"><?php echo $ImageTag; ?></div>

<h2><?php echo sprintf('<a href="index.php">Home</a> - <a href="model_view.php?model_id=%1$d">%2$s</a> - <a href="set.php?model_id=%1$d">Sets</a> - %3$s',
	$ModelID,
	htmlentities($Model->GetShortName()),
	htmlentities($Set->getName())
); ?></h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>" method="post">
<fieldset>
<legend>Please fill in these fields:</legend>

<input type="hidden" id="hidAction" name="hidAction" value="SetView" />

<div class="FormRow">
<label for="txtPrefix">Prefix:</label>
<input type="text" id="txtPrefix" name="txtPrefix" maxlength="100" value="<?php echo $Set->getPrefix();?>"<?php echo HTMLstuff::DisabledStr($DeleteSet); ?> />
</div>

<div class="FormRow">
<label for="txtName">Name: <em>*</em></label>
<input type="text" id="txtName" name="txtName" maxlength="100" value="<?php echo $Set->getName();?>"<?php echo HTMLstuff::DisabledStr($DeleteSet); ?> />
</div>

<div class="FormRow">
<label>Contains: </label>
<input type="radio" id="radImages" name="radContains" value="<?php echo SET_CONTENT_IMAGE; ?>"<?php echo ($Set->getContainsWhat() & SET_CONTENT_IMAGE) > 0 ? ' checked="checked"' : null; ?><?php echo HTMLstuff::DisabledStr($DeleteSet); ?> /> 
<label for="radImages" class="Radio">Images</label>
<input type="radio" id="radVideos" name="radContains" value="<?php echo SET_CONTENT_VIDEO; ?>"<?php echo ($Set->getContainsWhat() & SET_CONTENT_VIDEO) > 0 ? ' checked="checked"' : null; ?><?php echo HTMLstuff::DisabledStr($DeleteSet); ?> /> 
<label for="radVideos" class="Radio">Videos</label>
<input type="radio" id="radBoth" name="radContains" value="<?php echo (SET_CONTENT_IMAGE + SET_CONTENT_VIDEO); ?>"<?php echo (($Set->getContainsWhat() & SET_CONTENT_IMAGE) > 0 && ($Set->getContainsWhat() & SET_CONTENT_VIDEO) > 0) ? ' checked="checked"' : null; ?><?php echo HTMLstuff::DisabledStr($DeleteSet); ?> /> 
<label for="radBoth" class="Radio">Both</label>
</div>

<div class="FormRow">
<label for="txtDatePic">Date (pics):</label>
<input type="text" id="txtDatePic" name="txtDatePic" class="DatePicker" maxlength="10" value="<?php echo $Set->getDatePic() > 0 ? date('Y-m-d', $Set->getDatePic()) : null; ?>"<?php echo HTMLstuff::DisabledStr($DeleteSet); ?> />
</div>

<div class="FormRow">
<label for="txtDateVid">Date (vids):</label>
<input type="text" id="txtDateVid" name="txtDateVid" class="DatePicker" maxlength="10" value="<?php echo $Set->getDateVid() > 0 ? date('Y-m-d', $Set->getDateVid()) : null; ?>"<?php echo HTMLstuff::DisabledStr($DeleteSet); ?> />
</div>

<div class="FormRow">
<label>&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteSet ? 'Delete' : 'Save'; ?>" />
<input type="button" class="FormButton" value="Cancel" onclick="window.location='<?php echo $ReturnURL; ?>';" />
</div>

<div class="Separator"></div>


<?php
	if($Set && ($Set->getContainsWhat() & SET_CONTENT_IMAGE) > 0) 
	{ echo HTMLstuff::Button(sprintf('image.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), 'Images'); }
	
	if($Set && ($Set->getContainsWhat() & SET_CONTENT_VIDEO) > 0) 
	{ echo HTMLstuff::Button(sprintf('video.php?model_id=%1$d&amp;set_id=%2$d', $ModelID, $SetID), 'Videos'); }
?>

<?php echo HTMLstuff::Button(sprintf('set.php?model_id=%1$d', $ModelID), 'Sets'); ?>

<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter();
?>