<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$q = null;
$searchMode = 'MODEL';
$Tags = Tag::GetTags();
$Tag2Alls = array();
$ModelCount = 0;
$Modelprint = '';


if(array_key_exists('hidAction', $_POST) && $_POST['hidAction'] == 'Search')
{
	/* Core search-processing */
	$q = $_POST['q'];
	$searchMode  = $_POST['selectType'];
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
	 * Model::Fetch(model_id in $ModelIDsToShow)
	 * Set::Fetch(set_id in $SetIDsToShow or model_id in $ModelIDsToShow)
	 * Image::Fetch(image_id in $ImageIDsToShow or set_id in $SetIDsToShow)
	 * Video::Fetch(video_id in $VideoIDsToShow or set_id in $SetIDsToShow)
	 * 
	 * Construct DIVs with thumbnails, etc.
	 */
	
	switch ($searchMode)
	{
		
		case 'MODEL':
			if(!$ModelIDsToShow){
				break;
			}
			$where = sprintf('model_id in ( %1$s ) AND mut_deleted = -1', join(', ', $ModelIDsToShow));
			$ToShow = Model::GetModels($where);
			break;

			
		case 'SET':
			if(!$SetIDsToShow && !$ModelIDsToShow){
				break;
			}
			$where = sprintf('( set_id in ( %1$s ) OR model_id in ( %2$s ) ) AND mut_deleted = -1',
				join(', ', $SetIDsToShow),
				join(', ', $ModelIDsToShow)
			);
			$ToShow = Set::GetSets($where);
			break;
			
			
		case 'IMAGE':
			break;
			
			
		case 'VIDEO':
			break;
	}
	
	
	echo "ModelIDs => ";
	var_dump($ModelIDsToShow);
	
	echo "<br />\n";
	
	echo "SetIDs => ";
	var_dump($SetIDsToShow);
	
	echo "<br />\n";
	
	echo "ImageIDs => ";
	var_dump($ImageIDsToShow);
	
	echo "<br />\n";
	
	echo "VideoIDs => ";
	var_dump($VideoIDsToShow);
	if($ToShow)
	{
		foreach($ToShow as $Model)
		{
		$ModelCount++;
		switch ($searchMode)
			{
				case "MODEL":
					$Modelprint .= sprintf(
			"<div class=\"ThumbGalItem\">
			<h3 class=\"Hidden\">%1\$s</h3>
			<div class=\"ThumbImageWrapper\">
			<a href=\"set.php?model_id=%2\$d\">
			<img src=\"download_image.php?model_id=%2\$d&amp;portrait_only=true&amp;width=150&amp;height=225\" width=\"150\" height=\"225\" alt=\"%1\$s\" title=\"%1\$s\" />
			</a>
			</div>
			<div class=\"ThumbDataWrapper\">
			<ul>
			<li>Name: %1\$s</li>
			</ul>
			</div>
			</div>
			%3\$s",
			htmlentities($Model->GetFullName()),
			$Model->getID(),
			($ModelCount % 4 == 0 ? "<div class=\"Clear\"></div>" : null)
			);
				break;
				case "SET":
				$Modelprint .= sprintf(
				"<div class=\"SetThumbGalItem\">
				<h3 class=\"Hidden\">%1\$s set %2\$s</h3>
				<div class=\"SetThumbImageWrapper\">
				<a href=\"image.php?model_id=%3\$d&amp;set_id=%4\$d\">
				<img src=\"download_image.php?set_id=%4\$d&amp;landscape_only=true&amp;width=225&amp;height=150\" height=\"150\" alt=\"%1\$s set %2\$s\" title=\"%1\$s set %2\$s\" />
				</a>
				</div>
				<div class=\"SearchThumbDataWrapper\">
					<ul>
				<li>Model: %s</li>
				<li>Name: %s</li>
				</ul>
				</div>
				</div>
				%5\$s",
				htmlentities($Model->getModel()->GetFullName()),
				htmlentities($Model->getName()),
				$Model->getModel()->getID(),
				$Model->getID(),
				($ModelCount % 3 == 0 ? "<div class=\"Clear\"></div>" : null)
				);
				break;
				case "IMAGE":
				$Modelprint = "No Results";
				break;
				case "VIDEO":
				$Modelprint = "No Results";
				break;
			}
		}
	}
}

echo HTMLstuff::HtmlHeader('Tag search', $CurrentUser);

?>

<h2><a href="index.php">Home</a> - Tag search</h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>" method="post" class="Search">
<fieldset><legend>Please fill in these fields:</legend>

<input type="hidden" id="hidAction" name="hidAction" value="Search" />

<label for="selectType">Search For:</label>
<select id="selectType" name="selectType">
	<option value="SET" <?php echo $searchMode == "SET" ? ' selected="selected"' : null ?>>Sets</option>
	<option value="MODEL" <?php echo $searchMode == "MODEL" ? ' selected="selected"' : null ?>>Models</option>
	<option value="IMAGE" <?php echo $searchMode == "IMAGE" ? ' selected="selected"' : null ?>>Images</option>
	<option value="VIDEO" <?php echo $searchMode == "VIDEO" ? ' selected="selected"' : null ?>>Videos</option>
</select>
<label for="q">Tagged with
</label>
<input type="text" id="q" name="q" class="TagsBox" value="<?php echo $q; ?>" />
<input type="submit" class="FormButton" value="Search" />

<div class="Separator"></div>
<?php

echo "<div class=\"Clear\"></div>".$Modelprint."<div class=\"Clear\"></div>";
echo "<div style=\"font-weight: bold;text-align: center\">".$ModelCount." results returned</div>";
?>
<div class="Separator"></div>
<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>