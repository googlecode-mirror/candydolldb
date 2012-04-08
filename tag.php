<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$TagID = Utils::SafeIntFromQS('tag_id');
$DeleteTag = (array_key_exists('hidTagToDelete', $_POST) && $TagID && $_POST['hidTagToDelete'] == $TagID);

$Tags = Tag::GetTags();
$Tag = Tag::FilterTags($Tags, $TagID);
$Tag = $TagID && $Tag ? $Tag[0] : new Tag();

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
				$wc = sprintf('tag_id = %1d', $Tag->getID());
				$t2as = Tag2All::GetTag2Alls($wc);
				
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
	$TagList .= sprintf('<a class="TagSelect" href="tag.php?tag_id=%1$d">%2$s%3$s</a>',
		$t->getID(),
		htmlentities($t->getName()),
		$t->getID() == $Tag->getID() ? ' <span>*</span>' : null
	);
}

echo HTMLstuff::HtmlHeader('Manage tags', $CurrentUser);

?>

<h2><?php echo sprintf('<a href="index.php">Home</a> - Manage tags - %1$s',
	htmlentities($Tag->getID() ? $Tag->getName() : 'New')
); ?></h2>

<div style="float:right; margin: 0 0 48px 30px;">

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
<fieldset><legend>Please fill in these fields:</legend>

<input type="hidden" id="hidAction" name="hidAction" value="TagView" />
<input type="hidden" id="hidTagToDelete" name="hidTagToDelete" value="" />

<div class="FormRow">
<label for="txtName" style="width:60px;">Name: <em>*</em></label>
<input type="text" id="txtName" name="txtName" maxlength="50" value="<?php echo $Tag->getName();?>"<?php echo HTMLstuff::DisabledStr($DeleteTag); ?> />
</div>

<div class="FormRow">
<label style="width:60px;">&nbsp;</label>
<input type="submit" class="FormButton" value="<?php echo $DeleteTag ? 'Delete' : 'Save'; ?>" />
<input type="button" class="FormButton" value="Cancel" onclick="window.location='tag.php';" />

<?php if($Tag->getID()) { ?>
	<input type="checkbox" id="chkDel" title="Delete selected tag" onclick="
		$('#hidTagToDelete').val(<?php echo $Tag->getID(); ?>); 
		$('#txtName, #chkDel').attr('disabled', 'disabled');
		$('input[type=submit]').val('Delete');" />
<?php } ?>

<input type="button" class="FormButton" value="Clean" title="Clear unused tags from list" onclick="window.location='tag_nuke.php';" />
</div>

</fieldset>
</form>

</div>

<?php echo $TagList; ?>

<div class="Clear Separator"></div>

<?php
echo HTMLstuff::Button('tag.php', 'Create new tag');
echo HTMLstuff::Button('index.php');
echo HTMLstuff::HtmlFooter($CurrentUser);
?>