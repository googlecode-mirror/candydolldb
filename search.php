<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$Tags = Tag::GetTags();

$searchMode = 'SET';
$q = null;
$ToShow = array();
$Tag2Alls = array();
$ItemCount = 0;
$Results = '';


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'Search')
{
	/* Core search-processing */
	$q = $_POST['q'];
	$searchMode = $_POST['selectType'];
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
	
	switch ($searchMode)
	{
		case 'MODEL':
			if(!$ModelIDsToShow){ break; }
			$where = sprintf('model_id in ( %1$s ) AND mut_deleted = -1', join(', ', $ModelIDsToShow));
			$ToShow = Model::GetModels($where);
			break;
			
		case 'SET':
			if(!$SetIDsToShow || !$ModelIDsToShow){ break; }
			$where = sprintf('set_id in ( %1$s ) AND mut_deleted = -1',	join(', ', $SetIDsToShow));
			$ToShow = Set::GetSets($where);
			break;
			
		case 'IMAGE':
			break;
			
		case 'VIDEO':
			break;
	}

	if($ToShow)
	{
		switch ($searchMode)
		{
			case "MODEL":
				foreach($ToShow as $Model)
				{
					/* @var $Model Model */
					$ItemCount++;
					$Results .= sprintf(
						"<div class=\"ThumbGalItem\">
							<h3 class=\"Hidden\">%1\$s</h3>
							<div class=\"ThumbImageWrapper\">
								<a href=\"set.php?model_id=%2\$d\" title=\"%1\$s\">
									<img src=\"download_image.php?model_id=%2\$d&amp;portrait_only=true&amp;width=150&amp;height=225\" width=\"150\" height=\"225\" alt=\"%1\$s\" />
								</a>
							</div>
							<div class=\"SearchThumbDataWrapper\">
								<ul>
									<li>Name: %1\$s</li>
								</ul>
							</div>
						</div>
						%3\$s",
						htmlentities($Model->GetFullName()),
						$Model->getID(),
						($ItemCount % 4 == 0 ? "<div class=\"Clear\"></div>" : null)
					);
				}
				break;
				
			case "SET":
				foreach($ToShow as $Set)
				{
					/* @var $Set Set */
					$ItemCount++;
					$Results .= sprintf(
						"<div class=\"SetThumbGalItem\">
							<h3 class=\"Hidden\">%1\$s set %3\$s</h3>
							<div class=\"SetThumbImageWrapper\">
								<a href=\"image.php?model_id=%4\$d&amp;set_id=%5\$d\" title=\"%1\$s set %3\$s\">
									<img src=\"download_image.php?set_id=%5\$d&amp;landscape_only=true&amp;width=225&amp;height=150\" height=\"150\" alt=\"%1\$s set %3\$s\" />
								</a>
							</div>
							<div class=\"SearchThumbDataWrapper\">
								<ul>
									<li>Model: %1\$s</li>
									<li>Setname: %3\$s</li>
								</ul>
							</div>
						</div>
						%6\$s",
						htmlentities($Set->getModel()->GetFullName()),
						htmlentities($Set->getPrefix()),
						htmlentities($Set->getName()),
						$Set->getModel()->getID(),
						$Set->getID(),
						($ItemCount % 3 == 0 ? "<div class=\"Clear\"></div>" : null)
					);
				}
				break;
					
			case "IMAGE":
				break;
				
			case "VIDEO":
				break;
		}
	}
}

echo HTMLstuff::HtmlHeader('Tag search', $CurrentUser);

?>

<h2><a href="index.php">Home</a> - Tag search</h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>" method="post" class="Search">
<fieldset><legend>Please fill in these fields:</legend>

<input type="hidden" id="hidAction" name="hidAction" value="Search" />

<label for="selectType">Search for</label>
<select id="selectType" name="selectType">
	<option value="MODEL" <?php echo $searchMode == "MODEL" ? ' selected="selected"' : null ?>>Models</option>
	<option value="SET" <?php echo $searchMode == "SET" ? ' selected="selected"' : null ?>>Sets</option>
	<option value="IMAGE" <?php echo $searchMode == "IMAGE" ? ' selected="selected"' : null ?>>Images</option>
	<option value="VIDEO" <?php echo $searchMode == "VIDEO" ? ' selected="selected"' : null ?>>Videos</option>
</select>

<label for="q">tagged with</label>
<input type="text" id="q" name="q" class="TagsBox" style="width:650px;" value="<?php echo $q; ?>" />
<input type="submit" class="FormButton" value="Search" />

<div class="Separator"></div>

<div class="Clear"></div>
<?php echo $Results ?>
<div class="Clear"></div>

<div class="SearchSummary">
<p><?php echo $ItemCount ?> result(s) returned</p>
</div>

<div class="Separator"></div>

<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>