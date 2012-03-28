<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$q = null;
$Tags = Tag::GetTags();
$Tag2Alls = array();


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'Search')
{
	/* Core search-processing */
	$q = $_POST['q'];
	$filteredTags = Tag::FilterTagsByCSV($Tags, $q);
	$filteredTagIDs = array();
	
	foreach($filteredTags as $t){
		$filteredTagIDs[] = $t->getID();
	}
	
	/* Fetch Tag2Alls if tags were entered, default to no results when $q is empty. */
	$whereClause = $filteredTagIDs ? sprintf('tag_id IN ( %1$s )', join(', ', $filteredTagIDs)) : '1 = 0';
	$Tag2Alls = Tag2All::GetTag2Alls($whereClause);
	
	
	
	/* Model-filtering */
	$AllModelIDs = array();
	foreach($Tag2Alls as $t2a){
		$AllModelIDs[] = $t2a->getModelID();
	}
	
	$ModelIDsToShow = array();
	foreach(array_unique($AllModelIDs) as $modelid)
	{
		foreach ($filteredTagIDs as $ftid)
		{
			if(count(Tag2All::FilterTag2Alls($Tag2Alls, $ftid, $modelid, null, null, null)) == 0)
			{ continue 2; }
		}
		$ModelIDsToShow[] = $modelid;
	}
	
	
	/* Set-filtering */
	$AllSetIDs = array();
	foreach($Tag2Alls as $t2a){
		$AllSetIDs[] = $t2a->getSetID();
	}
	
	$SetIDsToShow = array();
	foreach(array_unique($AllSetIDs) as $setid)
	{
		foreach ($filteredTagIDs as $ftid)
		{
			if(count(Tag2All::FilterTag2Alls($Tag2Alls, $ftid, null, $setid, null, null)) == 0)
			{ continue 2; }
		}
		$SetIDsToShow[] = $setid;
	}

	
	/* Image-filtering */
	$AllImageIDs = array();
	foreach($Tag2Alls as $t2a){
		$AllImageIDs[] = $t2a->getImageID();
	}
	
	$ImageIDsToShow = array();
	foreach(array_unique($AllImageIDs) as $imageid)
	{
		foreach ($filteredTagIDs as $ftid)
		{
			if(count(Tag2All::FilterTag2Alls($Tag2Alls, $ftid, null, null, $imageid, null)) == 0)
			{ continue 2; }
		}
		$ImageIDsToShow[] = $imageid;
	}
	
	
	/* Video-filtering */
	$AllVideoIDs = array();
	foreach($Tag2Alls as $t2a){
		$AllVideoIDs[] = $t2a->getVideoID();
	}
	
	$VideoIDsToShow = array();
	foreach(array_unique($AllVideoIDs) as $videoid)
	{
		foreach ($filteredTagIDs as $ftid)
		{
			if(count(Tag2All::FilterTag2Alls($Tag2Alls, $ftid, null, null, null, $videoid)) == 0)
			{ continue 2; }
		}
		$VideoIDsToShow[] = $videoid;
	}
	
	
	
	/* @TODO
	 * Fetch and show what we want to see:
	 * 
	 * Model::Fetch(model_id in $ModelIDsToShow)	or
	 * Set::Fetch(set_id in $SetIDsToShow)			or
	 * Image::Fetch(image_id in $ImageIDsToShow)	or
	 * Video::Fetch(video_id in $VideoIDsToShow)
	 * 
	 * Construct DIVs with thumbnails, etc.
	 */
	
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

<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>