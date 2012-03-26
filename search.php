<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$q = null;
$Models = array();
$Tags = Tag::GetTags();

// Unfortunately, this is a killer-query. It maxes out my 128M PHP-memory limit... :(
//$AllIDs = AllIDs::GetAllIDs();

$Tag2Alls = Tag2All::GetTag2Alls();


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'Search')
{
	$q = $_POST['q'];
	
	$filteredTags = Tag::FilterTagsByCSV($Tags, $q);
	$filteredTagIDs = array();
	
	foreach($filteredTags as $t){
		$filteredTagIDs[] = $t->getID();
	}
}

echo HTMLstuff::HtmlHeader('Tag search', $CurrentUser);

?>

<h2><a href="index.php">Home</a> - Tag search</h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>" method="post">
<fieldset><legend>Please fill in these fields:</legend>

<input type="hidden" id="hidAction" name="hidAction" value="Search" />

<input type="text" id="q" name="q" class="TagsBox" value="<?php echo $q; ?>" />
<input type="submit" class="FormButton" value="Search" />

<div class="Separator"></div>

<?php var_dump($Models); ?>

<div class="Separator"></div>

<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>