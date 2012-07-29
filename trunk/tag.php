<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();
HTMLstuff::RefererRegister($_SERVER['REQUEST_URI']);

$TagID = Utils::SafeIntFromQS('tag_id');
$DeleteTag = (array_key_exists('hidTagToDelete', $_POST) && $TagID && $_POST['hidTagToDelete'] == $TagID);

$Tags = Tag::GetTags();
$Tag = Tag::FilterTags($Tags, $TagID);
$Tag = $TagID && $Tag ? $Tag[0] : new Tag();

$Tagcount = 0;
$TagList = null;

if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'TagView')
{
	$Tag->setName($_POST['txtName']);	
	
	if($Tag->getID())
	{
		if($DeleteTag)
		{
			if(Tag::DeleteTag($Tag, $CurrentUser))
			{
				$t2as = Tag2All::GetTag2Alls(new Tag2AllSearchParameters($Tag->getID()));
				
				foreach($t2as as $t2a){
					Tag2All::Delete($t2a, $CurrentUser);
				}
				
				header('location:'.$_SERVER['PHP_SELF']);
				exit;
			}
		}
		else
		{
			if(Tag::UpdateTag($Tag, $CurrentUser))
			{
				header('location:'.$_SERVER['PHP_SELF']);
				exit;
			}
		}
	}
	else
	{
		if(Tag::InsertTag($Tag, $CurrentUser))
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
		$t->getID() == $Tag->getID() ? ' <span>*</span>' : null
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
<input type="text" id="txtName" name="txtName" maxlength="50" value="<?php echo $Tag->getName()?>"<?php echo HTMLstuff::DisabledStr($DeleteTag)?> />
</div>

<div class="FormRow">
<label style="width:60px;">&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteTag ? $lang->g('ButtonDelete') : $lang->g('ButtonSave')?>" />
<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonCancel')?>" onclick="window.location='tag.php';" />

<?php if($Tag->getID()) { ?>
	<input type="checkbox" id="chkDel" title="<?php echo $lang->g('LabelDeleteSelectedTag')?>" onclick="
		$('#hidTagToDelete').val(<?php echo $Tag->getID()?>); 
		$('#txtName, #chkDel').attr('disabled', 'disabled');
		$('input[type=submit]').val('<?php echo $lang->g('ButtonDelete')?>');" />
<?php } ?>

<input type="button" class="FormButton" value="<?php echo $lang->g('ButtonClean')?>"
	<?php echo $CurrentUser->hasPermission(RIGHT_TAG_CLEANUP) ? " onclick=\"window.location='tag_nuke.php';\"" : HTMLstuff::DisabledStr(true) ?>
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