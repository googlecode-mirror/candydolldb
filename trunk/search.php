<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$Tags = Tag::GetTags();

$TotalPages = 0;
$Total = null;
$searchMode = null;
$q = null;
$ToShow = array();
$Tag2Alls = array();
$ItemCount = 0;
$Results = '';

if (isset($_GET['page']))
{ $page = $_GET['page']; }
else
{ $page = 1; }

if (isset($_GET['txtResults']))
{ $max_results = $_GET['txtResults']; }
else
{ $max_results = 30; }

$from = (($page * $max_results) - $max_results);

if(array_key_exists('hidAction', $_GET) && $_GET['hidAction'] == 'Search')
{
	$q = $_SESSION['q']		= $_GET['q'];
	$searchMode = $_SESSION['selectType'] 		= $_GET['selectType'];
}
else
{
	$q = array_key_exists('q', $_SESSION) ? $_SESSION['q'] : '';
	$searchMode = array_key_exists('selectType', $_SESSION) ? $_SESSION['selectType'] : 'SET';
}

if(array_key_exists('hidAction', $_GET) && $_GET['hidAction'] == 'Search')
{

/* Core search-processing */
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
			$ToShow = Model::GetModels($where, null, sprintf("%1\$d, %2\$d", $from, $max_results));
			$Total = count(Model::GetModels($where));
			break;

		case 'SET':
			if(!$SetIDsToShow || !$ModelIDsToShow){ break; }
			$where = sprintf('set_id in ( %1$s ) AND mut_deleted = -1',	join(', ', $SetIDsToShow));
			$ToShow = Set::GetSets($where, null, sprintf("%1\$d, %2\$d", $from, $max_results));
			$Total = count(Set::GetSets($where));
			break;

		case 'IMAGE':
			if(!$SetIDsToShow || !$ModelIDsToShow){ break; }
			$where = $ImageIDsToShow ? sprintf('(image_id in ( %1$s ) OR set_id in ( %2$s )) AND mut_deleted = -1',	join(', ', $ImageIDsToShow), join(', ', $SetIDsToShow)) : null;
			$where = $where ? $where : sprintf('set_id in ( %1$s ) AND mut_deleted = -1',	join(', ', $SetIDsToShow));
			$ToShow = Image::GetImages($where, null, sprintf("%1\$d, %2\$d", $from, $max_results));
			$Total = count(Image::GetImages($where));
			break;

		case 'VIDEO':
			if(!$SetIDsToShow || !$ModelIDsToShow){ break; }
			$where = $VideoIDsToShow ? sprintf('(video_id in ( %1$s ) OR set_id in ( %2$s )) AND mut_deleted = -1',	join(', ', $VideoIDsToShow), join(', ', $SetIDsToShow)) : null;
			$where = $where ? $where : sprintf('set_id in ( %1$s ) AND mut_deleted = -1',	join(', ', $SetIDsToShow));
			$ToShow = Video::GetVideos($where, null, sprintf("%1\$d, %2\$d", $from, $max_results));
			$Total = count(Video::GetVideos($where));
			break;
	}
	$TotalPages = ceil($Total / $max_results);
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
				foreach($ToShow as $Image)
				{
					/* @var $Image Image */
					$ItemCount++;
					$Results .= sprintf(
						"<div class=\"SetThumbGalItem\">
							<h3 class=\"Hidden\">%4\$s.%5\$s</h3>
							<div class=\"SetThumbImageWrapper\">
								<a href=\"image_view.php?model_id=%6\$d&amp;set_id=%7\$d&amp;image_id=%8\$d\" title=\"%4\$s.%5\$s\">
									<img src=\"download_image.php?image_id=%8\$d&amp;width=225&amp;height=150\" height=\"150\" alt=\"%4\$s.%5\$s\" />
								</a>
							</div>
							<div class=\"SearchThumbDataWrapper\">
								<ul>
									<li>%4\$s.%5\$s</li>
								</ul>
							</div>
						</div>
						%9\$s",
						htmlentities($Image->getSet()->getModel()->GetFullName()),
						htmlentities($Image->getSet()->getPrefix()),
						htmlentities($Image->getSet()->getName()),
						htmlentities($Image->getFileName()),
						htmlentities($Image->getFileExtension()),
						$Image->getSet()->getModel()->getID(),
						$Image->getSet()->getID(),
						$Image->getID(),
						($ItemCount % 3 == 0 ? "<div class=\"Clear\"></div>" : null)
					);
				}
				break;

			case "VIDEO":
				foreach($ToShow as $Video)
				{
					/* @var $Video Video */
					$ItemCount++;
					$Results .= sprintf(
						"<div class=\"SetThumbGalItem\">
							<h3 class=\"Hidden\">%4\$s.%5\$s</h3>
							<div class=\"SetThumbImageWrapper\">
								<a href=\"video_view.php?model_id=%6\$d&amp;set_id=%7\$d&amp;video_id=%8\$d\" title=\"%4\$s.%5\$s\">
									<img src=\"download_image.php?video_id=%8\$d&amp;width=225&amp;height=150\" height=\"150\" alt=\"%4\$s.%5\$s\" />
								</a>
							</div>
							<div class=\"SearchThumbDataWrapper\">
								<ul>
									<li>%4\$s.%5\$s</li>
								</ul>
							</div>
						</div>
						%9\$s",
						htmlentities($Video->getSet()->getModel()->GetFullName()),
						htmlentities($Video->getSet()->getPrefix()),
						htmlentities($Video->getSet()->getName()),
						htmlentities($Video->getFileName()),
						htmlentities($Video->getFileExtension()),
						$Video->getSet()->getModel()->getID(),
						$Video->getSet()->getID(),
						$Video->getID(),
						($ItemCount % 3 == 0 ? "<div class=\"Clear\"></div>" : null)
					);
				}
				break;
		}
	}
}

echo HTMLstuff::HtmlHeader('Tag search', $CurrentUser);

?>

<h2><a href="index.php">Home</a> - Tag search</h2>

<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>" method="get" class="Search">
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
</br>
<label for="txtResults">Results per page</label>
<input type="text" id="txtResults" name="txtResults" class="TagsBox" style="width:50px;" value="<?php echo $max_results; ?>" />


<div class="Separator"></div>

<div class="Clear"></div>
<?php echo $Results ?>
<div class="Clear"></div>

<div class="SearchSummary">
<?php
if($page > 1)
{
echo HTMLStuff::Button('search.php?page=1&hidAction=Search&selectType='.$searchMode.'&q='.$q.'&txtResults='.$max_results, 'First Page', " style=\"float:left\"");
echo HTMLStuff::Button('search.php?page='.($page-1).'&hidAction=Search&selectType='.$searchMode.'&q='.$q.'&txtResults='.$max_results, 'Previous Page', " style=\"float:left\"");
}

if($page < $TotalPages)
{
echo HTMLStuff::Button('search.php?page='.($TotalPages).'&hidAction=Search&selectType='.$searchMode.'&q='.$q.'&txtResults='.$max_results, 'Last Page', " style=\"float:right\"");
echo HTMLStuff::Button('search.php?page='.($page+1).'&hidAction=Search&selectType='.$searchMode.'&q='.$q.'&txtResults='.$max_results, 'Next Page', " style=\"float:right\"");
}
?>
<div class="Clear"></div>

<p>Showing <?php echo $Total ? (($from+1).' to '.(($from+$max_results > $Total) ? $Total : $from+$max_results).' of '.$Total) : '0' ?> result(s) returned</p>
</div>

<?php echo HTMLstuff::Button('index.php'); ?>

</fieldset>
</form>

<?php
echo HTMLstuff::HtmlFooter($CurrentUser);
?>