<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$TagID = Utils::SafeIntFromQS('tag_id');
$DeleteTag = (array_key_exists('hidTagToDelete', $_POST) && $TagID && $_POST['hidTagToDelete'] == $TagID);

$Tags = Tag::GetTags();
$Tag = Tag::Filter($Tags, $TagID);
$Tag = $TagID && $Tag ? $Tag[0] : new Tag();

$Tagcount = 0;
$TagList = NULL;

$DisableControls =
	$DeleteTag ||
	(!$CurrentUser->hasPermission(RIGHT_TAG_EDIT) && !is_null($TagID)) ||
	(!$CurrentUser->hasPermission(RIGHT_TAG_ADD) && is_null($TagID));

$DisableDefaultButton =
	(!$CurrentUser->hasPermission(RIGHT_TAG_DELETE) && !is_null($TagID) && $DeleteTag) ||
	(!$CurrentUser->hasPermission(RIGHT_TAG_EDIT) && !is_null($TagID) && !$DeleteTag) ||
	(!$CurrentUser->hasPermission(RIGHT_TAG_ADD) && is_null($TagID));

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'TagView')
{
	$Tag->setName(Utils::NullIfEmpty($_POST['txtName']));	
	
	if($Tag->getID())
	{
		if($DeleteTag)
		{
			if($CurrentUser->hasPermission(RIGHT_TAG_DELETE) && Tag::Delete($Tag, $CurrentUser))
			{
				$t2as = Tag2All::GetTag2Alls(new Tag2AllSearchParameters($Tag->getID()));
				Tag2All::DeleteMulti($t2as, $CurrentUser);
				
				header('location:'.$_SERVER['PHP_SELF']);
				exit;
			}
		}
		else
		{
			if($CurrentUser->hasPermission(RIGHT_TAG_EDIT) && Tag::Update($Tag, $CurrentUser))
			{
				header('location:'.$_SERVER['PHP_SELF']);
				exit;
			}
		}
	}
	else
	{
		if($CurrentUser->hasPermission(RIGHT_TAG_ADD) && Tag::Insert($Tag, $CurrentUser))
		{
			header('location:'.$_SERVER['PHP_SELF']);
			exit;
		}
	}
}

foreach($Tags as $t){
	$Tagcount++;
	$TagList .= sprintf('<a class="TagSelect" href="tag.php?tag_id=%1$d">%2$s%3$s</a>',
		$t->getID(),
		htmlentities($t->getName()),
		$t->getID() == $Tag->getID() ? ' <span>*</span>' : NULL
	);
}

echo HTMLstuff::HtmlHeader($lang->g('NavigationManageTags'), $CurrentUser);

?>

<h2><?php echo sprintf('<a href="index.php">%3$s</a> - %2$s - %1$s',
	htmlentities($Tag->getID() ? $Tag->getName() : $lang->g('LabelNew')),
	$lang->g('NavigationManageTags'),
	$lang->g('NavigationHome')
)?></h2>

<div style="float:right; margin: 0 0 48px 30px;">

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI'])?>" method="post">
<fieldset>

<input type="hidden" id="hidAction" name="hidAction" value="TagView" />
<input type="hidden" id="hidTagToDelete" name="hidTagToDelete" value="" />

<div class="FormRow">
<label for="txtName" style="width:60px;"><?php echo $lang->g('LabelName')?>: <em>*</em></label>
<input type="text" id="txtName" name="txtName" maxlength="50" value="<?php echo $Tag->getName()?>"<?php echo HTMLstuff::DisabledStr($DisableControls)?> />
</div>

<div class="FormRow">
<label style="width:60px;">&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteTag ? $lang->g('ButtonDelete') : $lang->g('ButtonSave')?>"<?php echo HTMLstuff::DisabledStr($DisableDefaultButton)?> />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='tag.php';" />

<?php if($Tag->getID()) { ?>
	<input type="checkbox" id="chkDel" title="<?php echo $lang->g('LabelDeleteSelectedTag')?>"<?php echo HTMLstuff::DisabledStr(!$CurrentUser->hasPermission(RIGHT_TAG_DELETE))?> onclick="
		$('#hidTagToDelete').val(<?php echo $Tag->getID()?>); 
		$('#txtName, #chkDel').attr('disabled', 'disabled');
		$('input[type=submit]').val('<?php echo $lang->g('ButtonDelete')?>').attr('disabled', false);" />
<?php } ?>

<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonClean')?>"
	<?php echo $CurrentUser->hasPermission(RIGHT_TAG_CLEANUP) ? " onclick=\"window.location='tag_nuke.php';\"" : HTMLstuff::DisabledStr(TRUE) ?>
/>
</div>

</fieldset>
</form>

</div>

<?php echo $TagList?>

<div class="Clear Separator"></div>

<?php echo '<div style="text-align: center;font-weight: bold">'.$lang->g('LabelTotalTagCount').': '.$Tagcount.'</div>'?>

<div class="Clear Separator"></div>

<?php
echo HTMLstuff::Button('tag.php', $lang->g('ButtonCreateNewTag'));
echo HTMLstuff::Button('index.php');
echo HTMLstuff::HtmlFooter($CurrentUser);
?>